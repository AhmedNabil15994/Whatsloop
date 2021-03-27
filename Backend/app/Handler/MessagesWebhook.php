<?php
namespace App\Handler;
use App\Models\Bot;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\ContactReport;
use App\Models\ChatMessage;
use App\Events\IncomingMessage;
use App\Events\BotMessage;
use \Spatie\WebhookClient\ProcessWebhookJob;
use Http;
use Session;

class MessagesWebhook extends ProcessWebhookJob{
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
				    $messagesData['last'] = 100;
				    $messagesData['chatId'] = str_replace('@c.us', '', $sender);
					$result = $mainWhatsLoopObj->messages($messagesData);

					if(isset($result['data']) && isset($result['data']['messages'])){
						$count = count($result['data']['messages']);
						$lastObj = $result['data']['messages'][$count - 1];
						if(strpos($lastObj['body'], '://')){

							$lastObj['message_type'] = \ImagesHelper::checkExtensionType(substr($lastObj['body'], strrpos($lastObj['body'], '.') + 1));
							$fileName = '/uploads/chats/'.substr($lastObj['body'], strrpos($lastObj['body'], '/' )+1);
            				$destinationPath = public_path() . $fileName;
            				$succ = file_put_contents($destinationPath, file_get_contents($lastObj['body']));
							$lastObj['body'] = config("app.BASE_URL").$fileName;
						}else{
							$lastObj['message_type'] = 'text';
						}
				        $messageObj = ChatMessage::newMessage($lastObj);
				    	broadcast(new IncomingMessage($userObj->domain , ChatMessage::getData($messageObj)));
					}

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

	    			if($botObj){
	    				$botObj = Bot::getData($botObj);
	    				$reply = $botObj->reply;
	    				$myMessage = $reply;
	    				$message_type = '';
	    				if($botObj->reply_type == 1){
	    					$message_type = 'text';
		    				$sendData['body'] = $myMessage;
			    			$result = $mainWhatsLoopObj->sendMessage($sendData);
		    			}elseif($botObj->reply_type == 2){
		    				$message_type = \ImagesHelper::checkExtensionType(substr($botObj->file_name, strrpos($botObj->file_name, '.') + 1));
		    				$sendData['filename'] = $botObj->file_name;
		    				$sendData['body'] = $botObj->file;
		    				$sendData['caption'] = $botObj->reply;
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 3){
	    					$message_type = 'video';
		    				$sendData['filename'] = $botObj->file_name;
		    				$sendData['body'] = $botObj->file;
			    			$result = $mainWhatsLoopObj->sendFile($sendData);
		    			}elseif($botObj->reply_type == 4){
	    					$message_type = 'sound';
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
		    				$sendData['contactId'] = $botObj->whatsapp_no;
			    			$result = $mainWhatsLoopObj->sendContact($sendData);
		    			}elseif($botObj->reply_type == 7){
	    					$message_type = 'location';
		    				$sendData['lat'] = $botObj->lat;
		    				$sendData['lng'] = $botObj->lng;
		    				$sendData['address'] = $botObj->address;
			    			$result = $mainWhatsLoopObj->sendLocation($sendData);
		    			}elseif($botObj->reply_type == 8){
	    					$message_type = 'webhook';
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
				            $messageObj = ChatMessage::newMessage($lastMessage);
				            $messageObj['bot_details'] = $botObj;
	    					// Fire Bot Message Event For Web Application
				    		broadcast(new BotMessage($userObj->domain,$messageObj));
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
	    		}	

	    	}
	    }

	    // If Chat Status Changed
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
	    		}else{
	    			$statusInt = 1;
	    		}

	    		ChatMessage::where('id',$messageId)->update(['sending_status'=>$statusInt]);
	    	}
	    }
	}
}