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
use App\Models\CentralChannel;
use App\Models\ModNotificationReport;
use App\Events\ChatLabelStatus;
use App\Events\BotMessage;

use Throwable;

class SallaWebhook2 extends ProcessWebhookJob{
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
				$oauthDataObj = OAuthData::where('type','salla')->where('access_token',$mainData['access_token'])->first();
				if(!$oauthDataObj){
					$oauthDataObj = new OAuthData;
					$oauthDataObj->created_at = date('Y-m-d H:i:s');
					$oauthDataObj->type = 'salla';
					$oauthDataObj->user_id = 9999;//$tenantUser->id;
					$oauthDataObj->tenant_id = 'null';//$tenantObj->tenant_id;
					$oauthDataObj->phone = 'null';//$tenantUser->phone;
					$oauthDataObj->domain = 'null';//$tenantUser->domain;
					$oauthDataObj->access_token = $mainData['access_token'];
					$oauthDataObj->token_type = $mainData['token_type'];
					$oauthDataObj->expires_in = $mainData['expires'];
					$oauthDataObj->refresh_token = $mainData['refresh_token'];
					$oauthDataObj->authorization = $mainData['access_token'];
					$oauthDataObj->save();
				}else{
					$oauthDataObj->updated_at = date('Y-m-d H:i:s');
					$oauthDataObj->access_token = $mainData['access_token'];
					$oauthDataObj->token_type = $mainData['token_type'];
					$oauthDataObj->expires_in = $mainData['expires'];
					$oauthDataObj->refresh_token = $mainData['refresh_token'];
					$oauthDataObj->authorization = $mainData['access_token'];
					$oauthDataObj->save();
				}

				$result = Http::withToken($mainData['access_token'])->get('https://accounts.salla.sa/oauth2/user/info');
				$result = $result->json();
				if($result && $result['data']){
					$mobile = $result['data']['mobile'];
					if($result['data']['mobile'] == '+966500000000'){
						$mobile = '+966557722004';
					}
				}
				
				$channelCentral = CentralChannel::first();
				$mainWhatsLoopObjs = new \MainWhatsLoop($channelCentral->instanceId,$channelCentral->instanceToken);

				$sendData['body'] = 'رمز التحقق الخاص ب سلة هو : '.$mainData['access_token'];
		    	$sendData['chatId'] = str_replace('+', '', $mobile).'@c.us';
				$result2 = $mainWhatsLoopObjs->sendMessage($sendData);

				$oauthDataObj->phone = $mobile;
				$oauthDataObj->save();
			}else if($allData['event'] == 'app.settings.updated'){
				$oauthDataObj = OAuthData::where('type','salla')->where('access_token',$mainData['settings']['access_token'])->first();

				if($oauthDataObj){
					$mobile = $oauthDataObj->phone;
				}

				$channelCentral = CentralChannel::first();
				$mainWhatsLoopObjs = new \MainWhatsLoop($channelCentral->instanceId,$channelCentral->instanceToken);

				$sendData['body'] = 'Please visit this link to complete registeration process : '.config('app.BASE_URL').'/welcome/salla/'.base64_encode($oauthDataObj->id);
		    	$sendData['chatId'] = str_replace('+', '', $mobile).'@c.us';
				$result2 = $mainWhatsLoopObjs->sendMessage($sendData);

				$oauthDataObj->phone = $mobile;
				$oauthDataObj->save();
			}
	    }
	}
}