<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Models\ChatEmpLog;
use App\Events\SentMessage;

class NewDialogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $contacts;
    public $messageObj;
    
    public function __construct($chatId,$inputs,$file,$domain,$senderStatus)
    {
        $this->chatId = $chatId;
        $this->inputs = $inputs;
        $this->file   = $file;
        $this->domain = $domain;  
        $this->senderStatus = $senderStatus;  
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        foreach($this->chatId as $chatId){
            $sendData['chatId'] = $chatId;
            $caption = '';
            $mainWhatsLoopObj = new \MainWhatsLoop();

            if($this->inputs['type'] == 1){
                if(!isset($this->inputs['message']) || empty($this->inputs['message']) ){
                    return \TraitsFunc::ErrorMessage("Message Field Is Required");
                }

                $message_type = 'text';
                $sendData['body'] = $this->inputs['message'];
                $bodyData = $this->inputs['message'];
                $whats_message_type = 'chat';
                $result = $mainWhatsLoopObj->sendMessage($sendData);
            }elseif($this->inputs['type'] == 2){
                if ($this->file != null) {
                    $image = $this->file;
                    $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                    if($image == false || $fileName == false){
                        return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                    }            
                    $bodyData = config("app.BASE_URL").'/public/uploads/chats/'.$fileName;
                    $message_type = \ImagesHelper::checkExtensionType(substr($bodyData, strrpos($bodyData, '.') + 1));
                    $sendData['filename'] = $fileName;
                    $sendData['body'] = $bodyData;
                    if($message_type == 'photo'){
                        if(isset($this->inputs['caption']) && !empty($this->inputs['caption']) ){
                            $sendData['caption'] = $this->inputs['caption'];
                            $caption = $this->inputs['caption'];
                        }
                    }
                    $whats_message_type = $message_type == 'photo' ? 'image' : 'document' ;
                    $result = $mainWhatsLoopObj->sendFile($sendData);
                }
            }elseif($this->inputs['type'] == 3){
                if ($this->file != null) {
                    $image = $this->file;
                    $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                    if($image == false || $fileName == false){
                        return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                    }            
                    $bodyData = config("app.BASE_URL").'/public/uploads/chats/'.$fileName;
                    $message_type = "video";
                    $sendData['filename'] = $fileName;
                    $sendData['body'] = $bodyData;
                    $whats_message_type = 'video';
                    $result = $mainWhatsLoopObj->sendFile($sendData);
                }
            }elseif($this->inputs['type'] == 4){
                if ($this->file != null) {
                    $image = $this->file;
                    $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                    if($image == false || $fileName == false){
                        return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                    }            
                    $bodyData = config("app.BASE_URL").'/public/uploads/chats/'.$fileName;
                    $message_type = "sound";
                    $whats_message_type = 'ppt';
                    $sendData['audio'] = $bodyData;
                    $result = $mainWhatsLoopObj->sendFile($sendData);
                }
                $result = $mainWhatsLoopObj->sendPTT($sendData);
            }elseif($this->inputs['type'] == 5){
                if(!isset($this->inputs['contact']) || empty($this->inputs['contact']) ){
                    return \TraitsFunc::ErrorMessage("Contact Field Is Required");
                }

                $message_type = 'contact';
                $whats_message_type = 'contact';
                $sendData['contactId'] = $this->inputs['contact'];
                $bodyData = $this->inputs['contact'];
                $result = $mainWhatsLoopObj->sendContact($sendData);
            }elseif($this->inputs['type'] == 6){
                if(!isset($this->inputs['address']) || empty($this->inputs['address']) ){
                    return \TraitsFunc::ErrorMessage("Address Field Is Required");
                }

                if(!isset($this->inputs['lat']) || empty($this->inputs['lat']) ){
                    return \TraitsFunc::ErrorMessage("Latitude Field Is Required");
                }

                if(!isset($this->inputs['lng']) || empty($this->inputs['lng']) ){
                    return \TraitsFunc::ErrorMessage("Longitude Field Is Required");
                }

                $message_type = 'location';
                $whats_message_type = 'location';
                $sendData['lat'] = $this->inputs['lat'];
                $sendData['lng'] = $this->inputs['lng'];
                $sendData['address'] = $this->inputs['address'];
                $bodyData = $this->inputs['address'];
                $result = $mainWhatsLoopObj->sendLocation($sendData);
            }elseif($this->inputs['type'] == 7){
                if(!isset($this->inputs['link']) || empty($this->inputs['link']) ){
                    return \TraitsFunc::ErrorMessage("Link Field Is Required");
                }

                if(!isset($this->inputs['link_title']) || empty($this->inputs['link_title']) ){
                    return \TraitsFunc::ErrorMessage("Link Title Field Is Required");
                }

                if ($this->file != null) {
                    $image = $this->file;
                    $fileName = \ImagesHelper::uploadFileFromRequest('chats', $image);
                    if($image == false || $fileName == false){
                        return \TraitsFunc::ErrorMessage("Upload Files Failed !!", 400);
                    }            
                    $fullUrl = config("app.BASE_URL").'/public/uploads/chats/'.$fileName;
                }

                $message_type = 'link';
                $whats_message_type = 'link';
                $sendData['body'] = $this->inputs['link'];
                $sendData['title'] = $this->inputs['link_title'];
                $sendData['description'] = $this->inputs['link_description'];
                $bodyData = $sendData['body'];
                if(isset($fileName) && $fileName != false){
                    $sendData['previewBase64'] = base64_encode(file_get_contents($fullUrl));
                }
                $result = $mainWhatsLoopObj->sendLink($sendData);
            }

            $result = $result->json();
            if(isset($result['data']) && isset($result['data']['id'])){
                $checkMessageObj = ChatMessage::where('chatId',$sendData['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->first();
                $messageId = $result['data']['id'];
                $lastMessage['status'] = $this->senderStatus;
                $lastMessage['id'] = $messageId;
                $lastMessage['fromMe'] = 1;
                $lastMessage['chatId'] = $sendData['chatId'];
                $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
                $lastMessage['body'] = $bodyData;
                $lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
                $lastMessage['caption'] = $caption;
                $lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
                $lastMessage['message_type'] = $message_type;
                $lastMessage['sending_status'] = 1;
                $lastMessage['type'] = $whats_message_type;
                $messageObj = ChatMessage::newMessage($lastMessage);

                $dialogObj = ChatDialog::getOne($sendData['chatId']);
                if(!$dialogObj){
                    $dialogObj = new ChatDialog;
                    $dialogObj->id = $sendData['chatId'];
                    $dialogObj->name = $sendData['chatId'];
                    $dialogObj->image = '';
                    $dialogObj->metadata = '';
                    $dialogObj->is_pinned = 0;
                    $dialogObj->is_read = 0;
                    $dialogObj->modsArr = '';
                    $dialogObj->last_time = $lastMessage['time'];
                    $dialogObj->save();
                }
                $dialogObj =ChatDialog::getData($dialogObj);
                broadcast(new SentMessage($this->domain , $dialogObj ));
            
                $is_admin = 0;
                $user_id = 4; 
                if(!$is_admin){
                    $dialogObj = ChatDialog::getData(ChatDialog::getOne($sendData['chatId']), true);
                    if(in_array($user_id, $dialogObj->modsArr)){
                        ChatEmpLog::newLog($sendData['chatId'],3);
                    }
                }
            }
        }
    }
}
