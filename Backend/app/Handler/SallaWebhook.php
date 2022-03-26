<?php
namespace App\Handler;
use App\Models\User;

use \Spatie\WebhookClient\ProcessWebhookJob;
use Http;
use Session;

use App\Models\OAuthData;
use App\Models\ModTemplate;
use App\Models\ChatMessage;
use App\Models\UserExtraQuota;
use App\Models\UserAddon;
use App\Models\BotPlus;
use App\Models\ChatDialog;
use App\Models\ContactLabel;
use App\Models\Category;
use App\Models\Variable;
use App\Models\ModNotificationReport;
use App\Events\ChatLabelStatus;
use App\Events\BotMessage;

use Throwable;

class SallaWebhook extends ProcessWebhookJob{
	public function handle(){
	    $data = json_decode($this->webhookCall, true);
	    $allData = $data['payload'];
		
		$tenantUser = User::first();
		$tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$tenantUser->global_id)->first();
		$userObj = \DB::connection('main')->table('domains')->where('tenant_id',$tenantObj->tenant_id)->first();


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

        $disabled = UserAddon::getDeactivated($tenantUser->id);
        $dis = 0;
        if(in_array(5,$disabled)){
            $dis = 1;
        }

        $disBotPlus = 0;
        if(in_array(10,$disabled)){
            $disBotPlus = 1;
        }

	    $mainWhatsLoopObj = new \MainWhatsLoop();

	    // If New Webhook
	    if(!empty($allData) && !$dis){
	    	$mainData = $allData['data'];
	    	// IF App Connected To Salla Account
			if($allData['event'] == 'app.store.authorize'){
				$oauthDataObj = OAuthData::where('type','salla')->where('user_id',$tenantUser->id)->first();
				if(!$oauthDataObj){
					$oauthDataObj = new OAuthData;
					$oauthDataObj->created_at = date('Y-m-d H:i:s');
				}else{
					$oauthDataObj->updated_at = date('Y-m-d H:i:s');
				}
				$oauthDataObj->type = 'salla';
				$oauthDataObj->user_id = $tenantUser->id;
				$oauthDataObj->tenant_id = $tenantObj->tenant_id;
				$oauthDataObj->phone = $tenantUser->phone;
				$oauthDataObj->domain = $tenantUser->domain;
				$oauthDataObj->access_token = $mainData['access_token'];
				$oauthDataObj->token_type = $mainData['token_type'];
				$oauthDataObj->expires_in = $mainData['expires'];
				$oauthDataObj->refresh_token = $mainData['refresh_token'];
				$oauthDataObj->authorization = $mainData['access_token'];
				$oauthDataObj->save();
			}

	  		// IF Customer Data
	    	if($allData['event'] == 'customer.updated' || $allData['event'] == 'customer.created'){
	    		// Customer (Create / Update)
	    		$customerObj = \DB::table('salla_customers')->where('id',$mainData['id'])->first();
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
	    		if(!$customerObj){
                	\DB::table('salla_customers')->insert($dataObj);
	    			$templateObj = ModTemplate::NotDeleted()->where('status',1)->where('mod_id',1)->where('statusText','ترحيب بالعميل')->first();
		    		if($templateObj){
		    			$content = $templateObj->content_ar;
		    			$content = str_replace('{CUSTOMERNAME}', $mainData['first_name'].' '.$mainData['last_name'], $content);
		    			$content = str_replace('{STORENAME}', $tenantUser->company, $content);

		    			$message_type = 'text';
						$whats_message_type = 'chat';
	    				$sendData['body'] = $content;
		    			$sendData['chatId'] = str_replace('+', '', $mainData['mobile_code'].$mainData['mobile']).'@c.us';
		    			
		    			$checkObj = ModNotificationReport::where('mod_id',1)->where('client',$sendData['chatId'])->where('statusText','ترحيب بالعميل')->first();
		    			if(!$checkObj){
		    				if($templateObj->type == 1){
			    				$result = $mainWhatsLoopObj->sendMessage($sendData);

				    			if(isset($result['data']) && isset($result['data']['id'])){
						            $messageId = $result['data']['id'];
						            $lastMessage['status'] = 'APP';
						            $lastMessage['id'] = $messageId;
						            $lastMessage['chatId'] = $sendData['chatId'];
						            $lastMessage['fromMe'] = 1;
						            $lastMessage['message_type'] = $message_type;
						            $lastMessage['type'] = $whats_message_type;
						            $lastMessage['time'] = time();
						            $lastMessage['sending_status'] = 1;
						            $lastMessage['module_id'] = 5;
					            	$lastMessage['module_status'] = 'ترحيب بالعميل';
					            	$lastMessage['module_order_id'] = '';
			        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
			        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
			        				ChatMessage::newMessage($lastMessage);
						        }
						    }else{
						    	$botObjs = BotPlus::find($templateObj->type);
		    					$botObj = BotPlus::getData($botObjs);
				    			$this->handleBotPlus($disBotPlus,$mainData,null,$botObj,$userObj->domain,$sendData['chatId'],$tenantUser->company,'ترحيب بالعميل');
						    }

						    if($templateObj->category_id != null){
		    					$categoryObj = Category::find($templateObj->category_id);
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
							        broadcast(new ChatLabelStatus($userObj->domain, ChatDialog::getData(ChatDialog::getOne($labelData['liveChatId'])) , Category::getData($categoryObj) , 1 ));
						        }
		    				}

		    				if($templateObj->moderator_id != null){
		    					$modObj = User::find($templateObj->moderator_id);
		    					if($modObj){
							        $dialogObj = ChatDialog::getOne($sendData['chatId']);
							        $modArrs = $dialogObj->modsArr;
							        if($modArrs == null){
							            $dialogObj->modsArr = serialize([$templateObj->moderator_id]);
							            $dialogObj->save();
							        }else{
							            $oldArr = unserialize($dialogObj->modsArr);
							            if(!in_array($templateObj->moderator_id, $oldArr)){
							                array_push($oldArr, $templateObj->moderator_id);
							                $dialogObj->modsArr = serialize($oldArr);
							                $dialogObj->save();
							            }
							        }
		    					}
		    				}

						    return ModNotificationReport::create([
	        					'mod_id' => 1,
	        					'client' => $sendData['chatId'],
	        					'statusText' => 'ترحيب بالعميل',
	        					'created_at' => date('Y-m-d H:i:s'),
	        				]);
		    			}
		    			
		    		}
	    		}else{
                	return \DB::table('salla_customers')->where('id',$dataObj[0]['id'])->update($dataObj[0]);
	    		}
	    	}

