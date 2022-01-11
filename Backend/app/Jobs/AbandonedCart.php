<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AbandonedCart implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $module; // 1 => Salla , 2 => Zid
    public $data;
    
    public function __construct($module,$data)
    {   
        $this->module = $module;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $message = $this->data['message'];
        $clientsData = $this->data['clientsData'];
        $mainWhatsLoopObj = new \MainWhatsLoop();
        foreach($clientsData as $oneCart){
            if(isset($oneCart['name']) && !empty($oneCart['name'])){
                $sendData['body'] = $this->reformMessage($message,$oneCart);
                $sendData['chatId'] = str_replace('+', '', $oneCart['mobile']).'@c.us';
                $result = $mainWhatsLoopObj->sendMessage($sendData);
                $result = $result->json();
                if(isset($result['data']) && isset($result['data']['id'])){
                    if($this->module == 1){
                        $dataObj = \DB::table('salla_abandonedCarts')->where('id',$oneCart['order_id'])->first();
                        $oldTotal = unserialize($dataObj->total);
                        $oldTotal['sent_count'] = isset($oldTotal['sent_count']) ? $oldTotal['sent_count']+1 : 1;
                        \DB::table('salla_abandonedCarts')->where('id',$oneCart['order_id'])->update([
                            'total' => serialize($oldTotal),
                        ]);
                    }elseif($this->module == 2){
                        $dataObj = \DB::table('zid_abandonedCarts')->where('cart_id',$oneCart['order_id'])->first();
                        \DB::table('zid_abandonedCarts')->where('cart_id',$oneCart['order_id'])->update([
                            'reminders_count' => $dataObj->reminders_count + 1, 
                        ]);
                    }
                }
            }
        }   
    }

    public function reformMessage($text,$customerData){
        $text = str_replace("{CUSTOMERNAME}",$customerData['name'],$text);
        $text = str_replace("{ORDERID}",$customerData['order_id'],$text);
        $text = str_replace("{ORDERTOTAL}",$customerData['total'],$text);
        $text = str_replace("{ORDERURL}",$customerData['url'],$text);
        return $text;
    }
}
