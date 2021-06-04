<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Central\Channel;
use App\Models\Variable;
use App\Models\UserChannels;
use App\Models\UserAddon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Validator;
use URL;
use Illuminate\Http\Request;

class AuthControllers extends Controller {

    use \TraitsFunc;

    public function login() {
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Tenancy.Auth.Views.login')->with('data',(object) $data);
    }

    public function loginByCode() {
        Session::put('check_user_id',\Request::get('user_id'));
        return view('Tenancy.Auth.Views.loginByCode');
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

        $userObj = User::checkUserBy('phone',$input['phone']);
        if ($userObj == null) {
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
            $this->setSessions($userObj);

            Session::flash('success', trans('auth.welcome') . $userObj->name_ar);
            return \TraitsFunc::LoginResponse(trans('auth.welcome') . $userObj->name_ar);
        }
    }

    public function setSessions($userObj){
        $isAdmin = in_array($userObj->group_id, [1,]);
        session(['group_id' => $userObj->group_id]);
        session(['global_id' => $userObj->global_id]);
        session(['user_id' => $userObj->id]);
        session(['email' => $userObj->email]);
        session(['name' => $userObj->name]);
        session(['domain' => $userObj->domain]);
        session(['is_admin' => $isAdmin]);
        session(['group_name' => $userObj->Group->name_ar]);
        // $channels = User::getData($userObj)->channels;
        $channels = $userObj->channels != null ? UserChannels::NotDeleted()->whereIn('id',unserialize($userObj->channels))->get() : [];
        session(['channel' => !empty($channels) ? $channels[0]->id : null]);
        session(['membership' => $userObj->membership_id]);
        if($isAdmin){
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
            session(['addons' => $userObj->addons !=  null ? UserAddon::dataList(unserialize($userObj->addons)) : []]);
        }else{
            $mainUser = User::first();
            $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$mainUser->global_id)->first();
            session(['addons' => $mainUser->addons !=  null ? UserAddon::dataList(unserialize($mainUser->addons)) : []]);
        }
        session(['tenant_id' => $tenantObj->tenant_id]);

        // Get Membership and Extra Quotas Features
        if(!empty($userObj->membership_id)){
            $membershipFeatures = \DB::connection('main')->table('memberships')->where('id',Session::get('membership'))->first()->features;
            $featuresId = unserialize($membershipFeatures);
            $features = \DB::connection('main')->table('membership_features')->whereIn('id',$featuresId)->pluck('title_en');
            $dailyMessageCount = (int) $features[0];
            $employessCount = (int) $features[1];
            $storageSize = (int) $features[2];
            session(['dailyMessageCount' => $dailyMessageCount]);
            session(['employessCount' => $employessCount]);
            session(['storageSize' => $storageSize]);
        }

    }

    public function checkByCode(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = Session::get('check_user_id');
        $userObj = User::getOne($user_id);
        // dd($userObj);
        if($code != $userObj->code && $code != $userObj->pin_code){
            return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
        }
        $this->setSessions($userObj);

        Session::flash('success', trans('auth.welcome') . $userObj->name_ar);
        return \TraitsFunc::SuccessResponse(trans('auth.welcome') . $userObj->name_ar);
    }

    public function logout() {
        $lang = Session::get('locale');
        session()->flush();
        $lang = Session::put('locale',$lang);
        Session::flash('success', trans('auth.seeYou'));
        return redirect('/login');
    }

    public function getResetPassword(){
        if(Session::has('user_id')){
            return redirect('/dashboard');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Tenancy.Auth.Views.resetPassword')->with('data',(object) $data);
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
        $userObj = User::checkUserBy('phone',$phone);

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
        $userObj = User::getOne($user_id);
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
        return view('Tenancy.Auth.Views.changePassword');
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
        $userObj = User::NotDeleted()->find($user_id);
        if($userObj == null){
            Session::flash('error', trans('auth.invalidUser'));
            return back()->withInput();
        }

        $userObj->password = Hash::make($password);
        $userObj->save();
        Session::forget('check_user_id');

        $this->setSessions($userObj);

        Session::flash('success', trans('auth.passwordChanged'));
        return redirect('/dashboard');
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
