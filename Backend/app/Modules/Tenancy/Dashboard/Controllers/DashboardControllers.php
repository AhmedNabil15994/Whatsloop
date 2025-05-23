<?php namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\WebActions;
use App\Models\Group;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserTheme;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\Addons;
use App\Models\ExtraQuota;
use App\Models\Variable;
use App\Models\Invoice;
use App\Models\CentralUser;
use App\Models\CentralChannel;
use App\Models\PaymentInfo;
use App\Models\UserChannels;
use App\Models\FAQ;
use App\Models\CentralVariable;
use App\Models\Changelog;
use App\Models\CentralCategory;
use App\Models\Department;
use App\Models\Rate;
use App\Models\ModTemplate;
use App\Models\UserStatus;
use App\Models\Contact;
use App\Models\ChatMessage;
use App\Models\Bundle;
use App\Models\ChatEmpLog;
use App\Models\ChatDialog;
use App\Models\OAuthData;

class DashboardControllers extends Controller {

    use \TraitsFunc;

    public function hneehm(){
        $data['color'] = Variable::getVar('COLOR');
        $data['size'] = Variable::getVar('SIZE');
        $data['height_space'] = Variable::getVar('HEIGHT_SPACE');
        $data['extra_msg'] = Variable::getVar('EXTRA_MSG');
        $data['design'] = Variable::getVar('SELECTED_TEMPLATE');
        return view('Tenancy.Dashboard.Views.V5.hneehm')->with('data',(object) $data);
    }

    public function getImageDimensions(){
        $input = \Request::all();
        $data = [];
        if(isset($input['image']) && !empty($input['image'])){
            $data = getimagesize($input['image']);
        }
        return $data;
    }

    public function postImageDimensions(){
        $input = \Request::all();
        $varObj = Variable::where('var_key','TEMPLATE'.$input['imgId'])->first();
        if(!$varObj){
            $varObj = new Variable;
        }
        $varObj->var_key = 'TEMPLATE'.$input['imgId'];
        $varObj->var_value = $input['width'].','.$input['height'];
        $varObj->save();
        return 1;
    }

    public function postHneehm(){
        $input = \Request::all();
        if(!isset($input['design']) || empty($input['design'])){
            \Session::flash('error','You must select at least one design');
            return redirect()->back()->withInput();
        }
        $varObj = Variable::where('var_key','SELECTED_TEMPLATE')->first();
        if(!$varObj){
            $varObj = new Variable;
        }
        $varObj->var_key = 'SELECTED_TEMPLATE';
        $varObj->var_value = $input['design'];
        $varObj->save();

        $varObj = Variable::where('var_key','COLOR')->first();
        if(!$varObj){
            $varObj = new Variable;
        }
        $varObj->var_key = 'COLOR';
        $varObj->var_value = $input['color'];
        $varObj->save();

        $varObj = Variable::where('var_key','SIZE')->first();
        if(!$varObj){
            $varObj = new Variable;
        }
        $varObj->var_key = 'SIZE';
        $varObj->var_value = $input['size'];
        $varObj->save();

        $varObj = Variable::where('var_key','EXTRA_MSG')->first();
        if(!$varObj){
            $varObj = new Variable;
        }
        $varObj->var_key = 'EXTRA_MSG';
        $varObj->var_value = $input['extra_msg'];
        $varObj->save();

        Session::flash('success',trans('main.editSuccess'));
        return redirect()->back();
    }

    public function menu(){
        $data = []; 
        Session::forget('check_user_id');
        return view('Tenancy.Dashboard.Views.V5.menu')->with('data',(object) $data);
    }

    public function Dashboard(){   
        $mainUser = User::first();
        $arr = [
            TENANT_ID,
            $mainUser->domain,
            $mainUser->id,
            $mainUser->phone,

        ];
        $oauthDataObj = OAuthData::where('type','salla')->where('domain','null')->where('phone',$arr[3])->first();
        if($oauthDataObj){
            $oauthDataObj->user_id = $arr[2];
            $oauthDataObj->domain = $arr[1];
            $oauthDataObj->tenant_id = $arr[0];
            $oauthDataObj->save();
        }
        
        // $base_url = 'https://accounts.salla.sa/oauth2/token';

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $base_url);
        // curl_setopt($ch, CURLOPT_POST, TRUE);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        //         'client_id'     => '1ad1ad373c0234a41c52a34556e4db3f',
        //         'client_secret' => 'a133165c2690b7dbc04b0854b2a2bab2',
        //         'username'      => '685f4xfyylmlwtxr@email.partners',
        //         'grant_type'    => 'authorization code',
        // ));

        // $data = curl_exec($ch);

        // $auth_string = json_decode($data, true);
        // dd($auth_string);
















        $varObj = Variable::getVar('QRIMAGE');
        if($varObj){
            $sendStatus = 0;
        }else{
            $sendStatus = 100;
        }
        $userStatusObj = UserStatus::orderBy('id','DESC')->first();
        if($userStatusObj!= null && in_array($userStatusObj->status,[2,3,4])){
            $sendStatus = 0;
        }else{
            $sendStatus = 100;
        }

        $messages = (object) ChatMessage::lastMessages();
        
