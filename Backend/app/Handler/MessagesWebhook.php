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
use App\Models\UserChannels;
use App\Models\CentralChannel;
use App\Models\Variable;
use App\Models\Order;
use App\Models\Product;
use App\Models\Template;
use App\Models\CentralVariable;
use App\Models\OrderDetails;
use App\Models\Category;
use App\Models\ContactLabel;
use App\Models\BotPlus;
use App\Models\ModTemplate;
use App\Models\OAuthData;
use App\Events\BotMessage;
use App\Events\SentMessage;
use App\Events\MessageStatus;
use App\Events\ChatLabelStatus;
use \Spatie\WebhookClient\ProcessWebhookJob;
use \Spatie\WebhookServer\WebhookCall;
use Http;
use Session;
use Throwable;


class MessagesWebhook extends ProcessWebhookJob{
	public function handle(){
        // ini_set('memory_limit', '-1');
	    $data = json_decode($this->webhookCall, true);
	    $mainData = $data['payload'];
	    $messages = @$mainData['messages'];
	    $actions = @$mainData['ack'];
		$tenantUser = User::first();
		$tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$tenantUser->global_id)->first();
		$userObj = \DB::connection('main')->table('domains')->where('tenant_id',$tenantObj->tenant_id)->first();
		
		$disabled = UserAddon::getDeactivated($tenantUser->id);
        $dis = 0;
        $dis2 = 0;
        if(in_array(1,$disabled)){
            $dis = 1;
        }
        if(in_array(10,$disabled)){
            $dis2 = 1;
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
				$lastM = $this->handleMessages($userObj->domain,$message,$tenantObj->tenant_id);

	    		if($message['fromMe'] == false && !str_contains($message['chatId'], '@g.us') ){	
	    			$this->handleNotification($message,$lastM);

	    			if(str_contains($senderMessage, 'هنيهم ')){
	    				$client_name = explode('هنيهم ', $senderMessage);
	    				$client_name = $client_name[1];

	    				$extra_msg = Variable::getVar('EXTRA_MSG');
	    				$color = Variable::getVar('COLOR');
	    				$color = $color != null ? $color : '#000'; 

	    				$size = Variable::getVar('SIZE');
	    				$size = $size != null ? $size : '30'; 
	    				$font_family = 'STC-Regular';

	    				$templateId = Variable::getVar('SELECTED_TEMPLATE');
	    				if($templateId == null){
	    					$varObj = Variable::where('var_key','LIKE','TEMPLATE%')->orderBy('id','DESC')->get();
	    					$varObj = !empty($varObj) && isset($varObj[0]) ? $varObj[0] : null;
	    					if($varObj){
		    					$templateId = str_replace('TEMPLATE','',$varObj->var_key);	
	    					}
	    				}else{
	    					$varObj = Variable::where('var_key','TEMPLATE'.$templateId)->first();
	    				}
	    				

	    				if($templateId == null){
							$templateId = 12;
							$dimens = [
								300,
								900
							];
	    				}else{
	    				    $dimens = explode(',',$varObj->var_value);
	    				}

	    				$url = 'https://final.wloop.net/public/tenancy/assets/V5/bnrs/'.$templateId.'.png';
	    				$urlData = [
	    					'text' => $client_name,
	    					'font_color' => $color,
	    					'font_size' => $size,
	    					'font_family' => $font_family,
	    					'margin_top' => $templateId % 2 == 0 ? (($dimens[1] / 10) - 10) : (($dimens[1] / 20) + 2),
	    					'margin_left' => $dimens[0],
	    					'template' => $url,
	    				];

	    				if($templateId != null){
	    					$curl = curl_init();
							curl_setopt_array($curl, array(
							  CURLOPT_URL => 'https://hneehm.com/api/',
							  CURLOPT_RETURNTRANSFER => true,
							  CURLOPT_ENCODING => '',
							  CURLOPT_MAXREDIRS => 10,
							  CURLOPT_TIMEOUT => 0,
							  CURLOPT_FOLLOWLOCATION => true,
							  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							  CURLOPT_CUSTOMREQUEST => 'POST',
							  CURLOPT_POSTFIELDS => $urlData,
							));
							$response = curl_exec($curl);
							curl_close($curl);

							$mainWhatsLoopObj = new \MainWhatsLoop();
							$sendData['chatId'] = $sender;
							$sendData['filename'] = 'hneehm'.rand(1,100000).'.png';
							$sendData['body'] = 'data:image/png;base64,'.base64_encode($response);
							if($extra_msg){
								$sendData['caption'] = $extra_msg;
							}
							$result = $mainWhatsLoopObj->sendFile($sendData);
	    				}
	    				return '';
	    			}

	    			if($message['type'] == 'buttons_response'){
	    				$this->handleButtonsResponse($message,$sender,$userObj,$tenantObj);
	    				if(!in_array(4,$disabled) || !in_array(5,$disabled)){
	    					$this->handleTemplateButtonsResponse($message,$sender,$userObj,$tenantObj);
	    				}
	    			}else{
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
		    			$botObj = Bot::findBotMessage($langPref,$senderMessage);
		    			if($botObj){
		    				if(count($botObj) == 0){
			    				$botObj = Bot::findBotMessage(!$langPref,$senderMessage);
			    				if(count($botObj) > 0){
			    					foreach ($botObj as $key => $oneBot) {
				    					$this->handleBasicBot($oneBot,$userObj->domain,$sender,$tenantObj->tenant_id,$message);
			    					}
			    				}
			    			}else if(count($botObj) > 0 && $message['type'] != 'buttons_response'){
			    				foreach ($botObj as $key => $oneBots) {
			    					$this->handleBasicBot($oneBots,$userObj->domain,$sender,$tenantObj->tenant_id,$message);
		    					}
			    			}
		    			}
		    		
		    			// Find BotPlus Object Based on incoming message
		    			$botPlusObjs = BotPlus::findBotMessage($langPref,$senderMessage);
		    			if($botPlusObjs){
		    				$botPlusObj = BotPlus::getData($botPlusObjs);
		    				$this->handleBotPlus($message,$botPlusObj,$userObj->domain,$sender);
		    			}
		    			
		    			if( ((!$botObj) || count($botObj) == 0) && !$botPlusObjs && $message['type'] != 'order'){
		    				$varObj = Variable::getVar('UNKNOWN_BOT_REPLY');
		    				if($varObj){
		    					$myMessage = $varObj;
			    				$sendData['body'] = $myMessage;
			    				$sendData['chatId'] = $sender;
				    			$result = $mainWhatsLoopObj->sendMessage($sendData);
		    				}	
		    			}
	    			}
	    		}	

	    		// Fire Webhook For Client
				$this->fireWebhook($message);
	    	}
	    }

	    // If Chat Status Changed
	    if(!empty($actions)){
	    	$this->handleUpdates($userObj->domain,$actions);
	    	// Fire Webhook For Client
			$this->fireWebhook($actions);
	    }

	}


	public function handleTemplateButtonsResponse($message,$sender,$userObj,$tenantObj){
		$mainWhatsLoopObj = new \MainWhatsLoop();
		$msgId= $message['quotedMsgId'];
		$msgObj = ChatMessage::find($msgId);
		if($msgObj){
			if(in_array($msgObj->module_id,[4,5]) && $msgObj->module_status != '' ){
				$mod_id = $msgObj->module_id == 4 ? 2 : 1;
				$templateObj = ModTemplate::where('mod_id',$mod_id)->where('statusText',$msgObj->module_status)->first();
				if($templateObj && $templateObj->type > 1){
					$botObj = BotPlus::find($templateObj->type);
					$replyData = null;
					$botObj = BotPlus::getData($botObj);
					if($botObj && isset($botObj->buttonsData)){
						foreach($botObj->buttonsData as $buttonData){
							if($buttonData['text'] == $message['body']){
								$replyData = $buttonData;
							}
						}
					}
					if($replyData != null){
						if(isset($replyData['reply_type']) && $replyData['reply_type'] == 1){
							$sendData['body'] = $replyData['msg'];
							$sendData['chatId'] = $sender;
							$result = $mainWhatsLoopObj->sendMessage($sendData);
							$this->handleRequest($message,$userObj->domain,$result,$sendData,'BOT PLUS','text','chat','BotMessage');
						}else if(isset($replyData['reply_type']) && $replyData['reply_type'] == 2){
							if($replyData['msg_type'] == 2){
								$botObj = BotPlus::getData(BotPlus::getOne($replyData['msg']));
								$this->handleBotPlus($message,$botObj,$userObj->domain,$sender);
							}elseif($replyData['msg_type'] == 1){
								$botObj = Bot::getData(Bot::getOne($replyData['msg']),$tenantObj->tenant_id);
								$this->handleBasicBot($botObj,$userObj->domain,$sender,$tenantObj->tenant_id,$message);
							}
						}else if(isset($replyData['reply_type']) && $replyData['reply_type'] == 3){
							$this->handleTemplateOrderStatus($mod_id,$msgObj->module_order_id,$replyData);
						}
					}
				}
			}

		}
	}

	public function handleTemplateOrderStatus($mod_id,$module_order_id,$replyData){
		if($mod_id == 1){ // Salla
			$status = $replyData['msg'];
	        $baseUrl = 'https://api.salla.dev/admin/v2/orders/'.$module_order_id.'/status';
	        $token = Variable::getVar('SallaStoreToken'); 
	        $userObj = User::first();
	        $oauthDataObj = OAuthData::where('user_id',$userObj->id)->where('type','salla')->first();
	        if($oauthDataObj && $oauthDataObj->authorization != null){
	            $token = $oauthDataObj->authorization;
	        }

	        $data = Http::withToken($token)->post($baseUrl,['status_id'=>$status]);
	        $result = $data->json();
		}elseif($mod_id == 2){  // Zid
			$status = $replyData['msg'];
			if($replyData['msg'] == 'جديد'){
				$status = 'new';
			}elseif($replyData['msg'] == 'جاري التجهيز'){
				$status = 'preparing';
			}elseif($replyData['msg'] == 'جاهز'){
				$status = 'ready';
			}elseif($replyData['msg'] == 'جارى التوصيل'){
				$status = 'indelivery';
			}elseif($replyData['msg'] == 'تم التوصيل'){
				$status = 'delivered';
			}elseif($replyData['msg'] == 'تم الالغاء'){
				$status = 'cancelled';
			}

        	$baseUrl = 'https://api.zid.sa/v1/managers/store/orders/'.$module_order_id.'/change-order-status';
        	$storeID = Variable::getVar('ZidStoreID');
        	$storeToken = CentralVariable::getVar('ZidMerchantToken');
        	$managerToken = Variable::getVar('ZidStoreToken');

        	$oauthDataObj = OAuthData::where('type','zid')->where('user_id',User::first()->id)->first();
        	$authorize = $oauthDataObj != null && $oauthDataObj->token_type != null ? $oauthDataObj->token_type . ' ' . $oauthDataObj->authorization : '';
	        $myHeaders = [
	            "X-MANAGER-TOKEN" => $managerToken,
	            "order-id" => $module_order_id,
	        ];
	        if($authorize != ''){
	        	$data = Http::withToken($oauthDataObj->authorization)->withHeaders($myHeaders)->post($baseUrl,['order_status'=>$status,]);         
	        	$result = $data->json();
	        }else{
        		$myHeaders = [
		            "X-MANAGER-TOKEN" => $managerToken,
		            "STORE-ID" => $storeID,
		            "ROLE" => 'Manager',
		            'User-Agent' => 'whatsloop/1.00.00 (web)',
		        ];

		        $dataArr = [
		            'baseUrl' => $baseUrl,
		            'storeToken' => $storeToken,
		            'dataURL' => $dataURL,
		            'tableName' => $tableName,
		            'myHeaders' => $myHeaders,
		            'service' => $service,
		            'params' => [],
		        ];
		        $data = Http::withToken($storeToken)->withHeaders($myHeaders)->post($baseUrl,['order_status'=>$status,]);
	        	$result = $data->json();
	        }
		}
	}

	public function handleButtonsResponse($message,$sender,$userObj,$tenantObj){
		$mainWhatsLoopObj = new \MainWhatsLoop();
		$msgText= $message['quotedMsgBody'];
		$botObjs = BotPlus::getMsg($msgText);
		$replyData = null;
		if(isset($botObjs->buttonsData)){
			foreach($botObjs->buttonsData as $buttonData){
				if($buttonData['text'] == $message['body']){
					$replyData = $buttonData;
				}
			}
		}

		if($replyData == null){
			$botPlusObj = BotPlus::getMsg2($message['body']);
			$newReplyData = null;
    		if(isset($botPlusObj->buttonsData)){
    			foreach($botPlusObj->buttonsData as $buttonData){
    				if($buttonData['text'] == $message['body']){
    					$newReplyData = $buttonData;
    				}
    			}
    		}
			if($newReplyData == null){
			    $this->handleBotPlus($message,$botPlusObj,$userObj->domain,$sender);
			}else{
    		    if(isset($newReplyData['reply_type']) && $newReplyData['reply_type'] == 1){
    				$sendData['body'] = $newReplyData['msg'];
    				$sendData['chatId'] = $sender;
    				$result = $mainWhatsLoopObj->sendMessage($sendData);
    				$this->handleRequest($message,$userObj->domain,$result,$sendData,'BOT PLUS','text','chat','BotMessage');
    			}else if(isset($newReplyData['reply_type']) && $newReplyData['reply_type'] == 2){
    				if($newReplyData['msg_type'] == 2){
    					$botObj = BotPlus::getData(BotPlus::getOne($newReplyData['msg']));
    					$this->handleBotPlus($message,$botObj,$userObj->domain,$sender);
    				}elseif($newReplyData['msg_type'] == 1){
    					$botObj = Bot::getData(Bot::getOne($newReplyData['msg']),$tenantObj->tenant_id);
    					$this->handleBasicBot($botObj,$userObj->domain,$sender,$tenantObj->tenant_id,$message);
    				}
    			}
			}
		}else{
			if(isset($replyData['reply_type']) && $replyData['reply_type'] == 1){
				$sendData['body'] = $replyData['msg'];
				$sendData['chatId'] = $sender;
				$result = $mainWhatsLoopObj->sendMessage($sendData);
				$this->handleRequest($message,$userObj->domain,$result,$sendData,'BOT PLUS','text','chat','BotMessage');
			}else if(isset($replyData['reply_type']) && $replyData['reply_type'] == 2){
				if($replyData['msg_type'] == 2){
					$botObj = BotPlus::getData(BotPlus::getOne($replyData['msg']));
					$this->handleBotPlus($message,$botObj,$userObj->domain,$sender);
				}elseif($replyData['msg_type'] == 1){
					$botObj = Bot::getData(Bot::getOne($replyData['msg']),$tenantObj->tenant_id);
					$this->handleBasicBot($botObj,$userObj->domain,$sender,$tenantObj->tenant_id,$message);
				}
			}
		}
	}
	
	public function handleNotification($message,$lastM){
		$vars = Variable::where('var_key','LIKE','ONESIGNALPLAYERID_%')->get();
		$ids = [];
		foreach($vars as $var){
			$more = array_values((array)json_decode($var->var_value));
			$ids = array_merge($ids,$more);
		}
		$ids = array_unique($ids);
		
		if(!empty($ids)){
			\OneSignalHelper::sendnotification([
				'title' => $message['senderName'],
				'message' => $lastM,
				'type' => 'ios',
				'to' => array_values($ids),
				'image' => '',
			]);
		}

	}

	public function handleBasicBot($botObj,$domain,$sender,$tenantId,$message){
    	$mainWhatsLoopObj = new \MainWhatsLoop();
		$botObj = Bot::getData($botObj,$tenantId);
		$botObj->file = str_replace('localhost',$domain.'.wloop.net',$botObj->file);
		$botObj->photo = str_replace('localhost',$domain.'.wloop.net',$botObj->photo);
		$reply = $botObj->reply;
		$myMessage = $reply;
		$message_type = '';
		$whats_message_type = '';
		$sendData['chatId'] = $sender;
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
			$sendData['body'] = $botObj->file;
		}elseif($botObj->reply_type == 5){
			$message_type = 'link';
			$whats_message_type = 'link';
			$sendData['body'] = $botObj->https_url;
			$sendData['title'] = $botObj->url_title;
			$sendData['description'] = $botObj->url_desc;
			if($botObj->photo){
				$dets = substr(base64_encode(@file_get_contents($botObj->photo)),20000);
				if(is_array($dets)){
					$sendData['previewBase64'] = $dets[0];
				}
			}
			$result = $mainWhatsLoopObj->sendLink($sendData);
		}elseif($botObj->reply_type == 6){
			$message_type = 'contact';
			$whats_message_type = 'contact';
			$sendData['contactId'] = str_replace('+','',$botObj->whatsapp_no);
			$result = $mainWhatsLoopObj->sendContact($sendData);
			$sendData['body'] = str_replace('+','',$botObj->whatsapp_no);
		}elseif($botObj->reply_type == 7){
			$message_type = 'location';
			$whats_message_type = 'location';
			$sendData['lat'] = $botObj->lat;
			$sendData['lng'] = $botObj->lng;
			$sendData['address'] = $botObj->address;
			$result = $mainWhatsLoopObj->sendLocation($sendData);
			$sendData['body'] = $botObj->address;
		}elseif($botObj->reply_type == 8){
			$message_type = 'webhook';
			$whats_message_type = 'webhook';
			$sendData['body'] = $botObj->webhook_url;
			// $result = $mainWhatsLoopObj->sendMessage($sendData);
			$message['author'] = str_replace('@c.us','',$message['author']);
			$message['chatId'] = str_replace('@c.us','',$message['chatId']);
			$message['chatName'] = str_replace('@c.us','',$message['chatName']);
			$webhookData = [
				'message' => $message,
				'templates' => Template::dataList(null,$botObj->templates)['data'],
			];
			$this->fireWebhook($webhookData, $botObj->webhook_url);
		}
		if($message_type != 'webhook'){
		    $this->handleRequest($message,$domain,$result,$sendData,'BOT',$message_type,$whats_message_type,'BotMessage',$botObj);
		}
	}

	public function handleBotPlus($message,$botObj,$domain,$sender){
		$buttons = '';
    	$mainWhatsLoopObj = new \MainWhatsLoop();
        if(isset($botObj->buttonsData) && !empty($botObj->buttonsData)){
    		foreach($botObj->buttonsData as $key => $oneItem){
    			$buttons.= $oneItem['text'].( $key == $botObj->buttons -1 ? '' : ',');
    		}
    
    		$sendData['body'] = $botObj->body;
    		$sendData['title'] = $botObj->title;
    		$sendData['footer'] = $botObj->footer;
    		$sendData['buttons'] = $buttons;
    		$sendData['chatId'] = str_replace('@c.us','',$sender);
    		$result = $mainWhatsLoopObj->sendButtons($sendData);
    		Logger($result->json());
    		$sendData['chatId'] = $sender;

    		if($botObj->category_id != null){
				$categoryObj = Category::find($botObj->category_id);
		        if($categoryObj){
		        	$labelData['liveChatId'] = $sendData['chatId'];
			        $labelData['labelId'] = $categoryObj->labelId;
			        
			        $varObj = Variable::getVar('BUSINESS');
			        if($varObj){
			            $mainWhatsLoopObj2 = new \MainWhatsLoop();
			            $result1 = $mainWhatsLoopObj2->unlabelChat($labelData);
			            $result2 = $mainWhatsLoopObj2->labelChat($labelData);
			            $result3 = $result2->json();  
			        }
			        
			        $contactLabelObj = ContactLabel::newRecord(str_replace('@c.us','',$sendData['chatId']),$labelData['labelId']);
			        broadcast(new ChatLabelStatus($domain, ChatDialog::getData(ChatDialog::getOne($labelData['liveChatId'])) , Category::getData($categoryObj) , 1 ));
		        }
			}

			if($botObj->moderator_id != null){
				$modObj = User::find($botObj->moderator_id);
				if($modObj){
			        $dialogObj = ChatDialog::getOne($sendData['chatId']);
			        $modArrs = $dialogObj->modsArr;
			        if($modArrs == null){
			            $dialogObj->modsArr = serialize([$botObj->moderator_id]);
			            $dialogObj->save();
			        }else{
			            $oldArr = unserialize($dialogObj->modsArr);
			            if(!in_array($botObj->moderator_id, $oldArr)){
			                array_push($oldArr, $botObj->moderator_id);
			                $dialogObj->modsArr = serialize($oldArr);
			                $dialogObj->save();
			            }
			        }
				}
			}


            $this->handleRequest($message,$domain,$result,$sendData,'BOT PLUS','text','chat','BotMessage',$botObj);
        }
	}

	public function handleUpdates($domain,$actions){
		foreach ($actions as $action) {
    		$action = (array) $action;
    		$sender = $action['chatId'];
    		$messageId = $action['id'];
    		$messageObj = ChatMessage::where('id',$messageId)->first();
    		if($action['status'] == 'delivered'){
    			$statusInt = 2;
    			$contactObj = ContactReport::where('contact',str_replace('@c.us', '', $sender))->where('message_id',$messageId)->update(['status' => $statusInt]);
    		}elseif ($action['status'] == 'viewed') {
    			$statusInt = 3;
    			$contactObj = ContactReport::where('contact',str_replace('@c.us', '', $sender))->where('message_id',$messageId)->update(['status' => $statusInt]);
    		}elseif ($action['status'] == 'sent'){
    			$statusInt = 1;
    		}

    		if(isset($messageObj) && $statusInt > $messageObj->sending_status){
	    		$messageObj->update(['sending_status'=>$statusInt]);
    		}
			broadcast(new MessageStatus($domain, $sender, $messageId , $statusInt ));
    	}
	}

	public function handleMessages($domain,$message,$tenantId){
		$hasOrders = 0;
		$lastOrder = [];
		if(filter_var($message['body'], FILTER_VALIDATE_URL) && !in_array($message['type'],['product','order','chat'])){
			$message['message_type'] = \ImagesHelper::checkExtensionType(substr($message['body'], strrpos($message['body'], '.') + 1));
			$fileName = substr($message['body'], strrpos($message['body'], '/' )+1);
			$destination = public_path().'/uploads/'.$tenantId.'/chats/';
			$destinationPath = public_path().'/uploads/'.$tenantId.'/chats/' . $fileName;
			if (!file_exists($destination)) {
	            @mkdir($destination, 0777, true);
	        }
			$succ = @file_put_contents($destinationPath, @file_get_contents($message['body']));
			$message['body'] = config('app.BASE_URL').'/public/uploads/'.$tenantId.'/chats/' . $fileName;
			$message['caption'] = $message['caption'];
		}else{
			$message['message_type'] = in_array($message['type'],['product','order']) ? $message['type'] : 'text';
			if($message['message_type'] == 'order' && $message['metadata']){

				$orderDetails = $message['metadata'];
				$orderDetails['sellerJid'] = str_replace('@s.whatsapp.net','',$orderDetails['sellerJid']);
				$orderCallData = [
					'sellerJid' => $orderDetails['sellerJid'],
					'orderId' => $orderDetails['orderId'],
					'orderToken' => $orderDetails['orderToken'],
				];
				$channelObj = CentralChannel::first();
	    		$mainWhatsLoopObj = new \MainWhatsLoop($channelObj->instanceId,$channelObj->token);
                $ordersCall = $mainWhatsLoopObj->getOrder($orderDetails);
                $ordersCall = $ordersCall->json();
                if($ordersCall && $ordersCall['status'] && $ordersCall['status']['status'] == 1 && isset($ordersCall['data']['orders']) && !empty($ordersCall['data']['orders'])  ){

               		$count = 0;
                    $ordersData = $ordersCall['data']['orders'];
                    foreach($ordersData as $orderData){
                        $orderObj = Order::getOne($orderData['id']);
                        if(!$orderObj){
                            $orderObj = new Order;
                            $orderObj->status = 1;
                        }

                        $count+= count($orderData['products']);

                        $orderObj->order_id =  $orderData['id'];
                        $orderObj->subtotal = $orderData['subtotal'];
                        $orderObj->tax = $orderData['tax'];
                        $orderObj->total = $orderData['total'];
                        $orderObj->message_id = $message['id'];
                        $orderObj->products = serialize($orderData['products']);
                        $orderObj->client_id = $message['author'];
                        $orderObj->products_count = $count;
                        $orderObj->created_at = $orderData['createdAt'];
                        $orderObj->save();
                        $hasOrders = 1;
                        $lastOrder = (object) $orderObj;
                    }
                }
			}
		}
        $message['sending_status'] = 2;
        $message['time'] = strtotime(date('Y-m-d H:i:s'));
        $checkMessageObj = ChatMessage::where('chatId',$message['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->orderBy('time','DESC')->first();
        $message['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
        $message['status'] = $message['fromMe'] == 1 ? (isset($message['metadata']) && isset($message['metadata']['replyButtons']) ? 'BOT PLUS' : 'APP') : '';
        $messageObj = ChatMessage::newMessage($message);
        $dialog = ChatDialog::getOne($message['chatId']);
        if(!$dialog){
            $dialog = new ChatDialog;
            $dialog->id = $message['chatId'];
            $dialog->name = $message['chatId'];
            $dialog->image = '';
            $dialog->metadata = '';
            $dialog->is_pinned = 0;
            $dialog->is_read = 0;
            $dialog->modsArr = '';
            $dialog->last_time = $message['time'];
            $dialog->save();
        }else{
        	$dialog->last_time = $message['time'];
            $dialog->save();
        }
		$dialogObj = ChatDialog::getData($dialog); 
		if($message['fromMe'] == 0){
	    	broadcast(new IncomingMessage($domain , $dialogObj ));
	    	if($hasOrders){
	    		$this->performAddonIntegration($message,$domain,$lastOrder,$tenantId);
				$this->sendLink($message,$domain,$lastOrder);
			}
		}else{
	    	broadcast(new SentMessage($domain , $dialogObj ));
		}

		if($message['type'] == 'chat'){
			return $message['body'];
		}elseif($message['type'] == 'document'){
			return 'Document ';
		}elseif($message['type'] == 'video'){
			return 'Video ';
		}elseif($message['type'] == 'ppt'){
			return 'Sound ';
		}elseif($message['type'] == 'image'){
			return 'Photo ';
		}elseif($message['type'] == 'vcard'){
			$number = @explode(':+',explode(';waid=',$message['body'])[1])[0];
			return 'Contact '.str_replace('END:VCARD','',$number);
		}elseif($message['type'] == 'order'){
			return 'Order '.$message['metadata']['orderTitle'];
		}elseif($message['type'] == 'product'){
			return 'Product';
		}
	}

	public function handleRequest($message,$domain,$result,$sendData,$status,$message_type,$whats_message_type,$channel,$botObj=null){
		if(isset($result['data']) && isset($result['data']['id'])){
            $checkMessageObj = ChatMessage::where('chatId',$sendData['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->orderBy('time','DESC')->first();
            $messageId = $result['data']['id'];
            $lastMessage['status'] = $channel == 'SentMessage' ? (isset($message['metadata']) && isset($message['metadata']['replyButtons']) ? 'BOT PLUS' : 'APP') : $status;
            $lastMessage['id'] = $messageId;
            $lastMessage['fromMe'] = 1;
            if($status == 'BOT' && $message_type == 'photo'){
            	$lastMessage['caption'] = $sendData['caption'];
			}
            $lastMessage['chatId'] = $sendData['chatId'];
            $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
            $lastMessage['body'] = $sendData['body'];
            $lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
            $lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
            $lastMessage['message_type'] = $message_type;
            $lastMessage['sending_status'] = 2;
            $lastMessage['caption'] = $message['caption'];
            $lastMessage['type'] = $whats_message_type;
            $lastMessage['metadata'] = json_encode($message['metadata']);
            if($whats_message_type == 'vcard'){
                $lastMessage['body'] = $message['body'];
            }
            if($whats_message_type == 'location'){
                $lastMessage['body'] = $message['body'];
                $lastMessage['caption'] = $message['caption'];
            }
            $messageObj = ChatMessage::newMessage($lastMessage);
            $dialog = ChatDialog::getOne($sendData['chatId']);
            $dialog->last_time = $lastMessage['time'];
            $dialogObj = ChatDialog::getData($dialog);
            if($channel == 'SentMessage'){
	            broadcast(new SentMessage($domain , $dialogObj ));
            }else if($channel == 'BotMessage' && $botObj){
            	$dialogObj->lastMessage = $messageObj;
            	$dialogObj->lastMessage->bot_details = $botObj;
    			broadcast(new BotMessage($domain , $dialogObj));
            }
        }
	}

	public function fireWebhook($data,$url=null){
		if($url){
			WebhookCall::create()
				   ->url($url)
				   ->payload($data)
				   ->doNotSign()
				   ->dispatch();
		}else{
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

	public function failed(Throwable $exception)
    {
        $count = \DB::connection('main')->table('failed_jobs')->count();
        
        // system('/usr/local/bin/php /home/wloop/public_html/artisan queue:restart');
        // system('/home/wloop/public_html/vendor/supervisorctl restart whatsloop:*');
    }

    public function performAddonIntegration($message,$domain,$order,$tenantId){
		// Product Details
		$this->integerateProducts($message,$domain,$order,$tenantId);

		// Customer Details
		$this->integerateCustomers($message,$domain,$order,$tenantId);
	}

	public function integerateProducts($message,$domain,$order,$tenantId){
		$prodsArr = [];
		foreach(unserialize($order['products']) as $oneProduct){
			$prodArr = [
				'id' => $oneProduct['id'],
				'name' => $oneProduct['name'],
				'price' => $oneProduct['price'],
				'product_type' => 'digital',
			];

			$productObj = Product::where('product_id',$oneProduct['id'])->first();
			if(!$productObj){
				unset($prodArr['id']);
				$prodArr['product_id'] = $oneProduct['id'];
				$productType = $prodArr['product_type'];
				unset($prodArr['product_type']);
				Product::insert($prodArr);
				$prodArr['product_type'] = $productType;
				$productObj = Product::where('product_id',$oneProduct['id'])->first();
			}

			// if($productObj->addon_product_id == null){
			// 	$baseUrl = 'https://api.salla.dev/admin/v2/products';
		 //        $token = Variable::getVar('SallaStoreToken'); 
		 //        $userObj = User::first();
		 //        $oauthDataObj = OAuthData::where('user_id',$userObj->id)->where('type','salla')->first();
		 //        if($oauthDataObj && $oauthDataObj->authorization != null){
		 //            $token = $oauthDataObj->authorization;
		 //        }

		 //        unset($prodArr['id']);
		 //        $data = Http::withToken($token)->post($baseUrl,$prodArr);
		 //        $result = $data->json();

		 //        if(isset($result['success']) && $result['success'] == true){
		 //        	$productId = $result['data']['id'];
		 //        	$productObj->addon_product_id = $productId;
		 //        	$productObj->save();

		 //        	$imagesUrl = $baseUrl.'/'.$productId.'/images';
		 //        	foreach($oneProduct['images'] as $key => $oneImage){
			// 			$fileName = 'productImage'.($key+1).$productId.'.png';
			// 			$destination = public_path().'/uploads/'.$tenantId.'/chats/';
			// 			$destinationPath = public_path().'/uploads/'.$tenantId.'/chats/' . $fileName;
			// 			if (!file_exists($destination)) {
			// 	            @mkdir($destination, 0777, true);
			// 	        }
			// 			$succ = @file_put_contents($destinationPath, @file_get_contents($oneImage));
			// 			$url = config('app.BASE_URL').'/public/uploads/'.$tenantId.'/chats/' . $fileName;

		 //        		$imageData = Http::withToken($token)->post($imagesUrl,[
			//         		'original' => $url,
			//         		'thumbnail' => $url, 
			//         	]);
		 //        		$result = $imageData->json();
		 //        	}
		        	
		 //        }
			// }
		}
	}

	public function integerateCustomers($message,$domain,$order,$tenantId){
		$name = explode(' ', $message['senderName']);
		$mobile = '+'.str_replace('@c.us','',$message['author']);
		$phoneNumber = preg_replace("/^\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/", '',$mobile);

		$customerArr = [
			'first_name' => $name[0],
			'last_name' => isset($name[1]) && $name[1] != null && $name[1] != '' ? $name[1] : $name[0],
			'mobile' => $phoneNumber,
			'mobile_code_country' => str_replace($phoneNumber,'',$mobile),
		];

		$orderObj = Order::where('order_id',$order->order_id)->first();
		if($orderObj){
			$orderDetailsObj = OrderDetails::where('order_id',$orderObj->id)->first();
			if(!$orderDetailsObj){
				$orderDetailsObj = new OrderDetails;
				$orderDetailsObj->order_id = $orderObj->id;
				$orderDetailsObj->name = $message['senderName'];
				$orderDetailsObj->phone = str_replace('+', '', $mobile);
				$orderDetailsObj->save();
			}

			if($orderDetailsObj->addon_customer_id == null){
				$baseUrl = 'https://api.salla.dev/admin/v2/customers';
		        $token = Variable::getVar('SallaStoreToken'); 
		        $userObj = User::first();
		        $oauthDataObj = OAuthData::where('user_id',$userObj->id)->where('type','salla')->first();
		        if($oauthDataObj && $oauthDataObj->authorization != null){
		            $token = $oauthDataObj->authorization;
		        }

		        $data = Http::withToken($token)->post($baseUrl,$customerArr);
		        $result = $data->json();
		        if(isset($result['success']) && $result['success'] == true){
		        	OrderDetails::where('order_id',$orderObj->id)->update(['addon_customer_id'=>$result['data']['id']]);
		        }
			}			
		}
	}
	

	public function sendLink($message,$domain,$oldOrderObj){
		$orderObj = Order::getData($oldOrderObj);
        $sendData['chatId'] = $orderObj->client_id;
        $url = \URL::to('/').'/orders/'.$orderObj->order_id.'/view';
        $url = str_replace('localhost',$domain.'.wloop.net',$url);
        foreach($orderObj->products as $product){
            $productObj = Product::where('product_id',$product['id'])->first();
            if(!$productObj){
                return 0;     
            }
        }
        $templateObj = Template::NotDeleted()->where('name_ar','whatsAppOrders')->first();
        if($templateObj && $orderObj->client){
            $content = $templateObj->description_ar;
            $content = str_replace('{CUSTOMERNAME}', str_replace('@c.us','',str_replace('+','',$orderObj->client->name)), $content);
            $content = str_replace('{ORDERID}', $orderObj->order_id, $content);
            $content = str_replace('{ORDERURL}', $url, $content);

            $sendData['body'] = $content;
            $mainWhatsLoopObj = new \MainWhatsLoop();
            $result = $mainWhatsLoopObj->sendMessage($sendData);
            $result = $result->json();
            $this->handleRequest($message,$domain,$result,$sendData,UserChannels::first()->name,'text','chat','SentMessage',$orderObj);
            $oldOrderObj->channel = $templateObj->channel;
            $oldOrderObj->save();
        }   
	}

}