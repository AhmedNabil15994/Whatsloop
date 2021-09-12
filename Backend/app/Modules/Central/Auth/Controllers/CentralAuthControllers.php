<?php namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Models\CentralChannel;
use App\Models\CentralVariable;
use App\Models\CentralWebActions;
use App\Models\Domain;
use App\Models\Tenant;
use App\Models\User;
use App\Models\ClientsRequests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Validator;
use URL;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CentralAuthControllers extends Controller {

    use \TraitsFunc;

    public function login() {
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.login')->with('data',(object) $data);
    }

    public function register() {
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }elseif(!Session::has('checked_user_phone')){
            return redirect('/checkAvailability');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.register')->with('data',(object) $data);
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
        if ($userObj == null || $userObj->group_id == 0) {
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }

        $checkPassword = Hash::check($input['password'], $userObj->password);
        if ($checkPassword == null) {
            return \TraitsFunc::ErrorMessage(trans('auth.invalidPassword'));
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

            Session::flash('success', trans('auth.welcome') . $userObj->name_ar);
            return \TraitsFunc::LoginResponse(trans('auth.welcome') . $userObj->name_ar);
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
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.resetPassword')->with('data',(object) $data);
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

        $phone = $input['phone'];
        $userObj = CentralUser::checkUserBy('phone',$phone);

        if ($userObj == null) {
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }

        // Send Code Here
        $code = rand(1000,10000);
        $userObj->code = $code;
        $userObj->save();

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
        $userObj = CentralUser::getOne($user_id);
        if($code != $userObj->code){
            return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
        }

        Session::flash('success', trans('auth.validated'));
        return \TraitsFunc::SuccessResponse(trans('auth.validated'));
    }

    public function changePassword() {
        if(!Session::has('check_user_id')){
            return redirect('/getResetPassword');
        }
        return view('Central.Auth.Views.changePassword');
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
        $userObj = CentralUser::NotDeleted()->find($user_id);
        if($userObj == null){
            Session::flash('error', trans('auth.invalidUser'));
            return back()->withInput();
        }

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
        session(['channel' => $channels[0]->id]);

        Session::flash('success', trans('auth.passwordChanged'));
        return redirect('/dashboard');
    }

    public function checkAvailability(){
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.checkAvailability')->with('data',(object) $data);
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
        // dd($clientRequestObj);
        if($clientRequestObj){
            $dataArr['userCode'] = $clientRequestObj->code;
            return view('Central.Auth.Views.checkCode')->with('data',(object) $dataArr);
        }else{
            $channelObj = \DB::connection('main')->table('channels')->first();
            $whatsLoopObj =  new \MainWhatsLoop($channelObj->id,$channelObj->token);
            
            $code = rand(1000,10000);
            $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
            $data['phone'] = str_replace('+','',$input['phone']);

            $test = $whatsLoopObj->sendMessage($data);
            $result = $test->json();

            if($result['status']['status'] != 1){
                return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
            }

            $clientRequestObj = new ClientsRequests();
            $clientRequestObj->phone = $input['phone'];
            $clientRequestObj->code = $code;
            $clientRequestObj->ip_address = $request->ip();
            $clientRequestObj->created_at = DATE_TIME;
            $clientRequestObj->save();
            return view('Central.Auth.Views.checkCode')->with('data',(object) $dataArr);
        }
    }

    public function checkAvailabilityCode(){
        $input = \Request::all();
        $clientRequestObj = ClientsRequests::getOne($input['phone']);
        $dataArr['phone'] = $input['phone'];
        $dataArr['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        if(!$clientRequestObj){
            Session::flash('error', trans('main.userNotFound'));
            return view('Central.Auth.Views.checkCode')->with('data',(object) $dataArr);
        }

        if(isset($clientRequestObj) && $clientRequestObj->code != $input['code']){
            Session::flash('error', trans('auth.codeError'));
            return view('Central.Auth.Views.checkCode')->with('data',(object) $dataArr);
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
            'email' => 'required',
            'domain' => 'required',
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
            'domain.required' => trans('main.domainValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function postRegister(){
        $input = \Request::all();
        $input['phone'] = Session::get('checked_user_phone');
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }
            
        $domainObj = Domain::getOneByDomain('domain',$input['domain']);
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

        $centralUser = CentralUser::create([
            'global_id' => (string) Str::orderedUuid(),
            'name' => $input['name'],
            'phone' => $input['phone'],
            'email' => $input['email'],
            'company' => $input['company'],
            'password' => Hash::make($input['password']),
            'notifications' => 0,
            'offers' => 0,
            'group_id' => 0,
            'is_active' => 1,
            'is_approved' => 1,
            'status' => 1,
            'two_auth' => 0,
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
                'two_auth' => 0,
                'sort' => 1,
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
