<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CentralUser;
use App\Models\UserAddon;
use App\Models\CentralChannel;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variable;
use App\Models\OAuthData;
use App\Models\CentralVariable;

class PushAddonSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:addonSetting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Addons Settings';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   

        $userAddons = UserAddon::NotDeleted()->whereIn('addon_id',[4,5])->where('setting_pushed',0)->where('status',1)->get();
        foreach($userAddons as $userAddon){
            if($userAddon->Client){
                $domain = CentralUser::getDomain($userAddon->Client);
                $domainObj = Domain::where('domain',$domain)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                $centralUserObj = CentralUser::getData($userAddon->Client);
                
                if($userAddon->addon_id == 4){
                    // Zid Webhooks
                    $webhookUrl = str_replace('://', '://'.$domain.'.', config('app.BASE_URL')).'/whatsloop/webhooks/zid-webhook';
                    $actions = ['order.create','order.status.update','product.create','product.update','product.publish','product.delete'];
                    
                    tenancy()->initialize($tenant);
                    $url = CentralVariable::getVar('ZidURL').'/managers/webhooks';
                    $storeId = Variable::getVar('ZidStoreID');
                    $managerToken = Variable::getVar('ZidStoreToken');
                    $merchantToken = CentralVariable::getVar('ZidMerchantToken');
                    tenancy()->end($tenant);
                    
                    foreach($actions as $key => $action){
                        $urlData = [
                            'event' => $action,
                            'target_url' => $webhookUrl,
                            'original_id' => 610 + $key,
                            'subscriber' => ucwords(str_replace('.',' ',$action)).' Notify',
                            'conditions' => "{}",
                        ];
                        $payload = json_encode($urlData);
    
    
                        if($storeId && $managerToken){
    
                            $ch = curl_init($url);
    
                            curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload ); 
                            curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
                                'Content-Type: application/json', 
                                'X-MANAGER-TOKEN: '.$managerToken,
                                'STORE-ID: '.$storeId,
                                'ROLE: Manager',
                                'User-Agent: whatsloop/1.00.00 (web)',
                                'Accept-Language: en',
                                'Authorization: Bearer '.$merchantToken,
                            )); 
                            curl_setopt($ch, CURLOPT_POST,           1 );
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
                            $result=curl_exec ($ch);   
                            $userAddon->setting_pushed = 1;
                            $userAddon->save();   
                        }
                    }
    
                }elseif($userAddon->addon_id == 5){
                    // Salla Webhooks
                    $webhookUrl = 'https://'.$domain.'.wloop.com'.'/whatsloop/webhooks/salla-webhook';
                    $actions = ['order.created','order.updated','product.created','product.updated','customer.created','customer.updated'];
    
                    tenancy()->initialize($tenant);
                    $url = CentralVariable::getVar('SallaURL').'/webhooks/subscribe';
                    $managerToken = Variable::getVar('SallaStoreToken');
                    tenancy()->end($tenant);
                    $oauthDataObj = OAuthData::where('type','salla')->where('user_id',$userAddon->user_id)->first();
                
                    foreach($actions as $key => $action){
                        $urlData = [
                            'name' => ucwords(str_replace('.',' ',$action)).' Notify',
                            'event' => $action,
                            'url' => $webhookUrl,
                            'headers' => [
                                [
                                    'key' => 'Authorization',
                                    'value' => $managerToken,
                                ],
                                [
                                    'key' => 'Accept-Language',
                                    'value' => "AR",
                                ]
                            ],
                        ];
                        if($oauthDataObj){
                            $managerToken = $oauthDataObj->access_token;
                            $urlData['headers'] = [

                            ];
                        }
    
                        $payload = json_encode($urlData);
    
                        if($managerToken){

                            $ch = curl_init($url);
    
                            curl_setopt($ch, CURLOPT_POSTFIELDS,     $payload ); 
                            curl_setopt($ch, CURLOPT_HTTPHEADER,     array(
                                'Content-Type: application/json', 
                                'Authorization: Bearer '.$managerToken,
                            )); 
                            curl_setopt($ch, CURLOPT_POST,           1 );
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
                            $result=curl_exec ($ch);  
                            $userAddon->setting_pushed = 1;
                            $userAddon->save();   
                        }
                    }
                }   
            }
        }
    }
}
