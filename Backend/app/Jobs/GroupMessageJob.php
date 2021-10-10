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
            $result = $this->sendData(str_replace('+', '', $contact->phone),(array) $this->messageObj);
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
        $sendData['chatId'] = $contact.'@c.us';
        $mainWhatsLoopObj = new \MainWhatsLoop();
        
        if($messageObj['message_type'] == 1){
            $sendData['body'] = $messageObj['message'];
            $result = $mainWhatsLoopObj->sendMessage($sendData);
        }elseif($messageObj['message_type'] == 2){
            $sendData['filename'] = $messageObj['file_name'];
            $sendData['body'] = $messageObj['file'];
            $sendData['caption'] = $messageObj['reply'];
            $result = $mainWhatsLoopObj->sendFile($sendData);
        }elseif($messageObj['message_type'] == 3){
            $sendData['audio'] = $messageObj['file'];
            $result = $mainWhatsLoopObj->sendPTT($sendData);
        }elseif($messageObj['message_type'] == 4){
            $sendData['body'] = $messageObj['https_url'];
            $sendData['title'] = $messageObj['url_title'];
            $sendData['description'] = $messageObj['url_desc'];
            $sendData['previewBase64'] = base64_encode(file_get_contents($messageObj['photo']));
            $result = $mainWhatsLoopObj->sendFile($sendData);
        }elseif($messageObj['message_type'] == 5){
            $sendData['contactId'] = $messageObj['whatsapp_no'];
            $result = $mainWhatsLoopObj->sendContact($sendData);
        }

        $status = 1;
        if($result['status']['status'] != 1){
            $status = 0;
        }

        $messageId = '';
        if(isset($result['data']) && isset($result['data']['id'])){
            $messageId = $result['data']['id'];
            $lastMessage['status'] = 'APP';
            $lastMessage['id'] = $messageId;
            $lastMessage['chatId'] = $sendData['chatId'];
            ChatMessage::newMessage($lastMessage);
        }

        ContactReport::newStatus('+'.$contact,$messageObj['group_id'],$messageObj['id'],$status,$messageId);
        return $status;
    }
}
