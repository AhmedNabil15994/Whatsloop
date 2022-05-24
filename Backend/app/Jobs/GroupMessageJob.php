<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\GroupMsg;
use App\Models\ContactReport;
use App\Models\Contact;
use App\Models\ChatMessage;
use App\Models\BotPlus;
use App\Models\UserAddon;
use App\Models\User;

// implements ShouldQueue
class GroupMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $contacts;
    public $messageObj;
    
    public function __construct($contacts,$messageObj)
    {
        $this->contacts = $contacts;
        $this->messageObj = $messageObj;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $unsent = $this->messageObj->unsent_msgs;
        $sent = $this->messageObj->sent_msgs;

        $mainWhatsLoopObj = new \MainWhatsLoop();
        
        $botObj = null;
        $messageObj = GroupMsg::NotDeleted()->where('id',$this->messageObj->id)->first();
        if($messageObj->bot_plus_id != null){
            $botObjs = BotPlus::find($messageObj->bot_plus_id);
            $botObj = BotPlus::getData($botObjs);
        }

        $disBotPlus = 0;
        $tenantUser = User::first();
        $disabled = UserAddon::getDeactivated($tenantUser->id);
        if(in_array(10,$disabled)){
            $disBotPlus = 1;
        }

        foreach ($this->contacts as $contact) {
            $result = $this->sendData($contact,(array) $this->messageObj,$botObj,$disBotPlus);
            if($result == 1){
                $sent+=1;
            }else{
                $unsent+=1;
            }
            sleep(3);
        }

        

        return $messageObj->update([
            'sent_count' => $sent,
            'unsent_count' => $unsent,
        ]);
    }

    public function sendData($contact,$messageObj,$botObj=null,$disBotPlus=0){
        $contact = (object) $contact;
        $sendData['chatId'] = str_replace('+', '', $contact->phone).'@c.us';
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $status = 0;
        
        $msg = ChatMessage::where('fromMe',1)->where('chatId',$sendData['chatId'])->where('time','>=',strtotime(date('Y-m-d H:i:s'))-1800);

        if($messageObj['message_type'] == 1){
            $sendData['body'] = $this->reformMessage($messageObj['message'],$contact->name,str_replace('+', '', $contact->phone));
            if(!$msg->where('body',$sendData['body'])->first()){
                $result = $mainWhatsLoopObj->sendMessage($sendData);
            }
        }elseif($messageObj['message_type'] == 2){
            $sendData['filename'] = $messageObj['file_name'];
            $sendData['body'] = $messageObj['file'];
            $sendData['caption'] = $this->reformMessage($messageObj['message'],$contact->name,str_replace('+', '', $contact->phone));
            if($messageObj['file_name'] == null || $messageObj['file'] == null){
                if(!$msg->where('body',$sendData['caption'])->first()){
                    $result = $mainWhatsLoopObj->sendMessage([
                        'chatId' => $sendData['chatId'],
                        'body' => $sendData['caption'],
                    ]);
                }
            }else{
                if(!$msg->where('body',$sendData['body'])->first()){
                    $result = $mainWhatsLoopObj->sendFile($sendData);
                }
            }
        }elseif($messageObj['message_type'] == 3){
            $sendData['audio'] = $messageObj['file'];
            if(!$msg->where('body',$sendData['audio'])->first()){
                $result = $mainWhatsLoopObj->sendPTT($sendData);
            }
        }elseif($messageObj['message_type'] == 4){
            $sendData['body'] = $messageObj['https_url'];
            $sendData['title'] = $messageObj['url_title'];
            $sendData['description'] = $this->reformMessage($messageObj['url_desc'],$contact->name,str_replace('+', '', $contact->phone));
            $messageObj['url_image'] = str_replace('http://final.whatsloop.localhost','https://e1b1-154-182-149-47.ngrok.io',$messageObj['url_image']);
            $imageData = file_get_contents($messageObj['url_image']);
            $type = pathinfo(
                parse_url($messageObj['url_image'], PHP_URL_PATH), 
                PATHINFO_EXTENSION
            );
            if(strlen(base64_encode($imageData)) > 20000){
                $dets = substr('data:image/' . $type . ';base64,' .base64_encode($imageData),0,20000);
            }else{
                $dets = 'data:image/' . $type . ';base64,' .base64_encode($imageData);
            }
            if(is_array($dets)){
                $sendData['previewBase64'] =  $dets[0];
            }else{
                $sendData['previewBase64'] = $dets;
            }
            if(!$msg->where('body',$sendData['body'])->first()){
                $result = $mainWhatsLoopObj->sendLink($sendData);
            }
        }elseif($messageObj['message_type'] == 5){
            $sendData['contactId'] = str_replace('+','',$messageObj['whatsapp_no']);
            if(!$msg->where('body',$sendData['contactId'])->first()){
                $result = $mainWhatsLoopObj->sendContact($sendData);
            }
        }elseif($messageObj['message_type'] == 6){

            if(isset($botObj->buttonsData) && !empty($botObj->buttonsData) && !$disBotPlus){
                $buttons = '';
                foreach($botObj->buttonsData as $key => $oneItem){
                    $buttons.= $oneItem['text'].( $key == $botObj->buttons -1 ? '' : ',');
                }
                $sendData['body'] = $this->reformMessage($botObj->body,$contact->name,str_replace('+', '', $contact->phone));
                $sendData['title'] = $this->reformMessage($botObj->title,$contact->name,str_replace('+', '', $contact->phone));
                $sendData['footer'] = $this->reformMessage($botObj->footer,$contact->name,str_replace('+', '', $contact->phone));
                $sendData['buttons'] = $buttons;
                $sendData['chatId'] = str_replace('@c.us','',$sendData['chatId']);
                $result = $mainWhatsLoopObj->sendButtons($sendData);
            }
        }
        if(isset($result) && $result){
            $result = $result->json();
            if(isset($result['status']) && isset($result['status']['status']) && $result['status']['status'] != 1){
                $status = 0;
            }
        }

        $messageId = '';
        if(isset($result) && $result && isset($result['data']) && isset($result['data']['id'])){
            $messageId = $result['data']['id'];
            $lastMessage['status'] = 'APP';
            $lastMessage['id'] = $messageId;
            $lastMessage['chatId'] = $sendData['chatId'];
            $status = 1;
            ChatMessage::newMessage($lastMessage);
        }

        ContactReport::newStatus(str_replace('@c.us','',$sendData['chatId']),$messageObj['group_id'],$messageObj['id'],$status,$messageId);
        return $status;
    }

    public function reformMessage($text,$contactName,$contactPhone){
        $newText = str_replace("{CUSTOMER_NAME}",$contactName,$text);
        $newText = str_replace("{CUSTOMER_PHONE}",$contactPhone,$newText);
        return $newText;
    }
}
