<?php
namespace App\Handler;
use App\Models\Bot;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\ContactReport;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Events\IncomingMessage;
use App\Models\UserExtraQuota;
use App\Models\UserAddon;
use App\Models\Variable;
use App\Events\BotMessage;
use App\Events\SentMessage;
use App\Events\MessageStatus;
use \Spatie\WebhookClient\ProcessWebhookJob;
use \Spatie\WebhookServer\WebhookCall;
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
		
		$disabled = UserAddon::getDeactivated($tenantUser->id);
        $dis = 0;
        if(in_array(1,$disabled)){
            $dis = 1;
        }

		$startDay = strtotime(date('Y-m-d 00:00:00'));
        $endDay = strtotime(date('Y-m-d 23:59:59'));
        $messagesCount = ChatMessage::where('fromMe',1)->where('status','!=',null)->where('time','>=',$startDay)->where('time','<=',$endDay)->count();
        $membershipFeatures = \DB::connection('main')->table('memberships')->where('id',$tenantUser->membership_id)->first()->features;
        $featuresId = unserialize($membershipFeatures);
        $features = \DB::connection('main')->table('membership_features')->whereIn('id',$featuresId)->pluck('title_en');
        $dailyCount = @(int) $features[0];
        $extraQuotas = UserExtraQuota::getOneForUserByType($tenantUser->global_id,1);
        if($dailyCount + $extraQuotas <= $messagesCount){
            return 1;
        }

	    $mainWhatsLoopObj = new \MainWhatsLoop();

	    // If New Message Sent
	    if(!empty($messages)){
	    	foreach ($messages as $message) {
	    		$message = (array) $message;
	    		$sender = $message['chatId'];
	    		$senderMessage = $message['body'];
	    		// Fire Incoming Message Event For Web Application
				$this->handleMessages($userObj->domain,$message,$tenantObj->tenant_id);
	    		
	    		if($message['fromMe'] == false){					

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

	    			if($botObj && !$dis){
	    				$botObj = Bot::getData($botObj,$tenantObj->tenant_id);
	    				$botObj->file = str_replace('localhost',$userObj->domain.'.wloop.net',$botObj->file);
	    				$botObj->photo = str_replace('localhost',$userObj->domain.'.wloop.net',$botObj->photo);
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
		    				if($message_type == 'photo'){
			    				$sendData['caption'] = $botObj->reply;
		    				}
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
	    					$whats_message_type = 'link';
		    				$sendData['body'] = $botObj->https_url;
	        				$sendData['title'] = $botObj->url_title;
	        				$sendData['description'] = $botObj->url_desc;
	        				$sendData['previewBase64'] = substr(base64_encode(file_get_contents($botObj->photo)),20000)[0];
			    			$result = $mainWhatsLoopObj->sendLink($sendData);
		    			}elseif($botObj->reply_type == 6){
	    					$message_type = 'contact';
	    					$whats_message_type = 'contact';
		    				$sendData['contactId'] = str_replace('+','',$botObj->whatsapp_no);
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
			    			$result = $mainWhatsLoopObj->sendMessage($sendData);
		    			}
				        if(isset($result['data']) && isset($result['data']['id'])){
            				$checkMessageObj = ChatMessage::where('chatId',$sender)->where('chatName','!=',null)->orderBy('messageNumber','DESC')->first();
				            $messageId = $result['data']['id'];
				            $lastMessage['status'] = 'BOT';
				            $lastMessage['id'] = $messageId;
				            $lastMessage['chatId'] = $sender;
				            $lastMessage['fromMe'] = 1;
				            $lastMessage['message_type'] = $message_type;
				            $lastMessage['body'] = $sendData['body'];
				            if($message_type == 'photo'){
				            	$lastMessage['caption'] = $sendData['caption'];
		    				}
            				$lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
				            $lastMessage['type'] = $whats_message_type;
				            $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
				            $lastMessage['sending_status'] = 1;
            				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
				            $messageObj = ChatMessage::getData(ChatMessage::newMessage($lastMessage));


				            $dialog = ChatDialog::getOne($sender);
							$dialog->last_time = $lastMessage['time'];
							$dialogObj = ChatDialog::getData($dialog); 
	    					$dialogObj->lastMessage = $messageObj;
				            $dialogObj->lastMessage->bot_details = $botObj;
	    					// Fire Bot Message Event For Web Application
				    		broadcast(new BotMessage($userObj->domain , $dialogObj));
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

	    		// Fire Webhook For Client
				$this->fireWebhook($message);
	    	}
	    }

	    // If Chat Status Changed
	    if(!empty($actions) && !$dis){
	    	$this->handleUpdates($userObj->domain,$actions);
	    	// Fire Webhook For Client
			$this->fireWebhook($actions);
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

	public function handleMessages($domain,$message,$tenantId){
		if(strpos($message['body'], '://') !== false){
			$message['message_type'] = \ImagesHelper::checkExtensionType(substr($message['body'], strrpos($message['body'], '.') + 1));
			$fileName = substr($message['body'], strrpos($message['body'], '/' )+1);
			$destinationPath = public_path().'/uploads/'.$tenantId.'/chats/' . $fileName;
			$succ = file_put_contents($destinationPath, file_get_contents($message['body']));
			$message['body'] = config('app.BASE_URL').'/public/uploads/'.$tenantId.'/chats/' . $fileName;
		}else{
			$message['message_type'] = 'text';
		}
        $message['sending_status'] = 1;
        $message['time'] = strtotime(date('Y-m-d H:i:s'));
        $checkMessageObj = ChatMessage::where('chatId',$message['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->first();
        $message['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
        $messageObj = ChatMessage::newMessage($message);
        $dialog = ChatDialog::getOne($message['chatId']);
        if(!$dialog){
            $dialogObj = new ChatDialog;
            $dialogObj->id = $message['chatId'];
            $dialogObj->name = $message['chatId'];
            $dialogObj->image = '';
            $dialogObj->metadata = '';
            $dialogObj->is_pinned = 0;
            $dialogObj->is_read = 0;
            $dialogObj->modsArr = '';
            $dialogObj->last_time = $message['time'];
            $dialogObj->save();
        }else{
        	$dialog->last_time = $message['time'];
            $dialog->save();
        }
		$dialogObj = ChatDialog::getData($dialog); 
		if($message['fromMe'] == 0){
	    	broadcast(new IncomingMessage($domain , $dialogObj ));
		}else{
	    	broadcast(new SentMessage($domain , $dialogObj ));
		}
	}

	public function fireWebhook($data){
		$webhook = Variable::getVar('WEBHOOK_URL');
		if($webhook){
			WebhookCall::create()
			   ->url($webhook)
			   ->payload(['data' => $data])
			   ->doNotSign()
			   ->dispatch();
		}
	}
}