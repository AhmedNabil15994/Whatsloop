<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GroupMsg;
use App\Models\GroupNumber;
use App\Models\Contact;
use App\Models\ChatMessage;
use App\Models\ContactReport;
use App\Models\UserExtraQuota;
use App\Models\UserAddon;
use App\Models\Bot;
use App\Models\BotPlus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use App\Jobs\GroupMessageJob;
use App\Jobs\CheckWhatsappJob;
use App\Jobs\FixReport;
use DataTables;
use Storage;
use Redirect;

class FeedbackControllers extends Controller {

    use \TraitsFunc;

    public function checkPerm(){
        // $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        // if(in_array(13,$disabled)){
        //     $dis = 1;
        // }
        return $dis;
    }

    public function getData(){
        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $data['mainData'] = [
            'title' => trans('main.feedback'),
            'url' => 'feedback',
            'name' => 'feedback',
            'nameOne' => 'feedback',
            'modelName' => 'Feedback',
            'icon' => 'mdi mdi-send',
            'sortName' => '',
            'addOne' => trans('main.feedback'),
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
            'channel' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $channels,
                'label' => trans('main.channel'),
            ],
        ];

        $data['tableData'] = [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            // 'group' => [
            //     'label' => trans('main.group'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'group_id',
            //     'anchor-class' => '',
            // ],
            // 'message_type_text' => [
            //     'label' => trans('main.message_type'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'message_type',
            //     'anchor-class' => '',
            // ],
            // 'message' => [
            //     'label' => trans('main.message_content'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'message',
            //     'anchor-class' => '',
            // ],
            // 'sent_type' => [
            //     'label' => trans('main.sent_type'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'sent_type',
            //     'anchor-class' => '',
            // ],
            // 'contacts_count' => [
            //     'label' => trans('main.contacts_count'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'contacts_count',
            //     'anchor-class' => '',
            // ],
            // 'sent_msgs' => [
            //     'label' => trans('main.sent_msgs'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'sent_msgs',
            //     'anchor-class' => '',
            // ],
            // 'unsent_msgs' => [
            //     'label' => trans('main.unsent_msgs'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'unsent_msgs',
            //     'anchor-class' => '',
            // ],
            // 'viewed_msgs' => [
            //     'label' => trans('main.viewed_msgs'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'viewed_msgs',
            //     'anchor-class' => '',
            // ],
            // 'publish_at' => [
            //     'label' => trans('main.sentDate'),
            //     'type' => '',
            //     'className' => '',
            //     'data-col' => 'publish_at',
            //     'anchor-class' => '',
            // ],
            'actions' => [
                'label' => trans('main.actions'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
        ];

        return $data;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = GroupMsg::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function add() {

        // if($this->checkPerm()){
        //     Session::flash('error','Please Re-activate Group Messages Addon');
        //     return redirect()->back();
        // }

        $startDay = strtotime(date('Y-m-d 00:00:00'));
        $endDay = strtotime(date('Y-m-d 23:59:59'));
        $messagesCount = ChatMessage::where('fromMe',1)->whereNotIn('status',[null,'APP'])->where('time','>=',$startDay)->where('time','<=',$endDay)->count();
        $dailyCount = Session::get('dailyMessageCount');
        $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,1);
        if($dailyCount + $extraQuotas < $messagesCount){
            Session::flash('error', trans('main.messageQuotaError'));
            return redirect()->back()->withInput();
        }

        $checkZid = UserAddon::checkUserAvailability(USER_ID,4);
        $checkSalla = UserAddon::checkUserAvailability(USER_ID,5);

        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.feedback') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['contacts'] = Contact::NotDeleted()->get();
        $data['contacts'] = reset($data['contacts']);
        $data['zidContacts'] = $checkZid ? $this->reformAddonClients(2) : [];
        $data['sallaContacts'] = $checkSalla ? $this->reformAddonClients(1) : [];
        $data['checkZid'] = $checkZid != null ? 1 : 0;
        $data['checkSalla'] = $checkSalla != null ? 1 : 0;        
        $data['allClients'] = array_slice(array_merge($data['contacts'],$data['zidContacts'],$data['sallaContacts']), 0, 20);
        return view('Tenancy.Feedback.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        dd($input);
    }

    public function reformAddonClients($type){
        $objs = [];
        if($type == 1){
            $data = \DB::table('salla_customers')->get();
            foreach ($data as $key => $value) {
                $dataObj = new \stdClass();
                $dataObj->id = $value->id;
                $dataObj->name = $value->first_name .' '.$value->last_name;
                $dataObj->phone = $value->mobile_code . '' . $value->mobile;
                $dataObj->email = $value->email;
                $dataObj->gender = $value->gender;
                $dataObj->image = $value->avatar;
                $dataObj->city = $value->city;
                $dataObj->country = $value->country;
                $dataObj->currency = $value->currency;
                $dataObj->location = $value->location;
                $dataObj->updated_at = date('Y-m-d H:i:s' , strtotime($value->updated_at));
                $objs[] = $dataObj;
            }
        }else{
            $data = \DB::table('zid_customers')->get();
            foreach ($data as $key => $value) {
                $dataObj = new \stdClass();
                $dataObj->id = $value->id;
                $dataObj->name = $value->name;
                $dataObj->phone = $value->mobile;
                $dataObj->email = $value->email;
                $dataObj->image = asset('images/not-available.jpg');
                $dataObj->city = isset($value->city) && !empty($value->city) ? unserialize($value->city)['name'] : '';
                $dataObj->country = isset($value->city) && !empty($value->city) ? unserialize($value->city)['country_name'] : '';
                $objs[] = $dataObj;
            }
        }
        
        return $objs;
    }

}
