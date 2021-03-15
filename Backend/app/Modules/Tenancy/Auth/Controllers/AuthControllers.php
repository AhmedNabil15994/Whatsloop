<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LoginHistory;
use App\Models\BlockedUser;
use App\Models\Variable;
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
        $data['code'] = \Helper::getCountryCode()->countryCode;
        return view('Tenancy.Auth.Views.login')->with('data',(object) $data);
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


        // $whatsLoopObj =  new \WhatsLoop();
        // $test = $whatsLoopObj->sendMessage('كود التحقق الخاص بك هو : '.$code,$input['phone']);

        // if(json_decode($test)->Code == 'OK'){
        //     \Session::put('check_user_id',$userObj->id);
        //     return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
        // }else{
        //     return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        // }

        $whatsLoopObj =  new \MainWhatsLoop();
        $data['body'] = 'كود التحقق الخاص بك هو : '.$code;
        $data['phone'] = str_replace('+','',$input['phone']);
        $test = $whatsLoopObj->sendMessage($data);
        $result = $test->json();
        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        }

        \Session::put('check_user_id',$userObj->id);
        return \TraitsFunc::SuccessResponse(trans('auth.codeSuccess'));
    }

    public function checkByCode(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = Session::get('check_user_id');
        $userObj = User::getOne($user_id);
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
        $channels = User::getData($userObj)->channels;
        session(['channel' => $channels[0]->id]);


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
        $data['code'] = \Helper::getCountryCode()->countryCode;
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

        $whatsLoopObj =  new \MainWhatsLoop();
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

        $isAdmin = in_array($userObj->group_id, [1,]);
        session(['group_id' => $userObj->group_id]);
        session(['user_id' => $userObj->id]);
        session(['email' => $userObj->email]);
        session(['name' => $userObj->name]);
        session(['is_admin' => $isAdmin]);
        session(['group_name' => $userObj->Group->name_ar]);
        $channels = User::getData($userObj)->channels;
        session(['channel' => $channels[0]->id]);

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
