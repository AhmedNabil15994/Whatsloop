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
use App\Models\ModNotificationReport;

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

	    $mainWhatsLoopObj = new \MainWhatsLoop();

	    // If New Webhook
	    if(!empty($allData) && !$dis){
	    	$mainData = $allData['data'];
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
		        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
		        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
		        				ModNotificationReport::create([
		        					'mod_id' => 1,
		        					'client' => $sendData['chatId'],
		        					'statusText' => 'ترحيب بالعميل',
		        					'created_at' => date('Y-m-d H:i:s'),
		        				]);
					            return ChatMessage::newMessage($lastMessage);
					        }
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
	        				$checkMessageObj = ChatMessage::where('fromMe',0)->where('chatId',$sendData['chatId'])->where('chatName','!=',null)->first();
	        				$lastMessage['chatName'] = $checkMessageObj != null ? $checkMessageObj->chatName : '';
				            ChatMessage::newMessage($lastMessage);

				            ModNotificationReport::create([
	        					'mod_id' => 1,
	        					'client' => $sendData['chatId'],
	        					'order_id' => $mainData['reference_id'],
	        					'statusText' => $status,
	        					'created_at' => date('Y-m-d H:i:s'),
	        				]);
				        }
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