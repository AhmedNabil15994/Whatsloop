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
class ExtraControllers extends Controller {

    use \TraitsFunc;
    
    public function checkClientAvailability($id){
        if($id == null ){
            return redirect(404);
        }

        $oauthDataObj =  OAuthData::find(base64_decode($id));
        if(!$oauthDataObj){
            return redirect(404);
        }


        $data['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        return view('Central.Auth.Views.V5.checkClientAvailability')->with('data',(object) $data);
    }

    public function postCheckClientAvailability($id,Request $request){
        $input = \Request::all();

        if($id == null ){
            return redirect(404);
        }

        $oauthDataObj =  OAuthData::find(base64_decode($id));
        if(!$oauthDataObj){
            return redirect(404);
        }

        if(!isset($input['phone']) || empty($input['phone'])){
            Session::flash('error', trans('auth.phoneValidation'));
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
        return view('Central.Auth.Views.V5.checkClientCode')->with('data',(object) $dataArr);
    }

    public function checkClientAvailabilityCode($id){
        $input = \Request::all();
        
        $clientRequestObj = ClientsRequests::getOne($input['phone']);
        $dataArr['phone'] = $input['phone'];
        $dataArr['code'] = \Helper::getCountryCode() ? \Helper::getCountryCode()->countryCode : 'sa';
        if(!$clientRequestObj){
            Session::flash('error', trans('main.userNotFound'));
            return view('Central.Auth.Views.V5.checkClientCode')->with('data',(object) $dataArr);
        }

        if($clientRequestObj->code != $input['code']){
            Session::flash('error', trans('auth.codeError'));
            return view('Central.Auth.Views.V5.checkClientCode')->with('data',(object) $dataArr);
        }



        $userObj = CentralUser::checkUserBy('phone',$input['phone']);
        if($userObj && $userObj->group_id == 0){
            $tenant = Tenant::where('phone',$userObj->phone)->first();
            $token = tenancy()->impersonate($tenant,$userObj->id,'/dashboard');

            return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
                'token' => $token
            ]));
        }else{
            Session::put('checked_user_phone',$input['phone']);
            return redirect()->to('/register');
        }
    }

  
}
