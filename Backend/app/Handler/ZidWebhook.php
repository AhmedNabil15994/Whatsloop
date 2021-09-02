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
        if(in_array(5,$disabled)){
            $dis = 1;
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

	    		$templateObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('statusText',$status)->first();
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

}