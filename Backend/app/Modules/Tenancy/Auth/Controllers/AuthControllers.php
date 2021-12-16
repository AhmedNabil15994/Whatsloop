<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Central\Channel;
use App\Models\Variable;
use App\Models\UserChannels;
use App\Models\UserAddon;
use App\Models\CentralUser;
use App\Models\Domain;
use App\Models\Tenant;
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
            return redirect('/menu');
        }
        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        // return view('Tenancy.Auth.Views.login')->with('data',(object) $data);
        return view('Tenancy.Auth.Views.V5.login')->with('data',(object) $data);
    }

    public function loginByCode() {
        Session::put('check_user_id',\Request::get('user_id'));
        return view('Tenancy.Auth.Views.V5.loginByCode');
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
            $whatsLoopObj =  new \MainWhatsLoop($channelObj->instanceId,$channelObj->instanceToken);
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
            User::setSessions($userObj);

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
        $userObj = User::getOne($user_id);
        // dd($userObj);
        if($code != $userObj->code && $code != $userObj->pin_code){
            return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
        }
        User::setSessions($userObj);
        $this->genNewPinCode($userObj->id);
        Session::flash('success', trans('auth.welcome') . $userObj->name_ar);
        return \TraitsFunc::SuccessResponse(trans('auth.welcome') . $userObj->name_ar);
    }

    public function genNewPinCode($user_id){
        $newCode = rand(1,10000);
        $userObj = User::getOne($user_id);
        $userObj->pin_code = $newCode;
        $userObj->save();

        $userObj = CentralUser::getOne($user_id);
        $userObj->pin_code = $newCode;
        $userObj->save();
        return $newCode;
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
        return view('Tenancy.Auth.Views.V5.resetPassword')->with('data',(object) $data);
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
        return view('Tenancy.Auth.Views.V5.changePassword');
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

        User::setSessions($userObj);

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


    public function changeData() {
        $userObj = User::first();
        if($userObj->is_old != 1){
            return redirect()->to('/login');
        }
        return view('Tenancy.Auth.Views.V5.changeData');
    }

    public function completeChangeData() {
        $input = \Request::all();
        $rules = [
            'domain' => 'required|regex:/^([a-zA-Z0-9][a-zA-Z0-9-_]*\.)*[a-zA-Z0-9]*[a-zA-Z0-9-_]*[[a-zA-Z0-9]$/',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ];

        $message = [
            'domain.required' => trans('main.domainValidate'),
            'domain.regex' => trans('main.domain2Validate'),
            'password.required' => trans('auth.passwordValidation'),
            'password.confirmed' => trans('auth.passwordValidation2'),
            'password_confirmation.required' => trans('auth.passwordValidation3'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
        }

        $domainObj = Domain::getOneByDomain($input['domain']);
        if($domainObj){
            Session::flash('error', trans('main.domainValidate2'));
            return redirect()->back()->withInput();
        }

        $userObj = User::first();
        $oldDomain = $userObj->domain;
        $domainObj = Domain::getOneByDomain($userObj->domain);
        if($domainObj){
            $domainObj->domain = $input['domain'];
            $domainObj->save();
        }

        $password = $input['password'];
        if($userObj == null){
            Session::flash('error', trans('auth.invalidUser'));
            return back()->withInput();
        }

        $userObj->password = Hash::make($password);
        $userObj->save();

        User::NotDeleted()->update(['domain'=>$input['domain']]);
        $tenant = Tenant::where('phone',$userObj->phone)->first();
        $token = tenancy()->impersonate($tenant,$userObj->id,'/dashboard');
        
        Session::put('check_user_id',$userObj->id);
        // dd(request()->getHttpHost());
        // dd(str_replace($oldDomain,$input['domain'],request()->getHttpHost()));
        return redirect(tenant_route(str_replace($oldDomain,$input['domain'],request()->getHttpHost()) , 'impersonate',[
            'token' => $token
        ]));
    }
}
