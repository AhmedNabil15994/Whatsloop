<?php namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Models\CentralChannel;
use App\Models\CentralVariable;
use App\Models\CentralWebActions;
use App\Models\Domain;
use App\Models\Variable;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserData;
use App\Models\ClientsRequests;
use App\Models\NotificationTemplate;
use App\Models\OAuthData;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Validator;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use \Spatie\WebhookServer\WebhookCall;
use App\Jobs\SyncHugeOld;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Handler\MyTokenGenerator;

use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

class CentralAuthControllers extends Controller {

    use \TraitsFunc;
    
    public function translate(){
        $input = \Request::all();
        $tr = new GoogleTranslate('en','ar',[],new MyTokenGenerator);
        $result = $tr->translate($input['company']);
        $result = str_replace(' ','-',$result);
        $statusObj['data'] = $result;
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }

    public function zidCallback(Request $request){
        $base_url = 'https://oauth.zid.sa';
        $response = \Http::post($base_url . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => CentralVariable::getVar('ZID_CLIENT_ID'),
            'client_secret' => CentralVariable::getVar('ZID_CLIENT_SECRET'),
            'redirect_uri' => config('app.BASE_URL').'/oauth/callback',
            'code' => $request->code // grant code
        ]);
        $resp = $response->json();
        $data = [
            'type' => 'error',
            'data' => [], 
        ];
        $oauthDataObj = OAuthData::where('type','zid')->orderBy('id','DESC')->first();
        
        if(isset($resp['access_token'])){
            $data = [
                'type' => 'success',
                'data' => $resp, 
            ];
            
            if($oauthDataObj->access_token == null){
                $oauthDataObj->update([
                    'access_token' => $resp['access_token'],
                    'token_type' => $resp['token_type'],
                    'expires_in' => $resp['expires_in'],
                    'authorization' => $resp['authorization'],
                    'refresh_token' => $resp['refresh_token'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            
        }else{
            if($oauthDataObj != null){
                $oauthDataObj->delete();
            }
        }
        $url = 'https://'.$oauthDataObj->domain.'.wloop.net/services/zid/postSettings';
        return \Helper::RedirectWithPostForm($data,$url);
    }

    public function appLogin(Request $request) {
        $input =\Request::all();
        // if(isset($input['type']) && !empty($input['type']) && $input['type'] == 'mob'){
        //     return redirect()->away('https://whatsloop.net/ar/Login.html');
        // }
        
        if(isset($input['onesignal_push_id']) && !empty($input['onesignal_push_id']) ){
            $data['onesignal_push_id'] = $input['onesignal_push_id'];
            Session::put('one_signal',$input['onesignal_push_id']);
            // \Helper::RedirectWithPostForm($data,$url);
        }
        
        // if(Session::has('user_id')){
        //     return redirect('/dashboard');
        // }
        // return redirect()->away('https://whatsloop.net/ar/Login.html');
        // $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        // return view('Central.Auth.Views.V5.login')->with('data',(object) $data);
        return redirect('/login');
    }
    
    public function login() {

        $tenants = \DB::connection('main')->table('tenants')->get();
        foreach ($tenants as $key => $value) {
            tenancy()->initialize($value->id);
            Schema::table('mod_templates', function (Blueprint $table) {
                $table->string('type')->nullable()->default(1)->after('status'); // use this for field after specific column.
                // //$table->string('moderator_id')->nullable()->after('type'); // use this for field after specific column.
                // //$table->string('category_id')->nullable()->after('moderator_id'); // use this for field after specific column.
            });
            tenancy()->end();
        }
        


        if(Session::has('user_id') && !Session::has('t_reset')){
            return redirect('/dashboard');
        } 
        
        if(Session::has('t_user_id') && !Session::has('t_reset')){
            $userId = Session::get('t_user_id');
            $tenantPhone = '';
            if(Session::has('t_phone')){
                $tenantPhone = Session::get('t_phone');
            }
            
            if($tenantPhone != ''){
                $rootId = Session::has('t_root') && Session::get('t_root') != $userId  ? 0 : 1;
                if(!$rootId){
                    $userObj = UserData::where('phone',$tenantPhone)->first();
                    if($userObj){
                        $domainObj = Domain::where('domain',$userObj->domain)->first();
                        $tenant = Tenant::find($domainObj->tenant_id);
                        tenancy()->initialize($tenant->id);
                        $dataObj = User::where('phone',$tenantPhone)->first();
                        tenancy()->end();
                        if(isset($dataObj)){
                            $token = tenancy()->impersonate($tenant,$dataObj->id,'/dashboard');
                            return redirect( tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                                'token' => $token
                            ]));
                        }
                    }
                }
                
                $userObj = CentralUser::find($userId);
                $domainObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                tenancy()->initialize($tenant->id);
                $dataObj = User::where('id',$userId)->first();
                tenancy()->end();
                if(isset($dataObj)){
                    $token = tenancy()->impersonate($tenant,$userId,'/dashboard');
                    return redirect(tenant_route($dataObj->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                        'token' => $token
                    ]));
                }   
            }
            
            
        }
        
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.login')->with('data',(object) $data);
    }