        $data['allDialogs'] = ChatDialog::whereHas('LastMessage')->count();
        $data['data'] = $messages->data;
        $data['pagination'] = $messages->pagination;
        $data['sentMessages'] = ChatMessage::where('fromMe',1)->count();
        $data['incomingMessages'] = ChatMessage::count() - $data['sentMessages'];
        $data['contactsCount'] = Contact::NotDeleted()->whereHas('NotDeletedGroup')->count();
        $data['sendStatus'] = $sendStatus;
        $data['serverStatus'] = 100;
        $data['lastContacts'] = Contact::lastContacts()['data'];
        $data['logs'] = ChatEmpLog::dataList()['data'];
        return view('Tenancy.Dashboard.Views.V5.dashboard')->with('data',(object) $data);
    }

    public function getChartData($start=null,$end=null,$moduleName){
        $input = \Request::all();
        
        if(isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])){
            $start = $input['from'];
            $end = $input['to'];
        }

        $datediff = strtotime($end) - strtotime($start);
        $daysCount = round($datediff / (60 * 60 * 24));
        $datesArray = [];
        $datesArray[0] = $start;

        if($daysCount > 2){
            for($i=0;$i<$daysCount;$i++){
                $datesArray[$i] = date('Y-m-d',strtotime($start.'+'.$i."day") );
            }
            $datesArray[$daysCount] = $end;  
        }else{
            for($i=1;$i<24;$i++){
                $datesArray[$i] = date('Y-m-d H:i:s',strtotime($start.'+'.$i." hour") );
            }
        }

        $chartData = [];
        $dataCount = count($datesArray);
        $module = "\App\Models".$moduleName;
        for($i=0;$i<$dataCount;$i++){
            if($dataCount == 1){
                $count = $module::where('created_at','>=',$datesArray[0].' 00:00:00')->where('created_at','<=',$datesArray[0].' 23:59:59')->count();
            }else{
                if($i < count($datesArray)){
                    $count = $module::where('created_at','>=',$datesArray[$i].' 00:00:00')->where('created_at','<=',$datesArray[$i].' 23:59:59')->count();
                }
            }
            $chartData[0][$i] = $datesArray[$i];
            $chartData[1][$i] = $count;
        }
        return $chartData;
    }

    public function changeChannel(Request $request){
        if($request->ajax()){
            $userObj = User::getData(User::getOne(USER_ID));
            if(!Session::has('channel')){
                if(in_array($request->channel, $userObj->channelIDS)){
                    Session::put('channel', $request->channel);
                }
            }else{
                Session::forget('channel');
                if(in_array($request->channel, $userObj->channelIDS)){
                    Session::put('channel', $request->channel);
                }
            } 
            return Redirect::back();
        }
    }

    public function changeTheme(Request $request){
        if($request->ajax()){
            $type = $request->type;
            $value = $request->value;
            $dataObj = UserTheme::where('user_id',USER_ID)->first();
            if(!$dataObj){
                $dataObj = new UserTheme;
            }
            $dataObj->user_id = USER_ID;
            $dataObj->$type = $value;
            $dataObj->save();
            return Redirect::back();
        }
    }

    public function changeThemeToDefault(Request $request){
        if($request->ajax()){
            $type = $request->type;
            $value = $request->value;
            $dataObj = UserTheme::where('user_id',USER_ID)->first();
            $dataObj->theme = 'light';
            $dataObj->width = 'fluid';
            $dataObj->menus_position = 'fixed';
            $dataObj->sidebar_size = 'default';
            $dataObj->user_info = 'false';
            $dataObj->top_bar = 'dark';
            $dataObj->save();
            return Redirect::back();
        }
    }

    public function faqs(){   
        $data = FAQ::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.V5.faqs')->with('data',(object) $data);
    }

    public function helpCenter(){   
        $data = FAQ::dataList(1);
        $data['changeLogs'] = Changelog::dataList(1)['data'];
        $data['categories'] = CentralCategory::dataList(1)['data'];
        $data['email'] = CentralVariable::getVar('TECH_EMAIL');
        $data['phone'] = CentralVariable::getVar('TECH_PHONE');
        $data['pin_code'] = $this->genNewPinCode(IS_ADMIN ? USER_ID : User::first()->id);
        $data['clients'] = CentralUser::NotDeleted()->where('status',1)->where('global_id',GLOBAL_ID)->where('group_id',0)->get();
        $data['departments'] = Department::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.V5.helpCenter')->with('data',(object) $data);
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

    public function addRate(){
        $input = \Request::all();

        $rateObj = Rate::NotDeleted()->where('user_id',USER_ID)->where('changelog_id',$input['id'])->first();
        if($rateObj){
            return \TraitsFunc::ErrorMessage(trans('main.youRated'));
        }
        $rateObj = new Rate();
        $rateObj->user_id = USER_ID;
        $rateObj->tenant_id = TENANT_ID;
        $rateObj->changelog_id = (int) $input['id'];
        $rateObj->comment = (string) $input['comment'];
        $rateObj->rate = (int) $input['rate'];
        $rateObj->created_by = USER_ID;
        $rateObj->created_at = DATE_TIME;
        $rateObj->save();

        WebActions::newType(1,'Rate');
        return \TraitsFunc::SuccessResponse(trans('main.addSuccess'));
    }

}
