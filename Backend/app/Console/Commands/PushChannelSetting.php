<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CentralUser;
use App\Models\CentralChannel;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;

class PushChannelSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:channelSetting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push Channel Settings';

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

        $users = CentralUser::NotDeleted()->where('group_id',0)->where('setting_pushed',0)->where('status',1)->get();
        foreach($users as $user){
            $domain = CentralUser::getDomain($user);
            $channelObj = CentralChannel::where('global_user_id',$user->global_id)->first();
            if($channelObj){
                $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
                $myData = [
                    'sendDelay' => '0',
                    'webhookUrl' => str_replace('://', '://'.$domain.'.', config('app.BASE_URL')).'/whatsloop/webhooks/messages-webhook',
                    'instanceStatuses' => 1,
                    'webhookStatuses' => 1,
                    'statusNotificationsOn' => 1,
                    'ackNotificationsOn' => 1,
                    'chatUpdateOn' => 1,
                    'ignoreOldMessages' => 1,
                    'videoUploadOn' => 1,
                    'guaranteedHooks' => 1,
                    'parallelHooks' => 1,
                ];
                
                $updateResult = $mainWhatsLoopObj->postSettings($myData);
                $result = $updateResult->json();

                if($result['status']['status'] == 1){
                    $user->setting_pushed = 1;
                    $user->save();
                    
                    $domainObj = Domain::where('domain',$domain)->first();
                    $tenant = Tenant::find($domainObj->tenant_id);
                    
                    tenancy()->initialize($tenant);

                    $tenantUserObj = User::first();
                    $tenantUserObj->setting_pushed = 1;
                    $tenantUserObj->save();
                    
                    tenancy()->end($tenant);
                }
            }
        }
    }
}
