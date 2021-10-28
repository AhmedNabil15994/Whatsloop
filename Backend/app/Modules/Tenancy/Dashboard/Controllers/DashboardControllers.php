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

class DashboardControllers extends Controller {

    use \TraitsFunc;

    public function menu(){
        $data = []; 
        Session::forget('check_user_id');
        return view('Tenancy.Dashboard.Views.V5.menu')->with('data',(object) $data);
    }

    public function Dashboard(){   
        $varObj = Variable::getVar('QRIMAGE');
        if($varObj){
            $sendStatus = 100;
        }else{
            $sendStatus = 0;
        }

        $messages = (object) ChatMessage::lastMessages();
        
        $data['allMessages'] = ChatMessage::count();
        $data['data'] = $messages->data;
        $data['pagination'] = $messages->pagination;
        $data['sentMessages'] = ChatMessage::where('fromMe',1)->count();
        $data['incomingMessages'] = $data['allMessages'] - $data['sentMessages'];
        $data['contactsCount'] = Contact::NotDeleted()->count();
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
        $data['pin_code'] = $this->genNewPinCode(USER_ID);
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
