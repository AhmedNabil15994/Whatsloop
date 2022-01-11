<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Tenant;
use App\Models\CentralUser;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Models\UserChannels;
use App\Models\Contact;
use App\Models\Domain;
use App\Models\UserData;
use App\Models\Variable;
use Illuminate\Support\Str;

use App\Jobs\SyncOldClient;

class SyncHugeOld implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $client;
    public $key;
    
    public function __construct($client,$key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {   
        $this->registerTenantData($this->client,$this->key);
    }

    public function registerTenantData($phone,$key){
        // $key = $key * 10000;
        // $tenant = Tenant::create([
        //     'phone' => '+'.$phone,
        //     'title' => 't'.$key,
        //     'description' => '',
        // ]);
        
        // $tenant->domains()->create([
        //     'domain' => 't'.$key,
        // ]);        
        
        $tenant = Tenant::where('phone','+'.$phone)->first();
        $baseUrl = 'https://whatsloop.net/api/v1/';

        // Get User Details
        $mainURL = $baseUrl.'user-details';
        $isOld = 0;

        $data = ['phone' => $phone];
        $result =  \Http::post($mainURL,$data);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] === true){
                // Begin Sync
                $isOld = 1;
            }
        }
        
        $centralUser = CentralUser::where('phone','+'.$phone)->orWhere('phone',$phone)->first();
        // $centralUser = CentralUser::create([
        //     'global_id' => (string) Str::orderedUuid(),
        //     'name' => 't'.$key,
        //     'phone' => '+'.$phone,
        //     'email' => 't'.$key.'@wloop.net',
        //     'company' => 't'.$key,
        //     'password' => \Hash::make('whatsloop1994'),
        //     'notifications' => 0,
        //     'setting_pushed' => 1,
        //     'offers' => 0,
        //     'group_id' => 0,
        //     'is_active' => 1,
        //     'is_approved' => 1,
        //     'status' => 1,
        //     'two_auth' => 0,
        //     'is_old' => $isOld,
        //     'is_synced' => 0,
        // ]);

        // \DB::connection('main')->table('tenant_users')->insert([
        //     'tenant_id' => $tenant->id,
        //     'global_user_id' => $centralUser->global_id,
        // ]);
        
        // $user = $tenant->run(function() use(&$centralUser,$key,$phone){

        //     $userObj = User::create([
        //         'id' => $centralUser->id,
        //         'global_id' => $centralUser->global_id,
        //         'name' => 't'.$key,
        //         'phone' => '+'.$phone,
        //         'email' => 't'.$key.'@wloop.net',
        //         'company' => 't'.$key,
        //         'group_id' => 1,
        //         'status' => 1,
        //         'domain' => 't'.$key,
        //         'is_old' => $centralUser->is_old,
        //         'is_synced' => $centralUser->is_synced,
        //         'two_auth' => 0,
        //         'sort' => 1,
        //         'setting_pushed' => 1,
        //         'password' => \Hash::make('whatsloop1994'),
        //         'is_active' => 1,
        //         'is_approved' => 1,
        //         'notifications' => 0,
        //         'offers' => 0,
        //     ]);

        //     return $userObj;
        // });


        // $domainObj = Domain::getOneByDomain('t'.$key);
        $token = tenancy()->impersonate($tenant,$centralUser->id,'/menu');
        if($isOld){
            $token = tenancy()->impersonate($tenant,$centralUser->id,'/sync');
        
            $baseUrl = 'https://whatsloop.net/api/v1/';

            // Get User Details
            $mainURL = $baseUrl.'user-details';
            $token = '';
            $newEndData = '';
            $doSync = 0;
            $moduleData = [];
            $modules = [];

            $data = ['phone' =>  $phone];
            $result =  \Http::post($mainURL,$data);
            if($result->ok() && $result->json()){
                $data = $result->json();
                if($data['status'] == true){
                    // Begin Sync
                    $doSync = 1;
                    $token = $data['UserData']['JWTToken'];
                    // Get User Instace
                    $mainURL = $baseUrl.'migration/user-instance';
                    $result =  \Http::withToken($token)->get($mainURL);
                    if($result->ok() && $result->json()){
                        $data = $result->json();
                        if($data['status'] == true){
                            $modules = explode(',',$data['data']['Package']['Mod_Sections']);
                            $newEndData = $data['data']['Instance'][0]['EndDate'] != '' ? date('Y-m-d',$data['data']['Instance'][0]['EndDate']) : date('Y-m-d',$data['data']['Instance'][0]['StartDate']);
                        }
                    }

                    foreach($modules as $key){
                        if($key == 'NumbersGroups'){
                            $moduleData[] = 'groupNumbers';
                        }elseif($key == 'BotMsgs'){
                            $moduleData[] = 'bot';
                        }elseif($key == 'Contacts'){
                            $moduleData[] = 'contacts';
                        }elseif($key == 'LiveChat'){
                            $moduleData[] = 'chat';
                        }elseif($key == 'Labels'){
                            $moduleData[] = 'tags';
                        }elseif($key == 'Moderators'){
                            $moduleData[] = 'users';
                        }elseif($key == 'ModeratorsGroup'){
                            $moduleData[] = 'groups';
                        }elseif($key == 'GroupMsgs'){
                            $moduleData[] = 'group_messages';
                        }elseif($key == 'QuickReplies'){
                            $moduleData[] = 'quick_reply';
                        }elseif($key == '#SALLA'){
                            $moduleData[] = 'salla';
                        }elseif($key == '#ZID'){
                            $moduleData[] = 'zid';
                        }
                    }

                    if($doSync && $modules){
                        tenancy()->initialize($tenant);

                        $userObj = User::getData(User::first(),'ar');
                        $userChannelObj = UserChannels::first();
                        // if($userChannelObj && $userChannelObj->end_date != $newEndData){
                        //     if($modules){
                        //         dispatch(new SyncOldClient($userObj,$modules));
                        //     }
                        // }
                        if($modules){
                            dispatch(new SyncOldClient($userObj,$modules));
                        }
                        tenancy()->end();
                    }
                }
            }
        }
    }

}


