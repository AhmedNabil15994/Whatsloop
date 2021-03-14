<?php
namespace App\Handler;
use App\Models\Bot;
use App\Models\ChatSession;
use App\Models\ContactReport;
use \Spatie\WebhookClient\ProcessWebhookJob;
use Http;
use Session;

class MessagesWebhook extends ProcessWebhookJob{
	public function handle(){
	    $data = json_decode($this->webhookCall, true);
	    $mainData = $data['payload'];
	    $messages = @$mainData['messages'];
	    $actions = @$mainData['ack'];

	    if(!empty($messages)){
	    	foreach ($messages as $message) {
	    		$message = (array) $message;
	    		$sender = $message['chatId'];
	    		$senderMessage = $message['body'];

	    		if($message['fromMe'] == false){
	    			if(in_array(strtolower($senderMessage), ['english','عربي','#','خروج','exit'])){
	    				if(strtolower($senderMessage) == 'english'){
		    				$newLangPref = 1;
		    			}else if($senderMessage == 'عربي'){
		    				$newLangPref = 0;
		    			}
		    			$senderMessage = '#';
	    			}

	    			$lang = ChatSession::getOne($sender);
	    			if($lang == null){
	    				$chatSessionObj = new ChatSession;
		    			$chatSessionObj->chatId = $sender;
		    			$chatSessionObj->langId = isset($newLangPref) ? $newLangPref : 0;
		    			$chatSessionObj->save();
	    				$langPref = $chatSessionObj->langId;
	    			}else{
	    				if(isset($newLangPref)){
	    					$lang->langId = $newLangPref;
	    					$lang->save();
	    				}
	    				$langPref = $lang->langId;
	    			}

	    			$botObj = Bot::NotDeleted()->where([
	    				['status',1],
	    				['lang',$langPref],
	    				['message_type',1],
	    				['message',$senderMessage],
	    			])->orWhere([
	    				['status',1],
	    				['lang',$langPref],
	    				['message_type',2],
	    				['message','LIKE','%'.$senderMessage.'%'],
	    			])->first();

	    			$sendData['chatId'] = $sender;
	    			$mainWhatsLoopObj = new \MainWhatsLoop();

	    			if($botObj){
	    				$botObj = Bot::getData($botObj);
	    				$reply = $botObj->reply;
	    				$myMessage = $reply;

	    				if($botObj->reply_type == 1){
		    				$sendData['body'] = $myMessage;
			    			$result = $mainWhatsLoopObj->sendMessage($sendData);
		    			}elseif($botObj->reply_type == 2){
		    				$sendData['filename'] = $botObj->file_name;
		    				$sendData['body'] = $botObj->file;
		    				$sendData['caption'] = $botObj->reply;
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 3){
		    				$sendData['filename'] = $botObj->file_name;
		    				$sendData['body'] = $botObj->file;
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 4){
		    				$sendData['audio'] = $botObj->file;
			    			$result = $mainWhatsLoopObj->sendPTT($sendData);
		    			}elseif($botObj->reply_type == 5){
		    				$sendData['body'] = $botObj->https_url;
	        				$sendData['title'] = $botObj->url_title;
	        				$sendData['description'] = $botObj->url_desc;
	        				$sendData['previewBase64'] = base64_encode(file_get_contents($botObj->photo));
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 6){
		    				$sendData['contactId'] = $botObj->whatsapp_no;
			    			$result = $mainWhatsLoopObj->sendContact($sendData);
		    			}elseif($botObj->reply_type == 7){
		    				$sendData['lat'] = $botObj->lat;
		    				$sendData['lng'] = $botObj->lng;
		    				$sendData['address'] = $botObj->address;
			    			$result = $mainWhatsLoopObj->sendLocation($sendData);
		    			}elseif($botObj->reply_type == 8){
		    				$sendData['body'] = $botObj->webhook_url;
			    			$result = $mainWhatsLoopObj->sendContact($sendData);
		    			}

	    			}else{
	    				if($langPref == 0){
	    					$notFoundMessage = 'اسف لم استطع فهمك ! :(';
	    				}else{
	    					$notFoundMessage = "Sorry I Couldn't Reach You ! :(";
	    				}
	    				$myMessage = $notFoundMessage;
	    				$sendData['body'] = $myMessage;
		    			$result = $mainWhatsLoopObj->sendMessage($sendData);
	    			}

	    			if($result['status']['status'] != 1){
			            return \TraitsFunc::ErrorMessage("Server Error", 400);
			        }

			        $statusObj['data'] = $result->json();
			        $statusObj['status'] = \TraitsFunc::SuccessResponse();
			        return \Response::json((object) $statusObj); 
	    		}	

	    	}
	    }

	    if(!empty($actions)){
	    	foreach ($actions as $action) {
	    		$action = (array) $action;
	    		$sender = $action['chatId'];
	    		$messageId = $action['id'];
	    		if($action['status'] == 'delivered'){
	    			$statusInt = 2;
	    			$contactObj = ContactReport::where('contact','+'.str_replace('@c.us', '', $sender))->where('message_id',$messageId)->update(['status' => $statusInt]);
	    		}elseif ($action['status'] == 'viewed') {
	    			$statusInt = 3;
	    			$contactObj = ContactReport::where('contact','+'.str_replace('@c.us', '', $sender))->where('message_id',$messageId)->update(['status' => $statusInt]);
	    		}

		    	$statusObj['status'] = \TraitsFunc::SuccessResponse();
			    return \Response::json((object) $statusObj); 
	    	}
	    }
	}
}