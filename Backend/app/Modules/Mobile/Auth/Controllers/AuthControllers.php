<?php namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Models\Central\Channel;
use App\Models\Variable;
use App\Models\UserChannels;
use App\Models\UserAddon;
use App\Models\ApiKeys;
use App\Models\ApiAuth;
use App\Models\Devices;
use App\Models\User;
use App\Models\Domain;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Session;


class AuthController extends Controller {

    use \TraitsFunc;
    
    public function oldClient(){
        $input = \Request::all();
        if(!isset($input['phone']) || empty($input['phone'])){
            return \TraitsFunc::ErrorMessage('Phone Field is required');
        }

        $userObj = CentralUser::checkUserBy('phone','+'.$input['phone']);

        if ($userObj == null || $userObj->group_id != 0 || $userObj->is_old == 0 || $userObj->is_synced == 0) {
            $dataObj = [
                'found' => false,
                'domain' => null,
            ];
        }else{
            $userObj = CentralUser::getData($userObj);
            $dataObj = [
                'found' => true,
                'domain' => 'https://'.$userObj->domain.'.wloop.net',
            ];
        }

        $statusObj['data'] = $dataObj;  
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }

	public function login() {
        $input = \Request::all();

        $rules = array(
            'password' => 'required',
            'phone' => 'required',
        );

        $message = array(
            'phone.required' => 'Phone Field is required',
            'password.required' => 'Password Field is required',
        );

        $validate = \Validator::make($input, $rules,$message);

        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if ($userObj == null || $userObj->group_id != 0) {
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
            $statusObj['data'] = self::LoginAction($userObj);
            $statusObj['status'] = \TraitsFunc::SuccessMessage(trans('auth.codeSuccess'));
            return \Response::json((object) $statusObj);
        }

        $dataObj = self::LoginAction($userObj);

        //Check token
        $checkAuth = ApiAuth::checkUserToken($dataObj->token);
       
        if($checkAuth == null){
            \Auth::logout();
            session()->flush();
 
            return \TraitsFunc::ErrorMessage("Error, Please contact with admin to recheck user data!", 401);
        }

        Devices::applyNewDevice($checkAuth['auth']->id);

        $statusObj['data'] = $dataObj;
        $statusObj['status'] = \TraitsFunc::SuccessMessage("Login Success");
        return \Response::json((object) $statusObj);
	}

     public function checkByCode(){
        $input = \Request::all();
        $code = $input['code'];
        $user_id = $input['user_id'];
        
        if(!isset($input['code']) || empty($input['code'])){
            return \TraitsFunc::ErrorMessage(trans('auth.codeProblem'));
        }

        if(!isset($input['user_id']) || empty($input['user_id'])){
            return \TraitsFunc::ErrorMessage(trans('auth.invalidUser'));
        }
        
        $userObj = CentralUser::getOne($user_id);
        if($code != $userObj->code && $code != $userObj->pin_code){
            return \TraitsFunc::ErrorMessage(trans('auth.codeError'));
        }

        $dataObj = self::LoginAction($userObj);

        //Check token
        $checkAuth = ApiAuth::checkUserToken($dataObj->token);
       
        if($checkAuth == null){
            \Auth::logout();
            session()->flush();
 
            return \TraitsFunc::ErrorMessage("Error, Please contact with admin to recheck user data!", 401);
        }

        Devices::applyNewDevice($checkAuth['auth']->id);

        $statusObj['data'] = $dataObj;
        $statusObj['status'] = \TraitsFunc::SuccessMessage("Login Success");
        return \Response::json((object) $statusObj);
    }

	static function LoginAction($userObj) {
        $dateTime = DATE_TIME;
        $apiKeyId = ApiKeys::checkApiKey()->id;

        //ApiAuth::logoutOtherSessions($userObj->id, $apiKeyId);

        $ApiAuth = new ApiAuth();
        $ApiAuth->auth_token = md5(uniqid(rand(), true));
        $ApiAuth->auth_expire = 1;
        $ApiAuth->api_id = $apiKeyId;
        $ApiAuth->user_id = $userObj->id;
        $ApiAuth->created_at = $dateTime;
        $ApiAuth->save();

        $token_value = $ApiAuth->auth_token;

        $centralObj = CentralUser::getData($userObj);
        $domainObj = Domain::where('domain',$centralObj->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);

        tenancy()->initialize($tenant);
        $dataObj = User::getData(User::getOne($userObj->id));
        tenancy()->end($tenant);

        $dataObj->domain = $centralObj->domain;
        $dataObj->group = $centralObj->group_id == 0 ? 'Administrator' : $dataObj->group;
        $dataObj->tenant_id = $domainObj->tenant_id;
        $dataObj->token = $token_value;
        $dataObj->auth_id = $ApiAuth->id;
        return $dataObj;
    }

