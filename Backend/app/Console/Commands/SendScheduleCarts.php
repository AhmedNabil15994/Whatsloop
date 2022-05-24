<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Variable;
use App\Models\CartEvents;
use App\Models\User;
use App\Models\UserAddon;

class SendScheduleCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:scheduleCarts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Scheduled Carts Hourly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        $disBotPlus = 0;
        $tenantUser = User::first();
        $disabled = UserAddon::getDeactivated($tenantUser->id);
        if(in_array(10,$disabled)){
            $disBotPlus = 1;
        }
        $this->beingWorkWithData(1,$disBotPlus);
        $this->beingWorkWithData(2,$disBotPlus);
    }

    public function beingWorkWithData($type,$disBotPlus){
        $data = [];
        if($type == 1){
            $name = 'ZID';
        }else{
            $name = 'SALLA';
        }
        $events = CartEvents::NotDeleted()->where('type',$type)->where('status',1)->orderBy('id','asc')->get(['time','id']);
        $tableName = strtolower($name).'_abandonedCarts';
        $carts =  \DB::table($tableName)->orderBy('created_at','ASC')->get();

        foreach ($carts as $key => $value) {
            foreach ($events as $eventKey => $eventValue) {
                $newData = [
                    'id' => $value->id,
                    'created_at' => date('Y-m-d H',strtotime($value->created_at)),
                    'created_at_limit' =>  date('Y-m-d H',strtotime('+'.$eventValue->time.' hours',strtotime($value->created_at))),
                ];
                $itemData = array_merge((array)$value,$newData);
                $data[$eventValue->id][]  = array_unique($itemData);
            }
        }
      
        foreach($data as $oneKey => $oneItem){
            $eventObj = CartEvents::find($oneKey);
            if($eventObj){
                $this->sendMessage($oneItem,$eventObj,$type,$disBotPlus);
            }
        }
    }

    public function sendMessage($dataTable,$eventObj,$type,$disBotPlus){
        $message = '';
        $hasFile = 0;
        $hasButtons = 0;
        if($eventObj->message_type == 1){
            $message = $eventObj->message;
        }else if($eventObj->message_type == 2){
            $eventObj = CartEvents::getData($eventObj);
            $hasFile = 1;
            $sendFileData['filename'] = $eventObj->file_name;
            $sendFileData['body'] = $eventObj->file;
            $file_type = \ImagesHelper::checkFileExtension($eventObj->file_name);
            if($file_type == 'photo'){
                $sendFileData['caption'] = $eventObj->caption;
            }
        }else if($eventObj->message_type == 3){
            $eventObj = CartEvents::getData($eventObj);
            $hasButtons = 1;
            if($eventObj->bot_plus_id != null && isset($eventObj->bot_plus->buttonsData) && !empty($eventObj->bot_plus->buttonsData) && !$disBotPlus){
                $buttons = '';
                $botObj = $eventObj->bot_plus;
                foreach($botObj->buttonsData as $key => $oneItem){
                    $buttons.= $oneItem['text'].( $key == $botObj->buttons -1 ? '' : ',');
                }
                $buttonsData['body'] = $botObj->body;
                $buttonsData['title'] = $botObj->title;
                $buttonsData['footer'] = $botObj->footer;
                $buttonsData['buttons'] = $buttons;
            }
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        foreach($dataTable as $oneCart){
            $oneCart = (object) $oneCart;
            $sendMsgs = $oneCart->created_at_limit == date('Y-m-d H') ? 1 : 0;

            if($type == 2){
                if(isset($oneCart->customer) && !empty($oneCart->customer)){
                    $customerData = unserialize($oneCart->customer);
                    $total = unserialize($oneCart->total);
                    $chatId = str_replace('+', '', $customerData['mobile'].'@c.us');

                    if($message != ''){
                        $sendData['body'] = $this->reformMessage($message,$customerData['name'],$oneCart->id,$total['amount'].' '.$total['currency'],$oneCart->checkout_url);
                        $sendData['chatId'] = $chatId;
                        if($sendMsgs){
                            $result = $mainWhatsLoopObj->sendMessage($sendData);
                        }
                    }
                    
                    if($hasFile){
                        if(isset($sendFileData['caption']) && !empty($sendFileData['caption'])){
                            $sendFileData['caption'] = $this->reformMessage($sendFileData['caption'],$customerData['name'],$oneCart->id,$total['amount'].' '.$total['currency'],$oneCart->checkout_url);
                        }
                        $sendFileData['chatId'] = $chatId;
                        if($sendMsgs){
                            $result = $mainWhatsLoopObj->sendFile($sendFileData);
                        }
                    }

                    if($hasButtons){
                        $buttonsData['title'] = $this->reformMessage($buttonsData['title'],$customerData['name'],$oneCart->id,$total['amount'].' '.$total['currency'],$oneCart->checkout_url);
                        $buttonsData['body'] = $this->reformMessage($buttonsData['body'],$customerData['name'],$oneCart->id,$total['amount'].' '.$total['currency'],$oneCart->checkout_url);
                        $buttonsData['footer'] = $this->reformMessage($buttonsData['footer'],$customerData['name'],$oneCart->id,$total['amount'].' '.$total['currency'],$oneCart->checkout_url);
                        $buttonsData['chatId'] = $chatId;
                        if($sendMsgs){
                            $result = $mainWhatsLoopObj->sendButtons($buttonsData);
                        }
                    }
                    if($sendMsgs){
                        $result = $result->json();
                        if(isset($result['data']) && isset($result['data']['id'])){
                            $dataObj = \DB::table('salla_abandonedCarts')->where('id',$oneCart->id)->first();
                            $oldTotal = unserialize($dataObj->total);
                            $oldTotal['sent_count'] = isset($oldTotal['sent_count']) ? $oldTotal['sent_count']+1 : 1;
                            \DB::table('salla_abandonedCarts')->where('id',$oneCart->id)->update([
                                'total' => serialize($oldTotal),
                            ]);
                        }
                    }
                }
            }else{
                if(isset($oneCart->customer_id) && $oneCart->customer_id != null){
                    $chatId = str_replace('+', '', $oneCart->customer_mobile.'@c.us');

                    if($message != ''){
                        $sendData['chatId'] = $chatId;
                        $sendData['body'] = $this->reformMessage($message,$oneCart->customer_name,$oneCart->cart_id,$oneCart->cart_total_string,'https://web.zid.sa/abandoned-cart/'.$oneCart->store_id);
                        if($sendMsgs){
                            $result = $mainWhatsLoopObj->sendMessage($sendData);
                        }
                    }

                    if($hasFile){
                        $sendFileData['chatId'] = $chatId;
                        if(isset($sendFileData['caption']) && !empty($sendFileData['caption'])){
                            $sendFileData['caption'] = $this->reformMessage($sendFileData['caption'],$oneCart->customer_name,$oneCart->cart_id,$oneCart->cart_total_string,'https://web.zid.sa/abandoned-cart/'.$oneCart->store_id);
                        }
                        if($sendMsgs){
                            $result = $mainWhatsLoopObj->sendFile($sendFileData);
                        }
                    }

                    if($hasButtons){
                        $buttonsData['title'] = $this->reformMessage($buttonsData['title'],$oneCart->customer_name,$oneCart->cart_id,$oneCart->cart_total_string,'https://web.zid.sa/abandoned-cart/'.$oneCart->store_id);
                        $buttonsData['body'] = $this->reformMessage($buttonsData['body'],$oneCart->customer_name,$oneCart->cart_id,$oneCart->cart_total_string,'https://web.zid.sa/abandoned-cart/'.$oneCart->store_id);
                        $buttonsData['footer'] = $this->reformMessage($buttonsData['footer'],$oneCart->customer_name,$oneCart->cart_id,$oneCart->cart_total_string,'https://web.zid.sa/abandoned-cart/'.$oneCart->store_id);
                        $buttonsData['chatId'] = $chatId;
                        if($sendMsgs){
                            $result = $mainWhatsLoopObj->sendButtons($buttonsData);
                        }
                    }
                    
                    if($sendMsgs){
                        $result = $result->json();
                        if(isset($result['data']) && isset($result['data']['id'])){
                            $dataObj = \DB::table('zid_abandonedCarts')->where('cart_id',$oneCart->cart_id)->first();
                            \DB::table('zid_abandonedCarts')->where('cart_id',$oneCart->cart_id)->update([
                                'reminders_count' => $dataObj->reminders_count + 1, 
                            ]);
                        }
                    }
                }
            }            
        }
    }

    public function reformMessage($text,$name,$order_id,$total,$url){
        $text = str_replace("{CUSTOMERNAME}",$name,$text);
        $text = str_replace("{ORDERID}",$order_id,$text);
        $text = str_replace("{ORDERTOTAL}",$total,$text);
        $text = str_replace("{ORDERURL}",$url,$text);
        return $text;
    }
}
