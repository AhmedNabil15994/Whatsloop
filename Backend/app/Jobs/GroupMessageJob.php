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
use App\Models\ChatMessage;
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
        foreach ($this->contacts as $contact) {
            $result = $this->sendData($contact,(array) $this->messageObj);
            if($result == 1){
                $sent+=1;
            }else{
                $unsent+=1;
            }
        }
        GroupMsg::NotDeleted()->where('id',$this->messageObj->id)->update([
            'sent_count' => $sent,
            'unsent_count' => $unsent,
        ]);
    }

    public function sendData($contact,$messageObj){
        $contact = (object) $contact;
        $sendData['chatId'] = str_replace('+', '', $contact->phone).'@c.us';
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $status = 0;
        if($contact->has_whatsapp != 1){
            $check = $mainWhatsLoopObj->checkPhone(['phone' => str_replace('+', '', $contact->phone)]);
            $check = $check->json();
            if(isset($check['data']) && isset($check['data']['result']) && $check['data']['result'] == 'exists'){
                $status = 1;
                $contactObj->has_whatsapp = 1;
                $contactObj->save();
            }
        }

        $msg = ChatMessage::where('fromMe',1)->where('chatId',$sendData['chatId'])->where('time','>=',now()-1800);

        if($messageObj['message_type'] == 1){
            $sendData['body'] = $this->reformMessage($messageObj['message'],$contact->name,str_replace('+', '', $contact->phone));
            if(!$msg->where('body',$sendData['body'])->first()){
                $result = $mainWhatsLoopObj->sendMessage($sendData);
            }
        }elseif($messageObj['message_type'] == 2){
            $sendData['filename'] = $messageObj['file_name'];
            $sendData['body'] = $messageObj['file'];
            $sendData['caption'] = $this->reformMessage($messageObj['message'],$contact->name,str_replace('+', '', $contact->phone));
            if(!$msg->where('body',$sendData['body'])->first()){
                $result = $mainWhatsLoopObj->sendFile($sendData);
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
            //$sendData['previewBase64'] = base64_encode(file_get_contents($messageObj['url_image']));
            if(!$msg->where('body',$sendData['body'])->first()){
                $result = $mainWhatsLoopObj->sendLink($sendData);
            }
        }elseif($messageObj['message_type'] == 5){
            $sendData['contactId'] = str_replace('+','',$messageObj['whatsapp_no']);
            if(!$msg->where('body',$sendData['contactId'])->first()){
                $result = $mainWhatsLoopObj->sendContact($sendData);
            }
        }
        if($result){
            $result = $result->json();
            $status = 1;
            if(isset($result['status']) && isset($result['status']['status']) && $result['status']['status'] != 1){
                $status = 0;
            }
        }

        $messageId = '';
        if(isset($result['data']) && isset($result['data']['id'])){
            $messageId = $result['data']['id'];
            $lastMessage['status'] = 'APP';
            $lastMessage['id'] = $messageId;
            $lastMessage['chatId'] = $sendData['chatId'];
            ChatMessage::newMessage($lastMessage);
        }

        ContactReport::newStatus('+'.str_replace('@c.us','',$sendData['chatId']),$messageObj['group_id'],$messageObj['id'],$status,$messageId);
        return $status;
    }

    public function reformMessage($text,$contactName,$contactPhone){
        $newText = str_replace("{CUSTOMER_NAME}",$contactName,$text);
        $newText = str_replace("{CUSTOMER_PHONE}",$contactPhone,$newText);
        return $newText;
    }
}
