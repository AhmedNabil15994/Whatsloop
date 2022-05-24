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

use App\Models\CentralChannel;
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
	    			}
	    			
    				return ModNotificationReport::create([
    					'mod_id' => 1,
    					'client' => $sendData['chatId'],
    					'order_id' => $mainData['reference_id'],
    					'statusText' => $status,
    					'created_at' => date('Y-m-d H:i:s'),
    				]);
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

	    	// IF Order Status Data Updated
	    	if($allData['event'] == 'order.status.updated'){
	    		// Order Status Update
	    		$status = $mainData['order']['status']['name'];
	    		$checkObj = ModNotificationReport::where('mod_id',1)->where('order_id',$mainData['order']['reference_id'])->where('statusText',$status)->first();
	    		if(!$checkObj){
	    			$templateObj = ModTemplate::NotDeleted()->where('status',1)->where('mod_id',1)->where('statusText',$status)->first();
					if($templateObj){
		    			$content = $templateObj->content_ar;
		    			$content = str_replace('{STORENAME}', $tenantUser->company, $content);
		    			$content = str_replace('{ORDERID}', $mainData['order']['reference_id'], $content);
		    			$content = str_replace('{ORDERSTATUS}', $status, $content);

		    			$message_type = 'text';
						$whats_message_type = 'chat';
		    			$checkObjForCustomer = ModNotificationReport::where('mod_id',1)->where('order_id',$mainData['order']['reference_id'])->first();
		    			if($checkObjForCustomer){
		    				$customer = $checkObjForCustomer->client;
		    				$mobile = '+'.str_replace('@c.us','',$customer);
							$phoneNumber = preg_replace("/^\+(?:998|996|995|994|993|992|977|976|975|974|973|972|971|970|968|967|966|965|964|963|962|961|960|886|880|856|855|853|852|850|692|691|690|689|688|687|686|685|683|682|681|680|679|678|677|676|675|674|673|672|670|599|598|597|595|593|592|591|590|509|508|507|506|505|504|503|502|501|500|423|421|420|389|387|386|385|383|382|381|380|379|378|377|376|375|374|373|372|371|370|359|358|357|356|355|354|353|352|351|350|299|298|297|291|290|269|268|267|266|265|264|263|262|261|260|258|257|256|255|254|253|252|251|250|249|248|246|245|244|243|242|241|240|239|238|237|236|235|234|233|232|231|230|229|228|227|226|225|224|223|222|221|220|218|216|213|212|211|98|95|94|93|92|91|90|86|84|82|81|66|65|64|63|62|61|60|58|57|56|55|54|53|52|51|49|48|47|46|45|44\D?1624|44\D?1534|44\D?1481|44|43|41|40|39|36|34|33|32|31|30|27|20|7|1\D?939|1\D?876|1\D?869|1\D?868|1\D?849|1\D?829|1\D?809|1\D?787|1\D?784|1\D?767|1\D?758|1\D?721|1\D?684|1\D?671|1\D?670|1\D?664|1\D?649|1\D?473|1\D?441|1\D?345|1\D?340|1\D?284|1\D?268|1\D?264|1\D?246|1\D?242|1)\D?/", '',$mobile);
							$customerObj = \DB::table('salla_customers')->where('mobile_code',str_replace($phoneNumber,'',$mobile))->where('mobile',$phoneNumber)->first();
							if($customerObj){
			    				$content = str_replace('{CUSTOMERNAME}', $customerObj->first_name.' '.$customerObj->last_name, $content);
			    				$sendData['chatId'] = str_replace('+', '', $customer);
	    						$sendData['body'] = $content;

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
							            $lastMessage['module_order_id'] = $mainData['order']['id'];
				        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
				        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
							            ChatMessage::newMessage($lastMessage);
							        }
			    				}else{
			    					$botObjs = BotPlus::find($templateObj->type);
			    					$botObj = BotPlus::getData($botObjs);
					    			$this->handleBotPlus($disBotPlus,$mainData['order'],$status,$botObj,$userObj->domain,$sendData['chatId'],$tenantUser->company,$status);
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

			    				$oldReportObj = ModNotificationReport::where('mod_id',1)->where('order_id',$mainData['order']['reference_id'])->where('statusText','new')->first();
			    				if($oldReportObj){
			    					ModNotificationReport::where('mod_id',1)->where('order_id',$mainData['order']['reference_id'])->where('statusText','new')->delete();
			    				}
		    				    return ModNotificationReport::create([
		        					'mod_id' => 1,
		        					'client' => $sendData['chatId'],
		        					'order_id' => $mainData['order']['reference_id'],
		        					'statusText' => $status,
		        					'created_at' => date('Y-m-d H:i:s'),
		        				]);
							}
		    			}
		    			
		    		}
	    		}

	    		$orderObj = \DB::table('salla_orders')->where('id',$mainData['order']['id'])->first();
	    		$orderData = $mainData['order'];
	    		foreach ($orderData as $key => $value) {
	    			if($key == 'amounts'){
	    				$orderData['total'] = $orderData['amounts']['total'];
	    			}
	    			if(!in_array($key, ['id','reference_id','total','date','status','can_cancel','items'])){
	    				unset($orderData[$key]);
	    			}
	    		}
	    		$dataObj = \ExternalServices::reformatModelData([$orderData]);
                return \DB::table('salla_orders')->where('id',$dataObj[0]['id'])->update($dataObj[0]);	
	    	}

	    	// IF Abandoned Cart Data
	    	if($allData['event'] == 'abandoned.cart'){
	    		// Abandoned Cart (Create)
	    		$cartObj = \DB::table('salla_abandonedCarts')->where('id',$mainData['id'])->first();
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
	    		if(!$cartObj){
                	\DB::table('salla_abandonedCarts')->insert($dataObj);
	    			$templateObj = ModTemplate::NotDeleted()->where('status',1)->where('mod_id',1)->where('statusText','سلة متروكة')->first();
		    		if($templateObj){
		    			$content = $templateObj->content_ar;
		    			$content = str_replace('{STORENAME}', $tenantUser->company, $content);
		    			$content = str_replace('{CUSTOMERNAME}', $mainData['customer']['name'], $content);
		    			$content = str_replace('{ORDERID}', $mainData['id'], $content);
		    			$content = str_replace('{ORDERTOTAL}', round($mainData['total']['amount'],2) . $mainData['total']['currency'], $content);
		    			$content = str_replace('{ORDERURL}', $mainData['checkout_url'], $content);

		    			$message_type = 'text';
						$whats_message_type = 'chat';
	    				$sendData['body'] = $content;
		    			$sendData['chatId'] = str_replace('+', '', $mainData['customer']['mobile']).'@c.us';
		    			
		    			$checkObj = ModNotificationReport::where('mod_id',1)->where('order_id',$mainData['id'])->where('client',$sendData['chatId'])->where('statusText','سلة متروكة')->first();
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
					            	$lastMessage['module_status'] = 'سلة متروكة';
					            	$lastMessage['module_order_id'] = '';
			        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
			        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
			        				ChatMessage::newMessage($lastMessage);
						        }
						    }else{
						    	$botObjs = BotPlus::find($templateObj->type);
		    					$botObj = BotPlus::getData($botObjs);
				    			$this->handleBotPlus($disBotPlus,$mainData,null,$botObj,$userObj->domain,$sendData['chatId'],$tenantUser->company,'سلة متروكة');
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
	        					'client' => $sendData['chatId'].'@c.us',
	        					'order_id' => $mainData['id'],
	        					'statusText' => 'سلة متروكة',
	        					'created_at' => date('Y-m-d H:i:s'),
	        				]);
		    			}
		    			
		    		}
	    		}else{
                	return \DB::table('salla_abandonedCarts')->where('id',$dataObj[0]['id'])->update($dataObj[0]);
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
    			if($module_status == 'سلة متروكة'){
    				$body = str_replace('{ORDERID}', $mainData['id'], $body);
	    			$body = str_replace('{ORDERTOTAL}', round($mainData['total']['amount'],2) . $mainData['total']['currency'], $body);
	    			$body = str_replace('{ORDERURL}', $mainData['checkout_url'], $body);
    			}
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
        system('/usr/local/bin/php /home/wloop/public_html/artisan queue:restart');
        // system('/home/wloop/public_html/vendor/supervisorctl restart whatsloop:*');
    }
}