	public function logout() {
		$authObj = ApiAuth::checkUserToken(APP_TOKEN);
    
        if($authObj == null){
            return \TraitsFunc::ErrorMessage("Invalid Process, Please try again later", 400);
        }

        $authObj['auth']->auth_expire = 0;
        $authObj['auth']->save();

        \Auth::logout();
        session()->flush();

        $statusObj['status'] = new \stdClass();
        $statusObj['status'] = \TraitsFunc::SuccessMessage("Logout Success, You can now login again!");
		return \Response::json((object) $statusObj);
	}

  //   public function register() {
  //       if (!isset($_SERVER['HTTP_DEVICEKEY'])) {
  //           return \TraitsFunc::ErrorMessage("Please check device key", 400);
  //       }

  //       $dateTime = DATE_TIME;
  //       $input = \Input::all();

  //       $rules = [
  //           'name' => 'required',
  //           'email' => 'required|email',
  //           'password' => 'required',
  //           'phone' => 'required',
  //           'university_id' => 'required',
  //           'faculty_id' => 'required',
  //           'gender' => 'required',
  //           'year'  => 'required|gt:0',
  //       ];

  //       $message = [
  //           'name.required' => "Sorry Name Required",
  //           'email.required' => "Sorry Email Required",
  //           'email.email' => "Sorry Email Must Be Email Type",
  //           'password.required' => "Sorry Password Required",
  //           'phone.required' => "Sorry Phone Required",
  //           'university_id.required' => "Sorry University Required",
  //           'faculty_id.required' => "Sorry Faculty Required",
  //           'gender.required' => "Sorry Gender Required",
  //           'year.required' => "Sorry Year Required",
  //       ];

  //       $validate = \Validator::make($input, $rules, $message);

  //       if($validate->fails()){
  //           return \TraitsFunc::ErrorMessage($validate->messages()->first(), 400);
  //       }
            
  //       $checkPhone = Profile::where('phone',$input['phone'])->first();
  //       if($checkPhone != null) {
  //           return \TraitsFunc::ErrorMessage("This phone exist, Please choose another phone!", 400);
  //       }

  //       $checkEmail = User::checkUserByEmail($input['email']);
  //       if($checkEmail != null) {
  //           return \TraitsFunc::ErrorMessage("This email exist, Please choose another email!", 400);
  //       }

  //       $universityObj = University::getOne($input['university_id']);
  //       if ($universityObj == null) {
  //           return \TraitsFunc::ErrorMessage("This University not found", 400);
  //       }

  //       $facultyObj = Faculty::getOne($input['faculty_id']);
  //       if ($facultyObj == null) {
  //           return \TraitsFunc::ErrorMessage("This Faculty not found", 400);
  //       }

  //       if ($input['year'] > 0 && $input['year'] > $facultyObj->number_of_years) {
  //           return \TraitsFunc::ErrorMessage("Year Must Be Less than or equal to ".$facultyObj->number_of_years, 400);
  //       }

  //       $userObj = new User();        
  //       $userObj->email = isset($input['email']) ? $input['email'] : null;            
  //       $userObj->name = $input['name'];
  //       $userObj->is_active = 1;
  //       $userObj->password = isset($input['password']) ? Hash::make($input['password']) : '';
  //       $userObj->last_login = $dateTime;
  //       $userObj->created_at = $dateTime;
  //       $userObj->save();

  //       $slug = 'alien';
  //       $username = User::generateUsername($slug, $userObj->id);
  //       $userObj->created_by = $userObj->id;
  //       $userObj->save();

  //       $name = explode(' ', $input['name'], 2);

  //       $profileObj = new Profile();
  //       $profileObj->user_id = $userObj->id;
  //       $profileObj->first_name = $name[0];
  //       $profileObj->last_name = isset($name[1]) ? $name[1]  : '';
  //       $profileObj->display_name = $input['name'];
  //       $profileObj->phone = $input['phone'];
  //       $profileObj->group_id = 3;
  //       $profileObj->gender = $input['gender'];
  //       $profileObj->university_id = $input['university_id'];
  //       $profileObj->faculty_id = $input['faculty_id'];
  //       $profileObj->year = $input['year'];
  //       $profileObj->username = $username;
  //       $profileObj->save();

  //       $dataObj = self::LoginAction($userObj);

  //        //Check token
  //       $checkAuth = ApiAuth::checkUserToken($dataObj->token);
       
  //       if($checkAuth == null){
  //           \Auth::logout();
  //           session()->flush();
 
