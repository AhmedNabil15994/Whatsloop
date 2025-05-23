<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Variable;
use App\Models\UserChannels;
use App\Models\CentralChannel;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class CentralController extends Controller
{
    

    public function register()
    {
        $this->validate(request(),[
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required' ,'string', 'max:255'],
            'phone' => ['required', 'numeric','unique:tenants,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'subdomain' => ['required','string','unique:domains,domain']        
        ]);

        // Create New Instance For New Domain
        $channelObj = CentralChannel::first();
        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->createChannel();
        $result = $updateResult->json();

        if($result['status']['status'] != 1){
            \Session::flash('error', $result['status']['message']);
            return back()->withInput();
        }

        $channel = [
            'id' => $result['data']['channel']['id'],
            'token' => $result['data']['channel']['token'],
            'name' => 'Channel #'.$result['data']['channel']['id'],
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d',strtotime('+1 month')),
        ];

        $tenant = Tenant::create([
            'phone' => request('phone'),
            'title' => request('title'),
            'description' => request('description')
        ]);
        
        $tenant->domains()->create([
            'domain' => request('subdomain'),
        ]);

        $centralUser = CentralUser::create([
            'global_id' => (string) Str::orderedUuid(),
            'name' => request('name'),
            'phone' => request('phone'),
            'password' => Hash::make(request('password')),
            'is_active' => 1,
            'is_approved' => 1,
            'status' => 1,
            'channels' => serialize([$channel['id']]),
        ]);

        $extraChannelData = $channel;
        $extraChannelData['tenant_id'] = $tenant->id;
        $extraChannelData['global_user_id'] = $centralUser->global_id;

        CentralChannel::create($extraChannelData);

        $user = $tenant->run(function() use(&$centralUser,$channel){
            UserChannels::create($channel);
            return User::create([
                'global_id' => $centralUser->global_id,
                'name' => request('name'),
                'phone' => '+'.request('phone'),
                'group_id' => 1,
                'status' => 1,
                'domain' => request('subdomain'),
                'sort' => 1,
                'channels' => serialize([$channel['id']]),
                'password' => Hash::make(request('password')),
                'is_active' => 1,
                'is_approved' => 1
            ]);
        });



        // Update User With Settings For Whatsapp Based On His Domain
        // $myData = [
        //     'sendDelay' => '0',
        //     'webhookUrl' => str_replace('://', '://'.request('subdomain').'.', \URL::to('/')).'/whatsloop/webhooks/messages-webhook',
        //     'instanceStatuses' => 1,
        //     'webhookStatuses' => 1,
        //     'statusNotificationsOn' => 1,
        //     'ackNotificationsOn' => 1,
        //     'chatUpdateOn' => 1,
        //     'videoUploadOn' => 1,
        //     'guaranteedHooks' => 1,
        //     'parallelHooks' => 1,
        // ];
        // $updateResult = $mainWhatsLoopObj->setSettings($channel['id'],$channel['token'],$myData);
        // $result = $updateResult->json();
        // if($result['status']['status'] != 1){
        //     \Session::flash('error', $result['status']['message']);
        //     return back()->withInput();
        // }

        return $this->impersonateUser($tenant,$user->id);
    }

    public function login()
    {

        $validator = Validator::make(request()->all(),[
            'login_phone' => 'required',
            'login_password' => 'required'
        ]);

        $centralUser = CentralUser::where('phone',request('login_phone'))->first();

        if(!$centralUser || !Hash::check(request('login_password'),$centralUser->password)){
            $validator->errors()->add('login_phone','credentials error phone or password ');
            throw new ValidationException($validator);
        }
        
        $global_id = $centralUser->global_id;

        if($centralUser->tenants()->count() == 1){
            $tenant = $centralUser->tenants()->first();
            $user = $tenant->run(function() use($global_id){
                return User::where('global_id', $global_id)->firstOrFail();
            });
            return $this->impersonateUser($tenant,$user->id);
        }
        
        session(['global_id' => $global_id]);

        $tenats = $centralUser->tenants;

        return view('central.redirection',compact('tenats'));
 
    }

    public function redirectLogin()
    {

        $validator = Validator::make(request()->all(),[
            'tenant_id' => 'required|exists:tenatns,id',
        ]);

        $tenant = Tenant::where('id',request('tenant_id'))->firstOrFail();
        $global_id = session()->get('global_id');

        $user = $tenant->run(function() use($global_id){
            return User::where('global_id',$global_id)->firstOrFail();
        });

        $check_tenant_users = DB::table('tenant_users')
            ->where('global_user_id',$user->global_id)
            ->where('tenant_id',$tenant->id)
            ->exists();

        if(!$global_id || !$check_tenant_users){
            return back()->withErrors(['tenant_id' => 'credentials errors']);
        }
        
        session()->invalidate();

        return $this->impersonateUser($tenant,$user->id);

    }

    private function impersonateUser($tenant,$user_id)
    {
        $token = tenancy()->impersonate($tenant,$user_id,'/dashboard');
        return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
            'token' => $token
        ]));
    }

    public function showLogin()
    {
        return view('auth.login',with(['routeLogin' => route('central.login')]));
    }
}

