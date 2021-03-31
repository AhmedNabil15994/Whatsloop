<?php
namespace App\Handler;
use App\Models\Bot;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\ContactReport;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Events\IncomingMessage;
use App\Events\BotMessage;
use App\Events\MessageStatus;
use \Spatie\WebhookClient\ProcessWebhookJob;
use Http;
use Session;

class MessagesWebhook2 extends ProcessWebhookJob{
	public function handle(){
	    $data = json_decode($this->webhookCall, true);
	    $mainData = $data['payload'];
	    $messages = @$mainData['messages'];
	    $actions = @$mainData['ack'];
		$tenantUser = User::first();
		$tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$tenantUser->global_id)->first();
		$userObj = \DB::connection('main')->table('domains')->where('tenant_id',$tenantObj->tenant_id)->first();

	    $mainWhatsLoopObj = new \MainWhatsLoop();

	    // If New Message Sent
	    if(!empty($messages)){
	    	foreach ($messages as $message) {
	    		$message = (array) $message;
	    		$sender = $message['chatId'];
	    		$senderMessage = $message['body'];
	    		if($message['fromMe'] == false){

					// Fire Incoming Message Event For Web Application
					$this->handleMessages($userObj->domain,$message);

					// Check User Message
	    			if(in_array(strtolower($senderMessage), ['english','عربي','#','خروج','exit'])){
	    				if(strtolower($senderMessage) == 'english'){
		    				$newLangPref = 1;
		    			}else if($senderMessage == 'عربي'){
		    				$newLangPref = 0;
		    			}
		    			$senderMessage = '#';
	    			}

	    			// Determine User Language Session
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

	    			// Find Out Bot Object Based on incoming message
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

	    			if($botObj){
	    				$botObj = Bot::getData($botObj);
	    				$reply = $botObj->reply;
	    				$myMessage = $reply;
	    				$message_type = '';
	    				$whats_message_type = '';
	    				if($botObj->reply_type == 1){
	    					$message_type = 'text';
	    					$whats_message_type = 'chat';
		    				$sendData['body'] = $myMessage;
			    			$result = $mainWhatsLoopObj->sendMessage($sendData);
		    			}elseif($botObj->reply_type == 2){
		    				$message_type = \ImagesHelper::checkExtensionType(substr($botObj->file_name, strrpos($botObj->file_name, '.') + 1));
	    					$whats_message_type = $message_type == 'photo' ? 'image' : 'document' ;
		    				$sendData['filename'] = $botObj->file_name;
		    				$sendData['body'] = $botObj->file;
		    				$sendData['caption'] = $botObj->reply;
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 3){
	    					$message_type = 'video';
	    					$whats_message_type = 'video';
		    				$sendData['filename'] = $botObj->file_name;
		    				$sendData['body'] = $botObj->file;
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 4){
	    					$message_type = 'sound';
	    					$whats_message_type = 'ppt';
		    				$sendData['audio'] = $botObj->file;
			    			$result = $mainWhatsLoopObj->sendPTT($sendData);
		    			}elseif($botObj->reply_type == 5){
	    					$message_type = 'link';
		    				$sendData['body'] = $botObj->https_url;
	        				$sendData['title'] = $botObj->url_title;
	        				$sendData['description'] = $botObj->url_desc;
	        				$sendData['previewBase64'] = base64_encode(file_get_contents($botObj->photo));
			    			$result = $mainWhatsLoopObj->sendLink($sendData);
		    			}elseif($botObj->reply_type == 6){
	    					$message_type = 'contact';
	    					$whats_message_type = 'contact';
		    				$sendData['contactId'] = $botObj->whatsapp_no;
			    			$result = $mainWhatsLoopObj->sendContact($sendData);
		    			}elseif($botObj->reply_type == 7){
	    					$message_type = 'location';
	    					$whats_message_type = 'location';
		    				$sendData['lat'] = $botObj->lat;
		    				$sendData['lng'] = $botObj->lng;
		    				$sendData['address'] = $botObj->address;
			    			$result = $mainWhatsLoopObj->sendLocation($sendData);
		    			}elseif($botObj->reply_type == 8){
	    					$message_type = 'webhook';
	    					$whats_message_type = 'webhook';
		    				$sendData['body'] = $botObj->webhook_url;
			    			$result = $mainWhatsLoopObj->sendContact($sendData);
		    			}

				        if(isset($result['data']) && isset($result['data']['id'])){
				            $messageId = $result['data']['id'];
				            $lastMessage['status'] = 'BOT';
				            $lastMessage['id'] = $messageId;
				            $lastMessage['chatId'] = $sender;
				            $lastMessage['fromMe'] = 1;
				            $lastMessage['message_type'] = $message_type;
				            $lastMessage['type'] = $whats_message_type;
				            $lastMessage['time'] = time();
				            $lastMessage['sending_status'] = 1;
				            $messageObj = ChatMessage::getData(ChatMessage::newMessage($lastMessage));
	    					// $dialogObj = ChatDialog::getData(ChatDialog::getOne($sender)); 
				            // $dialogObj->lastMessage->bot_details = $botObj;
	    					// Fire Bot Message Event For Web Application
				    		// broadcast(new BotMessage($userObj->domain , $dialogObj));
				        }
	    			}
	    			// else{
	    			// 	if($langPref == 0){
	    			// 		$notFoundMessage = 'اسف لم استطع فهمك ! :(';
	    			// 	}else{
	    			// 		$notFoundMessage = "Sorry I Couldn't Reach You ! :(";
	    			// 	}
	    			// 	$myMessage = $notFoundMessage;
	    			// 	$sendData['body'] = $myMessage;
		    		// 	$result = $mainWhatsLoopObj->sendMessage($sendData);
	    			// }
	    		}else{
	    			$messageObj = ChatMessage::getOne($message['id']);
	    			if($messageObj->status == 'BOT'){
						$this->handleMessages($userObj->domain,$message);
	    			}
	    		}

	    	}
	    }

	    // If Chat Status Changed
	    if(!empty($actions)){
	    	$this->handleUpdates($userObj->domain,$actions);
	    }
	}

	public function handleUpdates($domain,$actions){
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
    		}else{
    			$statusInt = 1;
    		}

    		ChatMessage::where('id',$messageId)->update(['sending_status'=>$statusInt]);
			broadcast(new MessageStatus($domain, $sender, $messageId , $statusInt ));
    	}
	}

	public function handleMessages($domain,$message){
	    if($message['fromMe'] == 0){
			if(strpos($message['body'], '://') !== false){
				$message['message_type'] = \ImagesHelper::checkExtensionType(substr($message['body'], strrpos($message['body'], '.') + 1));
				$fileName = '/uploads/chats/'.substr($message['body'], strrpos($message['body'], '/' )+1);
				$destinationPath = public_path() . $fileName;
				$succ = file_put_contents($destinationPath, file_get_contents($message['body']));
				$message['body'] = config("app.BASE_URL").$fileName;
			}else{
				$message['message_type'] = 'text';
			}
	        $message['sending_status'] = 1;
	        $message['time'] = $message['time'];
	        $messageObj = ChatMessage::newMessage($message);
			$dialogObj = ChatDialog::getData(ChatDialog::getOne($message['chatId'])); 
	    	broadcast(new IncomingMessage($domain , $dialogObj ));
		}else{
	        $messageObj = ChatMessage::newMessage($message);
			$dialogObj = ChatDialog::getData(ChatDialog::getOne($message['chatId'])); 
			broadcast(new BotMessage($domain , $dialogObj));
		}
	}
}