    public function register() {
        if(Session::has('user_id') && !Session::has('t_reset')){
            return redirect('/dashboard');
        }elseif(!Session::has('checked_user_phone')){
            return redirect('/checkAvailability');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.register')->with('data',(object) $data);
    }

    public function doLogin() {
        $input = \Request::all();
        $rules = array(
            'password' => 'required',
            'phone' => 'required',
        );

        $message = array(
            'password.required' => trans('auth.passwordValidation'),
            'phone.required' => trans('auth.phoneValidation'),
        );

        $validate = \Validator::make($input, $rules,$message);

        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if ($userObj == null) {
            $userObj = UserData::where('phone',$input['phone'])->first();
            if($userObj){
                $checkPassword = Hash::check($input['password'], $userObj->password);
                if ($checkPassword == null) {
                    $statusObj['data'] = \URL::to('/getResetPassword?type=old');
                    $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.invalidPassword'));
                    return \Response::json((object) $statusObj);
                }

                $domainObj = Domain::where('domain',$userObj->domain)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                $ids = [];
                tenancy()->initialize($tenant->id);
                $dataObj = User::where('phone',$input['phone'])->first();
                tenancy()->end();
                if(isset($dataObj)){
                    $token = tenancy()->impersonate($tenant,$dataObj->id,'/dashboard');
                    Session::put('check_user_id',$dataObj->id);
                    Session::put('t_user_id',$dataObj->id);
                    Session::put('t_phone',$dataObj->phone);
                    Session::put('t_root',$dataObj->group_id == 1 ? 1 : 0);

                    tenancy()->initialize($tenant->id);
                    //{"860f8c1f-9d5b-46d3-b9b5-c3e84b1b338c":"860f8c1f-9d5b-46d3-b9b5-c3e84b1b338c"}
                    if(Session::has('one_signal') && Session::get('one_signal') != null){
                        $varObj = new Variable;
                        $varObj->var_key = 'ONESIGNALPLAYERID_'.str_replace('+','',$userObj->phone);
                        $varObj->var_value = '{"'.Session::get('one_signal').'":"'.Session::get('one_signal').'"}';
                        $varObj->save();
                    }
                    tenancy()->end();

                    $statusObj['data'] = tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                        'token' => $token
                    ]);
                    $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.welcome') . ucwords($dataObj->name));
                    return \Response::json((object) $statusObj);
                }
                return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
            }
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }

        $checkPassword = Hash::check($input['password'], $userObj->password);
        if ($checkPassword == null) {
            $statusObj['data'] = \URL::to('/getResetPassword?type=old');
            $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.invalidPassword'));
            return \Response::json((object) $statusObj);
        }