  //           return \TraitsFunc::ErrorMessage("Error, Please contact with admin to recheck user data!", 401);
  //       }

  //       Devices::applyNewDevice($checkAuth['auth']->id);

  //       $statusObj['data'] = new \stdClass();
  //       $statusObj['data'] = $dataObj;
  //       $statusObj['status'] = \TraitsFunc::SuccessMessage("Register Success");
		// return \Response::json((object) $statusObj);
  //   }

  //   public function getCode(){
  //       $input = \Input::all();
  //       $rules = [
  //           'email' => 'required|email',
  //       ];

  //       $message = [
  //           'email.required' => "Sorry Email Required",
  //           'email.email' => "Sorry Email Must Be Email Type",
  //       ];
  //       $validate = \Validator::make($input, $rules, $message);

  //       if($validate->fails()){
  //           return \TraitsFunc::ErrorMessage($validate->messages()->first(), 400);
  //       }

  //       $userObj = User::checkUserByEmail($input['email']);
  //       if ($userObj == null) {
  //           return \TraitsFunc::ErrorMessage("Email Not Found", 400);   
  //       }

  //       $code = rand(1000,10000);

  //       $userObj->code = $code;
  //       $userObj->code_verified = 0;
  //       $userObj->code_expire = date("Y-m-d H:i:s", strtotime('+24 hours'));
  //       $userObj->save();
        
  //       $emailData['firstName'] = $userObj->Profile->display_name;
  //       $emailData['code'] = $code;
  //       $emailData['subject'] = "Aliensera.com - Reset Your Password";
  //       $emailData['to'] = $userObj->email;
  //       $emailData['template'] = "emailUsers.resetPassword";
  //       \Helper::SendMail($emailData);

  //       $statusObj['status'] = \TraitsFunc::SuccessMessage("We Sent Code To Your Email");
  //       return \Response::json((object) $statusObj); 
  //   }

  //   public function checkEmailCode() {
  //       $input = \Input::all();

  //       $rules = [
  //           'email' => 'required|email',
  //           'code' => 'required',
  //       ];

  //       $message = [
  //           'email.required' => "Sorry email Required",
  //           'email.email' => "Please check email format",
  //           'code.required' => "Sorry Code Required",
  //       ];

  //       $validate = \Validator::make($input, $rules, $message);

  //       if($validate->fails()){
  //           return \TraitsFunc::ErrorMessage($validate->messages()->first(), 400);
  //       }

  //       $email = $input['email'];
  //       $userObj = User::checkUserByEmail($email);
  //       if ($userObj == null) {
  //           $statusObj['status'] = \TraitsFunc::SuccessMessage("Email Not Found");
  //           return \Response::json((object) $statusObj);
  //       }

  //       if($userObj->code != $input['code']){
  //           return \TraitsFunc::ErrorMessage("Sorry Code Mismatch", 400);
  //       }

  //       $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
  //       if($now > $userObj->code_expire){
  //           return \TraitsFunc::ErrorMessage("Sorry Code Expired", 400);   
  //       }

  //       if($userObj->code_verified == 1){
  //           return \TraitsFunc::ErrorMessage("Sorry Code Verified Before", 400);
  //       }

  //       $statusObj['status'] = \TraitsFunc::SuccessMessage("You Can Reset Password Now");
  //       return \Response::json((object) $statusObj);
  //   }    

  //   public function doResetPassword() {
  //       $input = \Input::all();

  //       $rules = [
  //           'email' => 'required|email',
  //           'password' => 'required|confirmed',
  //           'password_confirmation' => 'required'
  //       ];

  //       $message = [
  //           'email.required' => "Sorry email Required",
  //           'email.email' => "Please check email format",
  //           'password.required' => "Sorry Password Required",
  //           'password_confirmation.required' => "Sorry Password Confirmation Required",
  //       ];

  //       $validate = \Validator::make($input, $rules, $message);

  //       if($validate->fails()){
  //           return \TraitsFunc::ErrorMessage($validate->messages()->first(), 400);
  //       }

  //       $password = $input['password'];
  //       $userObj = User::NotDeleted()->where('email', $input['email'])->first();
        
  //       if ($userObj == null) {
  //           $statusObj['status'] = \TraitsFunc::SuccessMessage("Sorry please check your code again or it could expired");
  //           return \Response::json((object) $statusObj);
  //       }

  //       $userObj->password = Hash::make($password);
  //       $userObj->save();

  //       $statusObj['status'] = \TraitsFunc::SuccessMessage("Reset Password Success");
  //       return \Response::json((object) $statusObj);
  //   }
}