	    	// IF Product Data
	    	if($allData['event'] == 'product.updated' || $allData['event'] == 'product.created'){
	    		// Product (Create / Update)
	    		$productObj = \DB::table('salla_products')->where('id',$mainData['id'])->first();
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
	    		if(!$productObj){
	    		    unset($dataObj['tags']);
                	return \DB::table('salla_products')->insert($dataObj);
	    		}else{
	    		    unset($dataObj[0]['tags']);
                	return \DB::table('salla_products')->where('id',$dataObj[0]['id'])->update($dataObj[0]);
	    		}
	    	}

	    	// IF Order Data
	    	if($allData['event'] == 'order.updated' || $allData['event'] == 'order.created'){
	    		// Order Update
	    		$status = $mainData['status']['name'];

	    		$templateObj = ModTemplate::NotDeleted()->where('status',1)->where('mod_id',1)->where('statusText',$status)->first();
	    		if($templateObj){
	    			$content = $templateObj->content_ar;
	    			$content = str_replace('{CUSTOMERNAME}', $mainData['customer']['first_name'].' '.$mainData['customer']['last_name'], $content);
	    			$content = str_replace('{STORENAME}', $tenantUser->company, $content);
	    			$content = str_replace('{ORDERID}', $mainData['reference_id'], $content);
	    			$content = str_replace('{ORDERSTATUS}', $status, $content);

	    			$message_type = 'text';
					$whats_message_type = 'chat';
    				$sendData['body'] = $content;
	    			$sendData['chatId'] = str_replace('+', '', $mainData['customer']['mobile_code'].$mainData['customer']['mobile']).'@c.us';
	    			$checkObj = ModNotificationReport::where('mod_id',1)->where('client',$sendData['chatId'])->where('order_id',$mainData['reference_id'])->where('statusText',$status)->first();
	    			if(!$checkObj){
	    				if($templateObj->type == 1){
	    					$result = $mainWhatsLoopObj->sendMessage($sendData);
			    			if(isset($result['data']) && isset($result['data']['id'])){
					            $messageId = $result['data']['id'];
					            $lastMessage['status'] = 'APP';
					            $lastMessage['id'] = $messageId;
					            $lastMessage['chatId'] = $sendData['chatId'];
					            $lastMessage['fromMe'] = 1;
					            $lastMessage['message_type'] = $message_type;
					            $lastMessage['type'] = $whats_message_type;
					            $lastMessage['time'] = time();
					            $lastMessage['sending_status'] = 1;
					            $lastMessage['module_id'] = 5;
					            $lastMessage['module_status'] = $status;
					            $lastMessage['module_order_id'] = $mainData['id'];
		        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
		        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
					            ChatMessage::newMessage($lastMessage);
					        }
	    				}else{
	    					$botObjs = BotPlus::find($templateObj->type);
	    					$botObj = BotPlus::getData($botObjs);
			    			$this->handleBotPlus($disBotPlus,$mainData,$status,$botObj,$userObj->domain,$sendData['chatId'],$tenantUser->company,$status);
	    				}

	    				if($templateObj->category_id != null){
	    					$categoryObj = Category::find($templateObj->category_id);
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
						        broadcast(new ChatLabelStatus($userObj->domain, ChatDialog::getData(ChatDialog::getOne($labelData['liveChatId'])) , Category::getData($categoryObj) , 1 ));
					        }
	    				}

	    				if($templateObj->moderator_id != null){
	    					$modObj = User::find($templateObj->moderator_id);
	    					if($modObj){
						        $dialogObj = ChatDialog::getOne($sendData['chatId']);
						        $modArrs = $dialogObj->modsArr;
						        if($modArrs == null){
						            $dialogObj->modsArr = serialize([$templateObj->moderator_id]);
						            $dialogObj->save();
						        }else{
						            $oldArr = unserialize($dialogObj->modsArr);
						            if(!in_array($templateObj->moderator_id, $oldArr)){
						                array_push($oldArr, $templateObj->moderator_id);
						                $dialogObj->modsArr = serialize($oldArr);
						                $dialogObj->save();
						            }
						        }
	    					}
	    				}

	    				ModNotificationReport::create([
        					'mod_id' => 1,
        					'client' => $sendData['chatId'],
        					'order_id' => $mainData['reference_id'],
        					'statusText' => $status,
        					'created_at' => date('Y-m-d H:i:s'),
        				]);
	    			}
	    			
	    		}