        if($userObj->group_id == 0){
            $userObj = CentralUser::getData($userObj);
            $domainObj = Domain::where('domain',$userObj->domain)->first();
            $tenant = Tenant::find($domainObj->tenant_id);
            $token = tenancy()->impersonate($tenant,$userObj->id,'/dashboard');
            Session::put('check_user_id',$userObj->id);
            Session::put('t_user_id',$userObj->id);
            Session::put('t_phone',$userObj->phone);
            Session::put('t_root',$userObj->group_id == 1 ? 1 : 0);
            $statusObj['data'] = tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                'token' => $token
            ]);
            $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.welcome') . ucwords($userObj->name));
            return \Response::json((object) $statusObj);
        }

        // Send Code Here
        $code = rand(1000,10000);
        $userObj->code = $code;
        $userObj->save();


        // $whatsLoopObj =  new \WhatsLoop();
        // $test = $whatsLoopObj->sendMessage('كود التحقق الخاص بك هو : '.$code,$input['phone']);

        // if(json_decode($test)->Code == 'OK'){
        //     \Session::put('check_user_id',$userObj->id);
        //     return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        // }else{
        //     return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        // }

        // $isAdmin = in_array($userObj->group_id, [1,]);
        // session(['group_id' => $userObj->group_id]);
        // session(['user_id' => $userObj->id]);
        // session(['email' => $userObj->email]);
        // session(['name' => $userObj->name]);
        // session(['is_admin' => $isAdmin]);
        // session(['group_name' => $userObj->Group->name_ar]);
        // $channels = CentralUser::getData($userObj)->channels;
        // session(['channel' => $channels[0]->id]);

        // Session::flash('success', trans('auth.passwordChanged'));
        // return redirect('/dashboard');

        if($userObj->two_auth == 1){
            $channelObj = \DB::connection('main')->table('channels')->first();
            $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
            $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
            $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
            if($notificationTemplateObj){
                $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
            }
            $data['phone'] = str_replace('+','',$input['phone']);
            $test = $whatsLoopObj->sendMessage($data);
            $result = $test->json();
            if($result['status']['status'] != 1){
                return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
            }

            \Session::put('check_user_id',$userObj->id);
            return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        }else{
            $isAdmin = in_array($userObj->group_id, [1,]);
            session(['group_id' => $userObj->group_id]);
            session(['user_id' => $userObj->id]);
            session(['email' => $userObj->email]);
            session(['name' => $userObj->name]);
            session(['is_admin' => $isAdmin]);
            session(['group_name' => $userObj->Group->name_ar]);
            $channels = CentralUser::getData($userObj)->channels;
            if(!empty($channels)){
                session(['channel' => $channels[0]->id]);
            }
            if($isAdmin){
                session(['central' => 1]);
            }

            Session::flash('success', trans('auth.welcome') . ucwords($userObj->name));
            $statusObj['data'] = \URL::to('/dashboard');
            $statusObj['status'] = \TraitsFunc::LoginResponse(trans('auth.welcome') . ucwords($userObj->name));
            return \Response::json((object) $statusObj);
        }
        
    }

    public function checkByCode(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = Session::get('check_user_id');
        $userObj = CentralUser::getOne($user_id);
        if($code != $userObj->code){
            return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
        }
        $isAdmin = in_array($userObj->group_id, [1,]);
        session(['group_id' => $userObj->group_id]);
        session(['user_id' => $userObj->id]);
        session(['email' => $userObj->email]);
        session(['name' => $userObj->name]);
        session(['is_admin' => $isAdmin]);
        session(['group_name' => $userObj->Group->name_ar]);
        $channels = CentralUser::getData($userObj)->channels;
        if(!empty($channels)){
            session(['channel' => $channels[0]->id]);
        }

        Session::flash('success', trans('auth.welcome') . $userObj->name_ar);
        return \TraitsFunc::SuccessResponse(trans('auth.welcome') . $userObj->name_ar);
    }

    public function logout() {
        $lang = Session::get('locale');
        session()->flush();
        $lang = Session::put('locale',$lang);
        Session::flash('success', trans('auth.seeYou'));
        return redirect()->to(route('login'));
    }

    public function getResetPassword(){
        if(Session::has('user_id') && !Session::has('t_reset')){
            return redirect('/dashboard');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.resetPassword')->with('data',(object) $data);
    }

    public function resetPassword(){
        $input = \Request::all();
        $rules = [
            'phone' => 'required',
        ];

        $message = [
            'phone.required' => trans('auth.phoneValidation'),
        ];

        $validate = Validator::make($input, $rules, $message);

        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }
        
        Session::put('t_reset',1);

        $phone = $input['phone'];
        $userObj = CentralUser::checkUserBy('phone',$phone);

        if ($userObj == null) {
            $userObj = UserData::where('phone',$input['phone'])->first();
            if($userObj){
                $domainObj = Domain::where('domain',$userObj->domain)->first();
                $tenant = Tenant::find($domainObj->tenant_id);
                tenancy()->initialize($tenant->id);
                $dataObj = User::where('phone',$input['phone'])->first();
                tenancy()->end();
                if(isset($dataObj)){
                    $code = rand(1000,10000);
                    tenancy()->initialize($tenant->id);
                    $dataObj->code = $code;
                    $dataObj->save();
                    tenancy()->end();
                    $userObj->code = $code;
                    $userObj->save();
                    $channelObj = \DB::connection('main')->table('channels')->first();
                    $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
                    $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
                    $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
                    if($notificationTemplateObj){
                        $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
                    }
                    $data['phone'] = str_replace('+','',$input['phone']);
                    $test = $whatsLoopObj->sendMessage($data);
                    $result = $test->json();
                    if($result['status']['status'] != 1){
                        return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
                    }

                    Session::put('check_user_id',$dataObj->phone);
                    Session::put('t_user_id',$dataObj->id);
                    Session::put('t_phone',$dataObj->phone);
                    Session::put('t_root',$dataObj->group_id == 1 ? 1 : 0);
                    return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
                }
                return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
            }
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }

        // Send Code Here
        $code = rand(1000,10000);
        $userObj->code = $code;
        $userObj->save();
        
        if($userObj->group_id == 0){
            Session::put('t_user_id',$userObj->id);
            Session::put('t_phone',$userObj->phone);
            Session::put('t_root',1);
        }

        // $whatsLoopObj =  new \WhatsLoop();
        // $test = $whatsLoopObj->sendMessage('كود التحقق الخاص بك هو : '.$code,$input['phone']);

        // if(json_decode($test)->Code == 'OK'){
        //     Session::put('check_user_id',$userObj->id);
        //     return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        // }else{
        //     return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        // }

        $channelObj = \DB::connection('main')->table('channels')->first();
        $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
        $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
        if($notificationTemplateObj){
            $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
        }
        $data['phone'] = str_replace('+','',$input['phone']);
        $test = $whatsLoopObj->sendMessage($data);
        $result = $test->json();
        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        }

        Session::put('check_user_id',$userObj->id);
        return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
    }

    public function checkResetPassword(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = Session::get('check_user_id');
        $userObj = UserData::where('phone',$user_id)->first();
        if($userObj){
            if($code != $userObj->code){
                return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
            }
        }else{
            $userObj = CentralUser::getOne($user_id);
            if($code != $userObj->code){
                return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
            }

        }


        Session::flash('success', trans('auth.validated'));
        return \TraitsFunc::SuccessResponse(trans('auth.validated'));
    }

    public function changePassword() {
        if(!Session::has('check_user_id')){
            return redirect('/getResetPassword');
        }
        return view('Central.Auth.Views.V5.changePassword');
    }

    public function completeReset() {
        $input = \Request::all();
        $rules = [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ];

        $message = [
            'password.required' => trans('auth.passwordValidation'),
            'password.confirmed' => trans('auth.passwordValidation2'),
            'password_confirmation.required' => trans('auth.passwordValidation3'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
        }

        $password = $input['password'];
        $user_id = Session::get('check_user_id');

        if(Session::has('t_user_id')){
            $userId = Session::get('t_user_id');
            $tenantPhone = '';
            if(Session::has('t_phone')){
                $tenantPhone = Session::get('t_phone');
            }
            
            Session::forget('t_reset');
            if($tenantPhone != ''){
                $rootId = Session::has('t_root') && Session::get('t_root') != $userId  ? 0 : 1;
                if(!$rootId){
                    $userObj = UserData::where('phone',$tenantPhone)->first();
                    if($userObj){
                        $userObj->password = Hash::make($password);
                        $userObj->save();

                        $domainObj = Domain::where('domain',$userObj->domain)->first();
                        $tenant = Tenant::find($domainObj->tenant_id);
                        tenancy()->initialize($tenant->id);
                        $dataObj = User::where('phone',$tenantPhone)->first();
                        $dataObj->update(['password'=>Hash::make($password)]);
                        tenancy()->end();
                        $centralUserObj = CentralUser::find($dataObj->id);
                        if($centralUserObj){
                            $centralUserObj->update(['password'=>Hash::make($password)]);
                        }
                        if(isset($dataObj)){
                            $token = tenancy()->impersonate($tenant,$dataObj->id,'/dashboard');
                            return redirect( tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                                'token' => $token
                            ]));
                        }
                    }
                }
                
                $userObj = CentralUser::find($userId);
                if($userObj->group_id == 0){
                    $userObj->password = Hash::make($password);
                    $userObj->save();

                    $tenant = Tenant::where('phone',$userObj->phone)->first();
                    tenancy()->initialize($tenant->id);
                    User::where('id',$user_id)->update(['password'=>Hash::make($password)]);
                    tenancy()->end();
                    $usdObj = UserData::where('phone',$userObj->phone)->first();
                    if($usdObj){
                        $usdObj->update(['password'=>Hash::make($password)]);
                    }
                    $token = tenancy()->impersonate($tenant,$user_id,'/dashboard');

                    return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                        'token' => $token
                    ]));
                }
            }
        }else{
            $userObj = CentralUser::find($user_id);
            $userObj->password = Hash::make($password);
            $userObj->save();
            Session::forget('check_user_id');

            $isAdmin = in_array($userObj->group_id, [1,]);
            session(['group_id' => $userObj->group_id]);
            session(['user_id' => $userObj->id]);
            session(['email' => $userObj->email]);
            session(['name' => $userObj->name]);
            session(['is_admin' => $isAdmin]);
            session(['group_name' => $userObj->Group->name_ar]);
            $channels = CentralUser::getData($userObj)->channels;
            if(!empty($channels)){
                session(['channel' => $channels[0]->id]);
            }
            if($isAdmin){
                session(['central' => 1]);
            }

            Session::flash('success', trans('auth.passwordChanged'));
            return redirect('/dashboard');
        }
    }

    public function checkAvailability(){
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.checkAvailability')->with('data',(object) $data);
    }

    public function postCheckAvailability(Request $request){
        $input = \Request::all();
        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if($userObj){
            Session::flash('error', trans('main.phoneError'));
            return redirect()->back()->withInput();
        }
        
        $dataArr['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        $dataArr['phone'] = $input['phone'];
        
        $clientRequestObj = ClientsRequests::getOne($input['phone']);
        if($clientRequestObj){
            $clientRequestObj->delete();
        }
        $channelObj = \DB::connection('main')->table('channels')->first();
        $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
        
        $code = rand(1000,10000);
        $notificationTemplateObj = NotificationTemplate::getOne(1,'phoneConfirmation');
        $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
        if($notificationTemplateObj){
            $data['body'] = str_replace('{CODE}',$code,$notificationTemplateObj->content_ar);
        }
        $data['phone'] = str_replace('+','',$input['phone']);

        $test = $whatsLoopObj->sendMessage($data);
        $result = $test->json();

        if($result['status']['status'] != 1){
            Session::flash('error', trans('auth.codeProblem'));
            return redirect()->back()->withInput();
        }

        $clientRequestObj = new ClientsRequests();
        $clientRequestObj->phone = $input['phone'];
        $clientRequestObj->code = $code;
        $clientRequestObj->ip_address = $request->ip();
        $clientRequestObj->created_at = DATE_TIME;
        $clientRequestObj->save();
        return view('Central.Auth.Views.V5.checkCode')->with('data',(object) $dataArr);
    }

    public function checkAvailabilityCode(){
        $input = \Request::all();
        
        $clientRequestObj = ClientsRequests::getOne($input['phone']);
        $dataArr['phone'] = $input['phone'];
        $dataArr['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        if(!$clientRequestObj){
            Session::flash('error', trans('main.userNotFound'));
            return view('Central.Auth.Views.V5.checkCode')->with('data',(object) $dataArr);
        }

        if($clientRequestObj->code != $input['code']){
            Session::flash('error', trans('auth.codeError'));
            return view('Central.Auth.Views.V5.checkCode')->with('data',(object) $dataArr);
        }

        Session::put('checked_user_phone',$input['phone']);
        return redirect()->to('/register');
    }

    protected function validateInsertObject($input){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
            'company' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
            'email' => 'required|email',
            'domain' => 'required|regex:/^([a-zA-Z0-9][a-zA-Z0-9-_])*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]$/',
        ];

        $message = [
            'name.required' => trans('main.nameValidate'),
            'comapny.required' => trans('main.nameValidate'),
            'phone.required' => trans('main.phoneValidate'),
            'password.required' => trans('main.passwordValidate'),
            'password.min' => trans('main.passwordValidate2'),
            'password.confirmed' => trans('auth.passwordValidation2'),
            'password_confirmation.required' => trans('auth.passwordValidation3'),
            'email.required' => trans('main.emailValidate'),
            'email.email' => trans('main.emailValidate'),
            'domain.required' => trans('main.domainValidate'),
            'domain.regex' => trans('main.domain2Validate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function postRegister(){
        $input = \Request::all();
        $input['phone'] = Session::get('checked_user_phone');

        $names = explode(' ',$input['name']);
        if(count($names) < 2){
            Session::flash('error', trans('main.name2Validate'));
            return redirect()->back()->withInput();
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }
            
        $domainObj = Domain::getOneByDomain($input['domain']);
        if($domainObj){
            Session::flash('error', trans('main.domainValidate2'));
            return redirect()->back()->withInput();
        }

        $userObj = CentralUser::checkUserBy('email',$input['email']);
        if($userObj){
            Session::flash('error', trans('main.emailError'));
            return redirect()->back()->withInput();
        }

        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if($userObj){
            Session::flash('error', trans('main.phoneError'));
            return redirect()->back()->withInput();
        }


        $tenant = Tenant::create([
            'phone' => $input['phone'],
            'title' => $input['name'],
            'description' => '',
        ]);
        
        $tenant->domains()->create([
            'domain' => $input['domain'],
        ]);

        $baseUrl = 'https://whatsloop.net/api/v1/';

        // Get User Details
        $mainURL = $baseUrl.'user-details';
        $isOld = 0;

        $data = ['phone' => str_replace('+','',$input['phone']) /*'966570116626'*/];
        $result =  \Http::post($mainURL,$data);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] === true){
                // Begin Sync
                $isOld = 1;
            }
        }
        
        
        $centralUser = CentralUser::create([
            'global_id' => (string) Str::orderedUuid(),
            'name' => $input['name'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'company' => $input['company'],
            'password' => Hash::make($input['password']),
            'notifications' => 0,
            'setting_pushed' => 0,
            'offers' => 0,
            'group_id' => 0,
            'is_active' => 1,
            'is_approved' => 1,
            'status' => 1,
            'two_auth' => 0,
            'is_old' => $isOld,
            'is_synced' => 0,
        ]);

        
        \DB::connection('main')->table('tenant_users')->insert([
            'tenant_id' => $tenant->id,
            'global_user_id' => $centralUser->global_id,
        ]);
        
        $user = $tenant->run(function() use(&$centralUser,$input){

            $userObj = User::create([
                'id' => $centralUser->id,
                'global_id' => $centralUser->global_id,
                'name' => $input['name'],
                'phone' => $input['phone'],
                'email' => $input['email'],
                'company' => $input['company'],
                'group_id' => 1,
                'status' => 1,
                'domain' => $input['domain'],
                'is_old' => $centralUser->is_old,
                'is_synced' => $centralUser->is_synced,
                'two_auth' => 0,
                'sort' => 1,
                'setting_pushed' => 0,
                'password' => Hash::make($input['password']),
                'is_active' => 1,
                'is_approved' => 1,
                'notifications' => 0,
                'offers' => 0,
            ]);

            return $userObj;
        });

        Session::flash('success', trans('main.addSuccess'));
        $token = tenancy()->impersonate($tenant,$user->id,'/menu');
        if($isOld){
            $token = tenancy()->impersonate($tenant,$user->id,'/sync');
        }

        $notificationTemplateObj = NotificationTemplate::getOne(2,'newClient');
        $allData = [
            'name' => $input['name'],
            'subject' => $notificationTemplateObj->title_ar,
            'content' => $notificationTemplateObj->content_ar,
            'email' => $input['email'],
            'template' => 'tenant.emailUsers.default',
            'url' => 'https://'.$input['domain'].'.wloop.net/login',
            'extras' => [
                'company' => $input['company'],
                'url' => 'https://'.$input['domain'].'.wloop.net/login',
            ],
        ];
        \MailHelper::prepareEmail($allData);
        $salesData = $allData;
        $salesData['email'] = 'sales@whatsloop.net';
        \MailHelper::prepareEmail($salesData);


        $notificationTemplateObj = NotificationTemplate::getOne(1,'newClient');
        $allData['phone'] = $input['phone'];
        \MailHelper::prepareEmail($allData,1);

        
        Session::put('check_user_id',$user->id);
        return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
            'token' => $token
        ]));
    }

    public function changeLang(Request $request){
        if($request->ajax()){
            if(!Session::has('locale')){
                Session::put('locale', $request->locale);
            }else{
                Session::forget('locale');
                Session::put('locale', $request->locale);
            }
            return Redirect::back();
        }
    }
}
