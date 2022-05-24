<?php
namespace App\Handler;
use App\Models\User;

use \Spatie\WebhookClient\ProcessWebhookJob;
use Http;
use Session;
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

class ZidWebhook extends ProcessWebhookJob{
	public function handle(){
	    $data = json_decode($this->webhookCall, true);
	    $mainData = $data['payload'];

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
        if($dailyCount + $extraQuotas <= $messagesCount ){
            return 1;
        }

        $disabled = UserAddon::getDeactivated($tenantUser->id);
        $dis = 0;
        if(in_array(4,$disabled)){
            $dis = 1;
        }

        $disBotPlus = 0;
        if(in_array(10,$disabled)){
            $disBotPlus = 1;
        }

	    $mainWhatsLoopObj = new \MainWhatsLoop();

	    // If New Webhook
	    if(!empty($mainData) && !$dis){
	    	// Project (Delete)
	    	if(isset($mainData['product_id']) && isset($mainData['deleted_at'])){
	    		return \DB::table('zid_products')->where('id',$mainData['product_id'])->delete();
	    	}
	    	if(isset($mainData['sku'])){
	    		// Project (Create / Update / Publish)
	    		$productObj = \DB::table('zid_products')->where('id',$mainData['id'])->first();
	    		unset($mainData['variants']);
				unset($mainData['custom_user_input_fields']);
				unset($mainData['custom_option_fields']);
				unset($mainData['options']);
				unset($mainData['related_products']);
				unset($mainData['description']);
				unset($mainData['event_extra_data']);
				unset($mainData['purchase_restrictions']);
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
	    		if(!$productObj){
                	return \DB::table('zid_products')->insert($dataObj);
	    		}else{
                	return \DB::table('zid_products')->where('id',$dataObj[0]['id'])->update($dataObj[0]);
	    		}
	    	}

	    	if(isset($mainData['order_url']) && isset($mainData['order_status'])){
	    		// Order Status Updated
	    		$status = $mainData['order_status']['name'];
                if($status == 'تجهيز'){
	    			$status = 'جاري التجهيز';
	    		}else if($status == 'جاري التوصيل'){
	    			$status = 'جارى التوصيل';
	    		}else if($status == 'تم الإلغاء'){
	    		    $status = 'تم الالغاء';
	    		}
	    		$templateObj = ModTemplate::NotDeleted()->where('status',1)->where('mod_id',2)->where('statusText',$status)->first();
	    		if($templateObj){
	    			$content = $templateObj->content_ar;
	    			$content = str_replace('{CUSTOMERNAME}', $mainData['customer']['name'], $content);
	    			$content = str_replace('{STORENAME}', $mainData['store_name'], $content);
	    			$content = str_replace('{ORDERID}', $mainData['id'], $content);
	    			$content = str_replace('{ORDERSTATUS}', $status, $content);
	    			$content = str_replace('{ORDER_URL}', $mainData['order_url'], $content);

	    			$message_type = 'text';
					$whats_message_type = 'chat';
    				$sendData['body'] = $content;
	    			$sendData['chatId'] = str_replace('+', '', $mainData['customer']['mobile']).'@c.us';
	    			$checkObj = ModNotificationReport::where('mod_id',2)->where('client',$sendData['chatId'])->where('order_id',$mainData['id'])->where('statusText',$status)->first();
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
					            $lastMessage['module_id'] = 4;
					            $lastMessage['module_status'] = $status;
					            $lastMessage['module_order_id'] = $mainData['id'];
		        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
		        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
					            ChatMessage::newMessage($lastMessage);
					        }
	    				}else{
	    					$botObjs = BotPlus::find($templateObj->type);
	    					$botObj = BotPlus::getData($botObjs);
			    			$this->handleBotPlus($disBotPlus,$mainData,$status,$botObj,$userObj->domain,$sendData['chatId']);
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
        					'mod_id' => 2,
        					'client' => $sendData['chatId'],
        					'order_id' => $mainData['id'],
        					'statusText' => $status,
        					'created_at' => date('Y-m-d H:i:s'),
        				]);
	    			}
	    		}

	    		$orderObj = \DB::table('zid_orders')->where('id',$mainData['id'])->first();
	    		foreach ($mainData as $key => $value) {
	    			if(!in_array($key, ['id','code','store_id','order_url','store_name','store_url','order_status','customer','has_different_consignee','order_total','order_total_string','created_at','updated_at','requires_shipping','shipping','payment'])){
	    				unset($mainData[$key]);
	    			}
	    		}
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
	    		if(!$orderObj){
                	return \DB::table('zid_orders')->insert($dataObj);
	    		}else{
                	return \DB::table('zid_orders')->where('id',$dataObj[0]['id'])->update($dataObj[0]);;
	    		}


	    		$orderObj = \DB::table('zid_orders')->where('id',$mainData['id'])->first();
	    		unset($mainData['customer_note']);
	    		unset($mainData['transaction_reference']);
	    		unset($mainData['weight']);
	    		unset($mainData['weight_cost_details']);
	    		unset($mainData['currency']);
	    		unset($mainData['coupon']);
	    		unset($mainData['products']);
	    		unset($mainData['products_count']);
	    		unset($mainData['histories']);
	    		unset($mainData['return_policy']);
	    		$dataObj = \ExternalServices::reformatModelData([$mainData]);
               	\DB::table('zid_orders')->where('id',$dataObj[0]['id'])->update($dataObj[0]);
	    	}

	    }

	}

	public function handleBotPlus($disBotPlus,$mainData,$status,$botObj,$domain,$sender){
		$buttons = '';
	    $mainWhatsLoopObj = new \MainWhatsLoop();
        if(isset($botObj->buttonsData) && !empty($botObj->buttonsData) && !$disBotPlus){
    		foreach($botObj->buttonsData as $key => $oneItem){
    			$buttons.= $oneItem['text'].( $key == $botObj->buttons -1 ? '' : ',');
    		}

    		$body = $botObj->body;
			$body = str_replace('{CUSTOMERNAME}', $mainData['customer']['name'], $body);
			$body = str_replace('{STORENAME}', $mainData['store_name'], $body);
			$body = str_replace('{ORDERID}', $mainData['id'], $body);
			$body = str_replace('{ORDERSTATUS}', $status, $body);
			$body = str_replace('{ORDER_URL}', $mainData['order_url'], $body);

    		$sendData['body'] = $body;
    		$sendData['title'] = $botObj->title;
    		$sendData['footer'] = $botObj->footer;
    		$sendData['buttons'] = $buttons;
    		$sendData['chatId'] = str_replace('@c.us','',$sender);
    		$result = $mainWhatsLoopObj->sendButtons($sendData);
    		$sendData['chatId'] = $sender;
            $this->handleRequest($domain,$result,$sendData,'BOT PLUS','text','chat','BotMessage',$botObj,$status,$mainData['id']);
        }
	}

	public function handleRequest($domain,$result,$sendData,$status,$message_type,$whats_message_type,$channel,$botObj=null,$module_status,$module_order_id){
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
            $lastMessage['module_id'] = 4;
			$lastMessage['module_status'] = $module_status;
			$lastMessage['module_order_id'] = $module_order_id;
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