	    		$orderObj = \DB::table('salla_orders')->where('id',$mainData['id'])->first();
	    		foreach ($mainData as $key => $value) {
	    			if($key == 'amounts'){
	    				$mainData['total'] = $mainData['amounts']['total'];
	    			}
	    			if(!in_array($key, ['id','reference_id','total','date','status','can_cancel','items'])){
	    				unset($mainData[$key]);
	    			}
	    		}
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
	    		if(!$orderObj){
                	return \DB::table('salla_orders')->insert($dataObj);
	    		}else{
                	return \DB::table('salla_orders')->where('id',$dataObj[0]['id'])->update($dataObj[0]);;
	    		}
	    	}

	    	// IF Order Shipment Return
	    	
	    	if($allData['event'] == 'order.shipment.return.creating'){
	    		$shipment_policy = 0;
		    	$templateObj = ModTemplate::NotDeleted()->where('status',1)->where('mod_id',1)->where('statusText','مسترجع')->first();
		    	if($templateObj && $templateObj->shipment_policy == 1){
		    		$shipment_policy = 1;
		    	}

	    		if($shipment_policy == 1){
	    			$shipmentData = $mainData['shipping']['shipment'];

	    			$message_type = 'text';
					$whats_message_type = 'chat';
    				$sendData['body'] = $shipmentData['tracking_link'];
	    			$sendData['chatId'] = str_replace('+', '', @$mainData['customer']['mobile_code'].$mainData['customer']['mobile']).'@c.us';
	    			$result = $mainWhatsLoopObj->sendMessage($sendData);
	    			if(isset($result['data']) && isset($result['data']['id'])){
			            $messageId = $result['data']['id'];
			            $lastMessage['status'] = 'APP';
			            $lastMessage['id'] = $messageId;
			            $lastMessage['chatId'] = $sendData['chatId'];
			            $lastMessage['fromMe'] = 1;
			            $lastMessage['message_type'] = $message_type;
			            $lastMessage['type'] = $whats_message_type;
			            $lastMessage['time'] = time();
			            $lastMessage['sending_status'] = 1;
			            $lastMessage['module_id'] = 5;
        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
			            ChatMessage::newMessage($lastMessage);
			        }

			        $sendData['body'] = $shipmentData['label']['url'];
	    			$result2 = $mainWhatsLoopObj->sendMessage($sendData);
	    			if(isset($result2['data']) && isset($result2['data']['id'])){
			            $messageId = $result2['data']['id'];
			            $lastMessage['status'] = 'APP';
			            $lastMessage['id'] = $messageId;
			            $lastMessage['chatId'] = $sendData['chatId'];
			            $lastMessage['fromMe'] = 1;
			            $lastMessage['message_type'] = $message_type;
			            $lastMessage['type'] = $whats_message_type;
			            $lastMessage['time'] = time();
			            $lastMessage['sending_status'] = 1;
			            $lastMessage['module_id'] = 5;
        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
			            ChatMessage::newMessage($lastMessage);
			        }
	    		}
	    	}
	    }

	}

	public function handleBotPlus($disBotPlus,$mainData,$status=null,$botObj,$domain,$sender,$company,$module_status){
		$buttons = '';

	    $mainWhatsLoopObj = new \MainWhatsLoop();
        if(isset($botObj->buttonsData) && !empty($botObj->buttonsData) && !$disBotPlus){
    		foreach($botObj->buttonsData as $key => $oneItem){
    			$buttons.= $oneItem['text'].( $key == $botObj->buttons -1 ? '' : ',');
    		}

    		$body = $botObj->body;
    		$order_id = null;
    		if($status != null){
    			$body = str_replace('{CUSTOMERNAME}', $mainData['customer']['first_name'].' '.$mainData['customer']['last_name'], $body);
    			$body = str_replace('{STORENAME}', $company, $body);
    			$body = str_replace('{ORDERID}', $mainData['reference_id'], $body);
    			$body = str_replace('{ORDERSTATUS}', $status, $body);
    			$order_id = $mainData['id'];
    		}else{
    			$body = str_replace('{CUSTOMERNAME}', $mainData['first_name'].' '.$mainData['last_name'], $body);
		    	$body = str_replace('{STORENAME}', $company, $body);
    		}
    		
    		$sendData['body'] = $body;
    		$sendData['title'] = $botObj->title;
    		$sendData['footer'] = $botObj->footer;
    		$sendData['buttons'] = $buttons;
    		$sendData['chatId'] = str_replace('@c.us','',$sender);
    		$result = $mainWhatsLoopObj->sendButtons($sendData);
    		$sendData['chatId'] = $sender;
            $this->handleRequest($domain,$result,$sendData,'BOT PLUS','text','chat','BotMessage',$botObj,$module_status,$order_id);
        }
	}

	public function handleRequest($domain,$result,$sendData,$status,$message_type,$whats_message_type,$channel,$botObj=null,$module_status,$order_id=null){
		if(isset($result['data']) && isset($result['data']['id'])){
            $checkMessageObj = ChatMessage::where('chatId',$sendData['chatId'])->where('chatName','!=',null)->orderBy('messageNumber','DESC')->orderBy('time','DESC')->first();
            $messageId = $result['data']['id'];
            $lastMessage['status'] = 'BOT PLUS';
            $lastMessage['id'] = $messageId;
            $lastMessage['fromMe'] = 1;
            $lastMessage['chatId'] = $sendData['chatId'];
            $lastMessage['time'] = strtotime(date('Y-m-d H:i:s'));
            $lastMessage['body'] = $sendData['body'];
            $lastMessage['messageNumber'] = $checkMessageObj != null && $checkMessageObj->messageNumber != null ? $checkMessageObj->messageNumber+1 : 1;
            $lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
            $lastMessage['message_type'] = $message_type;
            $lastMessage['sending_status'] = 2;
            $lastMessage['type'] = $whats_message_type;
            $lastMessage['module_id'] = 5;
			$lastMessage['module_status'] = $module_status;
			$lastMessage['module_order_id'] = $order_id;
            $messageObj = ChatMessage::newMessage($lastMessage);
            $dialog = ChatDialog::getOne($sendData['chatId']);
            $dialog->last_time = $lastMessage['time'];
            $dialogObj = ChatDialog::getData($dialog);
            $dialogObj->lastMessage = $messageObj;
        	$dialogObj->lastMessage->bot_details = $botObj;
			broadcast(new BotMessage($domain , $dialogObj));
        }
	}
	
	public function failed(Throwable $exception){
        $count = \DB::connection('main')->table('failed_jobs')->count();
        if($count > 10){
        	\DB::connection('main')->table('failed_jobs')->truncate();
        }
        system('/usr/local/bin/php /home/wloop/public_html/artisan queue:restart');
        // system('/home/wloop/public_html/vendor/supervisorctl restart whatsloop:*');
    }
}