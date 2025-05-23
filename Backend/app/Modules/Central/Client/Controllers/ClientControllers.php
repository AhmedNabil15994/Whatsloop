<?php namespace App\Http\Controllers;

use App\Models\CentralUser;
use App\Models\Domain;
use App\Models\CentralGroup;
use App\Models\CentralChannel;
use App\Models\Membership;
use App\Models\Tenant;
use App\Models\Addons;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\UserAddon;
use App\Models\PaymentInfo;
use App\Models\Variable;
use App\Models\UserChannels;
use App\Models\CentralWebActions;
use App\Models\CentralTicket;
use App\Models\UserStatus;
use App\Models\Contact;
use App\Models\Invoice;
use App\Models\WebActions;
use App\Models\ChatDialog;
use App\Models\Category;
use App\Models\ContactLabel;
use App\Models\ContactReport;
use App\Models\ExtraQuota;
use App\Models\UserExtraQuota;
use App\Models\UserData;
use App\Models\BankAccount;
use App\Models\Product;
use App\Models\Order;
use App\Models\CentralVariable;
use App\Models\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Jobs\TransferDays;

use DataTables;
use Storage;

use App\Jobs\SyncMessagesJob;
use App\Jobs\SyncDialogsJob;
use App\Jobs\ReadChatsJob;


class ClientControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $groups = CentralGroup::dataList(1)['data'];
        $userObj = CentralUser::getData(CentralUser::getOne(USER_ID));
        $channels = CentralChannel::dataList()['data'];
        $data['mainData'] = [
            'title' => trans('main.clients'),
            'url' => 'clients',
            'name' => 'clients',
            'nameOne' => 'client',
            'modelName' => 'CentralUser',
            'icon' => 'fa fa-users',
            'sortName' => 'name',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
            ],
            'name' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.name'),
            ],
            'email' => [
                'type' => 'email',
                'class' => 'form-control m-input',
                'index' => '2',
                'label' => trans('main.email'),
            ],
            'phone' => [
                'type' => 'number',
                'class' => 'form-control m-input',
                'index' => '3',
                'label' => trans('main.phone'),
            ],
            'domain' => [
                'type' => 'text',
                'class' => 'form-control',
                'index' => '4',
                'label' => trans('main.domain'),
            ],
            'channels' => [
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
            'name' => [
                'label' => trans('main.name'),
                'type' => '',
                'className' => '',
                'data-col' => 'name',
                'anchor-class' => '',
            ],
            'email' => [
                'label' => trans('main.email'),
                'type' => '',
                'className' => '',
                'data-col' => 'email',
                'anchor-class' => '',
            ],
            'phone' => [
                'label' => trans('main.phone'),
                'type' => '',
                'className' => '',
                'data-col' => 'phone',
                'anchor-class' => '',
            ],
            'domain' => [
                'label' => trans('main.domain'),
                'type' => '',
                'className' => '',
                'data-col' => 'domain',
                'anchor-class' => '',
            ],
            'channelCodes' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => ' ',
                'data-col' => 'channels',
                'anchor-class' => '',
            ],
            'leftDays' => [
                'label' => trans('main.leftDays'),
                'type' => '',
                'className' => '',
                'data-col' => 'leftDays',
                'anchor-class' => '',
            ],
            'balance' => [
                'label' => trans('main.balance'),
                'type' => 'text',
                'className' => 'edits',
                'data-col' => 'balance',
                'anchor-class' => 'editable',
            ],
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

    protected function validateInsertObject($input){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
            'password' => 'required|min:6',
            'email' => 'required',
            'domain' => 'required',
            'membership_id' => 'required',
            'duration_type' => 'required',
        ];

        $message = [
            'name.required' => trans('main.nameValidate'),
            'phone.required' => trans('main.phoneValidate'),
            'password.required' => trans('main.passwordValidate'),
            'password.min' => trans('main.passwordValidate2'),
            'email.required' => trans('main.emailValidate'),
            'domain.required' => trans('main.domainValidate'),
            'membership_id.required' => trans('main.membershipValidate'),
            'duration_type.required' => trans('main.durationTypeValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    protected function validateUpdateObject($input){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
            'domain' => 'required',
            'membership_id' => 'required',
            'duration_type' => 'required',
        ];

        $message = [
            'name.required' => trans('main.nameValidate'),
            'phone.required' => trans('main.phoneValidate'),
            'password.min' => trans('main.passwordValidate2'),
            'email.required' => trans('main.emailValidate'),
            'domain.required' => trans('main.domainValidate'),
            'membership_id.required' => trans('main.membershipValidate'),
            'duration_type.required' => trans('main.durationTypeValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = CentralUser::dataList('domains');
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Central.Client.Views.index')->with('data', (object) $data);
    }

    public function transferDay(){
        shell_exec("/usr/local/bin/php /home/wloop/public_html/artisan transfer:days");
        \Session::flash('success', trans('main.inPrgo'));
        return redirect()->back();
    }

    public function pushAddonSetting(){
        shell_exec("/usr/local/bin/php /home/wloop/public_html/artisan push:addonSetting");
        \Session::flash('success', trans('main.inPrgo'));
        return redirect()->back();
    }

    public function pushChannelSetting(){
        shell_exec("/usr/local/bin/php /home/wloop/public_html/artisan push:channelSetting");
        \Session::flash('success', trans('main.inPrgo'));
        return redirect()->back();
    }

    public function setInvoices(){
        shell_exec("/usr/local/bin/php /home/wloop/public_html/artisan set:invoices");
        \Session::flash('success', trans('main.inPrgo'));
        return redirect()->back();
    }

    public function screenshot($id){
        // Perform Whatsapp Integration
        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $data['data'] = CentralUser::getData($userObj);
        $channel = CentralChannel::first();
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $channelObj = UserChannels::first();
        tenancy()->end($tenant);

        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->screenshot();
        $result = $updateResult->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        $dataList['image'] = str_replace('/engine','/engine/public',$result['data']['image']);
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);           
    }

    public function reconnect($id){
        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $data['data'] = CentralUser::getData($userObj);
        $channel = CentralChannel::first();
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $channelObj = UserChannels::first();
        tenancy()->end($tenant);

        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->reboot();
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }
        Session::flash('success',trans('main.reconnectDone'));
        return redirect()->back();
    }

    public function closeConn($id){
        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $data['data'] = CentralUser::getData($userObj);
        $channel = CentralChannel::first();
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $channelObj = UserChannels::first();
        tenancy()->end($tenant);

        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->logout();
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }
        Session::flash('success',trans('main.logoutDone'));
        return redirect()->back();
    }
    public function sync($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $lastMessageObj = ChatMessage::orderBy('time','DESC')->first();
        if($lastMessageObj != null){
            $data['min_time'] = $lastMessageObj->time - 7200;
        }
        $updateResult = $mainWhatsLoopObj->messages($data);
        $result = $updateResult->json();
    
        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }

        if($result['data'] && $result['data']['messages']){
            try {
                dispatch(new SyncMessagesJob($result['data']['messages']))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
            Session::flash('success',trans('main.syncInProgress'));
        }

        tenancy()->end($tenant);
        return redirect()->back();
    }

    public function syncAll($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        // if($userObj->is_old != 1){
        //     $lastMessageObj = ChatMessage::where('id','!=',null)->delete();
        // }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->messages($data);
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }

        if($result['data'] && $result['data']['messages']){
            try {
                dispatch(new SyncMessagesJob($result['data']['messages']))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
            Session::flash('success',trans('main.syncInProgress'));
        }

        tenancy()->end($tenant);
        return redirect()->back();
    }

    public function syncDialogs($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        // if($userObj->is_old != 1){
        //     ChatDialog::where('id','!=',null)->delete();
        // }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->dialogs($data);
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }

        if($result['data'] && $result['data']['dialogs']){
            try {
                dispatch(new SyncDialogsJob($result['data']['dialogs']))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }            
            Session::flash('success',trans('main.inPrgo'));
        }

        tenancy()->end($tenant);
        return redirect()->back();
    }

    public function syncLabels($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        if($userObj->is_old != 1){
            ChatDialog::where('id','!=',null)->delete();
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->labelsList($data);
        $updateResult = $updateResult->json();

        if(isset($updateResult['data']) && !empty($updateResult['data'])){
            $labels = $updateResult['data']['labels'];
            $value = 1;
            if(empty($labels)){
                $value = 0;
            }

            $varObj = Variable::where('var_key','BUSINESS')->first();
            if(!$varObj){
                $varObj = new Variable;
                $varObj->var_key = 'BUSINESS';
            }
            $varObj->var_value = $value;
            $varObj->save();

            $channelObj = CentralChannel::where('global_user_id',$userObj->global_id)->first();
            foreach($labels as $label){
                $labelObj = Category::NotDeleted()->where('labelId',$label['id'])->first();
                if(!$labelObj){
                    $labelObj = new Category;
                    $labelObj->channel = $channelObj->instanceId;
                    $labelObj->sort = Category::newSortIndex();
                }
                $labelObj->labelId = $label['id'];
                $labelObj->name_ar = $label['name'];
                $labelObj->name_en = $label['name'];
                $labelObj->color_id = Category::getColorData($label['hexColor'])[0];
                $labelObj->status = 1;
                $labelObj->save();
            }
        }

        tenancy()->end($tenant);
        Session::flash('success',trans('main.inPrgo'));
        return redirect()->back();
    }

    public function syncOrdersProducts($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $userObj = User::first();
        if($userObj->is_old != 1){
            Order::truncate();
            Product::truncate();
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->messages($data);
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }

        if($result['data'] && $result['data']['messages']){
            try {
                dispatch(new SyncMessagesJob($result['data']['messages']))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
            Session::flash('success',trans('main.syncInProgress'));
        }

        tenancy()->end($tenant);
        return redirect()->back();
    }


    public function restoreAccountSettings($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $mainWhatsLoopObj = new \MainWhatsLoop();
        // // Update User With Settings For Whatsapp Based On His Domain
        $domain = User::first()->domain;
        $myData = [
            'sendDelay' => '0',
            'webhookUrl' => str_replace('://', '://'.$domain.'.', config('app.BASE_URL')).'/whatsloop/webhooks/messages-webhook',
            'instanceStatuses' => 1,
            'webhookStatuses' => 1,
            'statusNotificationsOn' => 1,
            'ackNotificationsOn' => 1,
            'chatUpdateOn' => 1,
            'ignoreOldMessages' => 1,
            'videoUploadOn' => 1,
            'guaranteedHooks' => 1,
            'parallelHooks' => 1,
        ];
        $updateResult = $mainWhatsLoopObj->postSettings($myData);
        $result = $updateResult->json();

        $updateResult = $mainWhatsLoopObj->clearInstance();
        $result = $updateResult->json();
    
        $userObj = User::first();
        $centralUser = CentralUser::getOne($userObj->id);
        
        $userObj->setting_pushed = 0;
        $userObj->save();
        tenancy()->end($tenant);

        $centralUser->setting_pushed = 0;
        $centralUser->save();
        
        Variable::whereIn('var_key',[
            'MODULE_1','MODULE_2','MODULE_3','MODULE_4','MODULE_5',
            'MODULE_6','MODULE_7','MODULE_8','MODULE_9',
        ])->update(['var_value'=>0]);   

        // if($userObj->is_old != 1){
            Contact::where('id','!=',null)->delete();
            Category::where('id','!=',null)->delete();
            ChatMessage::where('id','!=',null)->delete();
            ChatDialog::where('id','!=',null)->delete();
            ContactLabel::where('id','!=',null)->delete();
            ContactReport::where('id','!=',null)->delete();
            UserStatus::where('id','!=',null)->delete();
        // }
     
        Session::flash('success',trans('main.logoutDone'));
        return redirect()->back();
    }

    public function read($id,$status){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $status = (int) $status;
        if(!in_array($status, [0,1])){
            return redirect('404');
        }

        $sending_status_text = 2;
        if($status == 1){
            $sending_status_text = 3;
        }

        $messages = ChatMessage::where('fromMe',0)->groupBy('chatId')->pluck('chatId');
        ChatMessage::whereIn('chatId',reset($messages))->update(['sending_status' => $sending_status_text]);
        try {
            dispatch(new ReadChatsJob(reset($messages),$status))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }

        tenancy()->end($tenant);
        Session::flash('success',trans('main.inPrgo'));
        return redirect()->back();
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $data['data'] = CentralUser::getData($userObj);

        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $data['paymentInfo'] = PaymentInfo::where('user_id',$id)->first();
        tenancy()->end($tenant);

        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.clients') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['memberships'] = Membership::dataList(1)['data'];
        $data['addons'] = Addons::dataList(1)['data'];
        $data['userAddons'] = UserAddon::getDataForUser($id);
        return view('Central.Client.Views.edit')->with('data', (object) $data);      
    }

    public function view($id) {
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        
        $data['data'] = CentralUser::getData($userObj);
        if($data['data']->domain == ''){
            return redirect()->back();
        }
        
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
            $data['paymentInfo'] = PaymentInfo::where('user_id',$id)->first();
            $data['messages'] = ChatMessage::generateObj(ChatMessage::where('fromMe',1)->orderBy('time','DESC')->take(10))['data'];
            $channelObj = UserChannels::first();
            if($channelObj){
                $whatsLoopObj = new \MainWhatsLoop($channelObj->instanceId,$channelObj->instanceToken);
                $updateResult = $whatsLoopObj->me();
                $result = $updateResult->json();
            }
            $lastStatus = UserStatus::orderBy('id','DESC')->first();

            $data['client'] = $userObj;
            $data['me'] =  isset($result) && isset($result['data']) ? (object) $result['data'] : [];
            $data['status'] = $lastStatus ? UserStatus::getData($lastStatus) : [];
            $data['allMessages'] = ChatMessage::count();
            $data['sentMessages'] = ChatMessage::where('fromMe',1)->count();
            $data['incomingMessages'] = $data['allMessages'] - $data['sentMessages'];
            $data['channel'] = $channelObj ? UserChannels::getData($channelObj) : [];
            $data['contactsCount'] = Contact::NotDeleted()->count();

        tenancy()->end($tenant);
        
        // // Update User With Settings For Whatsapp Based On His Domain
        $myData = [
            'sendDelay' => '0',
            'webhookUrl' => str_replace('://', '://'.$data['data']->domain.'.', \URL::to('/')).'/whatsloop/webhooks/messages-webhook',
            'instanceStatuses' => 1,
            'webhookStatuses' => 1,
            'statusNotificationsOn' => 1,
            'ackNotificationsOn' => 1,
            'chatUpdateOn' => 1,
            'ignoreOldMessages' => 1,
            'videoUploadOn' => 1,
            'guaranteedHooks' => 1,
            'parallelHooks' => 1,
        ];
        if($channelObj){
            $channelObj = CentralChannel::where('id',$channelObj->id)->first();
            if($channelObj && $channelObj->instanceId != null){
                $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->instanceId,$channelObj->instanceToken);
                if($userObj->setting_pushed == 0){
                    $updateResult = $mainWhatsLoopObj->postSettings($myData);
                    $result = $updateResult->json();
                    $userObj->setting_pushed = 1;
                    $userObj->save();
                    $settingsArr = $myData;
                }else{
                    $testResult = $mainWhatsLoopObj->settings([]);
                    $settingsArr = isset($testResult->json()['data']) ? $testResult->json()['data'] : $myData;
                }     
            }
        }

        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.clients') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['memberships'] = Membership::dataList(1)['data'];
        $data['tickets'] = CentralTicket::dataList(null,$id)['data'];
        $data['invoices'] = Invoice::dataList(null,$id)['data']; 
        $data['addons'] = Addons::dataList(1)['data'];
        $data['userAddons'] = UserAddon::getDataForUser($id);
        $data['settings'] = isset($settingsArr) ? $settingsArr : $myData;
        $data['channelSettings'] = $data['settings'];

        return view('Central.Client.Views.view')->with('data', (object) $data);      
    }

    public function updateSettings($id){
        $id = (int) $id;
        $input = \Request::all();
        unset($input['_token']);
        $myArr = [];
        foreach ($input as $key => $value) {
            if($value != null){
                $myArr[$key] = $value;
            }
        }

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $data['data'] = CentralUser::getData($userObj);

        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $channelObj = UserChannels::first();
        tenancy()->end($tenant);
        $channelObj = $channelObj != null ? CentralChannel::where('id',$channelObj->id)->first() : [];
        if($channelObj && $channelObj->instanceId != null){
            $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->instanceId,$channelObj->instanceToken);
            $settings = $mainWhatsLoopObj->postSettings($myArr);       
            $result = $settings->json();
            if($result['status']['status'] != 1){
                \Session::flash('error', $result['status']['message']);
                return back()->withInput();
            }
        }
        \Session::flash('success', trans('main.editSuccess'));
        return back()->withInput();
    }

    public function transferDays($id){
        $id = (int) $id;
        $input = \Request::all();

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $data['data'] = CentralUser::getData($userObj);
        $channel = CentralChannel::first();
        $domainObj = Domain::where('domain',$data['data']->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        tenancy()->initialize($tenant);
        $channelObj = UserChannels::first();
        tenancy()->end($tenant);

        // $mainWhatsLoopObj = new \MainWhatsLoop($channel->id,$channel->token);
        // $transferDaysData = [
        //     'receiver' => $channelObj->id,
        //     'days' => $input['days'],
        //     'source' => $channel->id,
        // ];

        // $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        // $result = $updateResult->json();

        // if($result['status']['status'] != 1){
        //     \Session::flash('error', $result['status']['message']);
        //     return back()->withInput();
        // }
        
        try {
          dispatch(new TransferDays($channel->id,$channel->token,$channelObj->id,$input['days']))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }


        // $channelObj->update(['end_date'=> date('Y-m-d' ,strtotime("+".$input['days']. " days" ,strtotime($channelObj->end_date) ))]);
        // CentralChannel::where('id',$channelObj->id)->update(['end_date'=> $channelObj->end_date]);

        \Session::flash('success', trans('main.editSuccess'));
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function invLogin($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $userObj = CentralUser::getData($userObj);
        $domainObj = Domain::where('domain',$userObj->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        $token = tenancy()->impersonate($tenant,$id,'/dashboard');
        Session::put('check_user_id',$id);
        return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'impersonate',[
            'token' => $token
        ]));
    }

    public function pinCodeLogin($id){
        $id = (int) $id;

        $userObj = CentralUser::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }
        $userObj = CentralUser::getData($userObj);
        $domainObj = Domain::where('domain',$userObj->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        $token = tenancy()->impersonate($tenant,$id,'/dashboard');
        return redirect(tenant_route($tenant->domains()->first()->domain  . '.' . request()->getHttpHost(), 'loginByCode',[
            'code' => $userObj->pin_code,
            'user_id' => $userObj->id,
        ]));
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();

        $centralUser = CentralUser::getOne($id);
        if($centralUser == null) {
            return Redirect('404');
        }
        $oldDuration = $centralUser->duration_type;
        $validate = $this->validateUpdateObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $dataObj = CentralUser::getData($centralUser);
        
        $domainObj = Domain::getOneByDomain('domain',$input['domain']);
        if($domainObj && $domainObj->domain != $dataObj->domain){
            Session::flash('error', trans('main.domainValidate2'));
            return redirect()->back()->withInput();
        }


        $userObj = CentralUser::checkUserBy('email',$input['email'],$id);
        if($userObj){
            Session::flash('error', trans('main.emailError'));
            return redirect()->back()->withInput();
        }

        if(isset($input['phone']) && !empty($input['phone'])){
            $userObj = CentralUser::checkUserBy('phone',$input['phone'],$id);
            if($userObj){
                Session::flash('error', trans('main.phoneError'));
                return redirect()->back()->withInput();
            }
        }


        $duration = strtotime('+1 month');
        $days = 3;
        if($input['duration_type'] == 2){
            $duration = strtotime('+1 year');
        }else if($input['duration_type'] == 3){
            $duration = strtotime('+3 days');
        }

        $domainObj = Domain::where('domain',$dataObj->domain)->first();
        $tenant = Tenant::find($domainObj->tenant_id);
        $centraChannelObj = CentralChannel::where('tenant_id',$domainObj->tenant_id)->first();
        // tenancy()->initialize($tenant);
        // tenancy()->end($tenant);
        $channel = [];
        if($input['duration_type'] != $oldDuration){
            $channel = [
                'id' => $centraChannelObj->id,
                'token' => $centraChannelObj['token'],
                'name' => 'Channel #'.$centraChannelObj['id'],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d',$duration),
            ];
        }

        Tenant::where('id',$domainObj->tenant_id)->update([
            'phone' => $input['phone'],
            'title' => $input['name'],
            'description' => '',
        ]);
        
        $tenant->domains()->first()->update([
            'domain' => $input['domain'],
        ]);

        if(isset($input['password']) && !empty($input['password'])){
            $rules = [
                'password' => 'required|min:6',
            ];

            $message = [
                'password.required' => trans('main.passwordValidate'),
                'password.min' => trans('main.passwordValidate2'),
            ];

            $validate = \Validator::make($input, $rules, $message);
            if($validate->fails()){
                Session::flash('error', $validate->messages()->first());
                return redirect()->back();
            }
            CentralUser::where('id',$id)->update( ['password' => \Hash::make($input['password']) ]);
            $user = $tenant->run(function() use(&$centralUser,$input){
                User::where('id',$centralUser->id)->update( ['password' => \Hash::make($input['password']) ]);
            });
        }


        CentralUser::where('id',$id)->update([
            'name' => $input['name'],
            'phone' => $input['phone'],
            'balance' => doubleval($input['balance']),
            'email' => $input['email'],
            'duration_type' => $input['duration_type'],
            'notifications' => isset($input['notifications']) && !empty($input['notifications']) && $input['notifications'] == 'on' ? 1 : 0,
            'offers' => isset($input['offers']) && !empty($input['offers']) && $input['offers'] == 'on' ? 1 : 0,
            'group_id' => 0,
            'company' => $input['company'],
            'pin_code' => $input['pin_code'],
            'emergency_number' => $input['emergency_number'],
            'two_auth' => $input['two_auth'],
            'is_active' => $input['status'],
            'is_approved' => $input['status'],
            'status' => $input['status'],
            'membership_id' => $input['membership_id'],
            'addons' => isset($input['addons']) && !empty($input['addons']) ? serialize($input['addons']) : null,
        ]);

        $addonsArr = [];
        if(isset($input['addons']) && !empty($input['addons'])){
            foreach ($input['addons'] as $key => $addonRow) {
                $addonsArr[] = $key;
                $addonsObj = UserAddon::where('user_id',$id)->where('addon_id',$key)->first();
                $addonDuration = strtotime('+1 month',  $addonsObj != null ? strtotime($addonsObj->start_date) : strtotime(date('Y-m-d')));
                $addonDurationType = 1;
                if(isset($addonRow[2])){
                    $addonDuration = strtotime('+1 year',  $addonsObj != null ? strtotime($addonsObj->start_date) : strtotime(date('Y-m-d')));
                    $addonDurationType = 2;
                }

                if($input['duration_type'] == 3){
                    $addonDuration = strtotime('+3 days', $addonsObj != null ? strtotime($addonsObj->start_date) : strtotime(date('Y-m-d')));
                    $addonDurationType = 3;
                }

                if($addonsObj){
                    $addonsObj->duration_type = $addonDurationType;
                    $addonsObj->end_date = date('Y-m-d',$addonDuration);
                    $addonsObj->save();
                }else{
                    UserAddon::create([
                        'user_id' => $centralUser->id,
                        'addon_id' => $key,
                        'duration_type' => $addonDurationType,
                        'global_user_id' =>$centralUser->global_id,
                        'status' => 1,
                        'tenant_id' => $tenant->id,
                        'start_date' => date('Y-m-d'),
                        'end_date' => date('Y-m-d',$addonDuration),
                        'created_at' => DATE_TIME,
                        'created_by' => USER_ID,
                    ]);
                }
            }
            CentralUser::where('id',$centralUser->id)->update(['addons' => !empty($addonsArr) ?  serialize($addonsArr) : null  ]);
        }

        if($input['duration_type'] != $oldDuration){
            $extraChannelData = $channel;
            $extraChannelData['tenant_id'] = $tenant->id;
            $extraChannelData['global_user_id'] = $centralUser->global_id;
            CentralChannel::where('id',$centraChannelObj->id)->update($extraChannelData);
        }

        $user = $tenant->run(function() use(&$centralUser,$channel,$input,$centraChannelObj,$addonsArr,$oldDuration){
            if($input['duration_type'] != $oldDuration){
                UserChannels::where('id',$centraChannelObj->id)->update($channel);
            }

            User::where('id',$centralUser->id)->update([
                'id' => $centralUser->id,
                'global_id' => $centralUser->global_id,
                'name' => $input['name'],
                'email' => $input['email'],
                'phone' => $input['phone'],
                'duration_type' => $input['duration_type'],
                'group_id' => 1,
                'status' => $input['status'],
                'domain' => $input['domain'],
                'sort' => 1,
                'is_active' => $input['status'],
                'is_approved' => $input['status'],
                'notifications' => isset($input['notifications']) && !empty($input['notifications']) && $input['notifications'] == 'on' ? 1:0,
                'offers' => isset($input['offers']) && !empty($input['offers']) && $input['offers'] == 'on' ? 1 : 0,
                'company' => $input['company'],
                'pin_code' => $input['pin_code'],
                'emergency_number' => $input['emergency_number'],
                'two_auth' => $input['two_auth'],
                'membership_id' => $input['membership_id'],
                'addons' => !empty($addonsArr) ?  serialize($addonsArr) : null,
            ]);

            $paymentInfoObj = PaymentInfo::where('user_id',$centralUser->id)->first();
            if($paymentInfoObj){
                $paymentInfoObj->user_id = $centralUser->id;
                $paymentInfoObj->address = $input['address'];
                $paymentInfoObj->address2 = $input['address2'];
                $paymentInfoObj->city = $input['city'];
                $paymentInfoObj->country = $input['country'];
                $paymentInfoObj->region = $input['region'];
                $paymentInfoObj->postal_code = $input['postal_code'];
                $paymentInfoObj->tax_id = $input['tax_id'];
                $paymentInfoObj->payment_method = $input['payment_method'];
                $paymentInfoObj->currency = $input['currency'];
                $paymentInfoObj->created_at = DATE_TIME;
                $paymentInfoObj->created_by = USER_ID;
                $paymentInfoObj->save();
            }

            return true;
        });

        if($input['duration_type'] != $oldDuration){
            $firstChannelObj = CentralChannel::first();
            try {
              dispatch(new TransferDays($firstChannelObj->id,$firstChannelObj->token,$channel['id'],1))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
            // $transferDaysData = [
            //     'receiver' => $channel['id'],
            //     'days' => 3,
            //     'source' => CentralChannel::first()->id,
            // ];
            // $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
            // $result = $updateResult->json();
        }

        Session::forget('photos');
        CentralWebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.clients') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['memberships'] = Membership::dataList(1)['data'];
        $data['addons'] = Addons::dataList(1)['data'];
        return view('Central.Client.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        // dd($input);
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

        $duration = strtotime('+1 month');
        $days = 3;
        if($input['duration_type'] == 2){
            $duration = strtotime('+1 year');
        }else if($input['duration_type'] == 3){
            $duration = strtotime('+3 days');
        }

        $channelObj = CentralChannel::first();
        $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
        $updateResult = $mainWhatsLoopObj->createChannel();
        $result = $updateResult->json();

    
        if($result['status']['status'] != 1){
            \Session::flash('error', $result['status']['message']);
            return back()->withInput();
        }

        $channel = [
            'id' => $result['data']['channel']['id'],
            'token' => $result['data']['channel']['token'],
            'name' => 'Channel #'.$result['data']['channel']['id'],
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d',$duration),
        ];

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
            'duration_type' => $input['duration_type'],
            'password' => Hash::make($input['password']),
            'notifications' => isset($input['notifications']) && !empty($input['notifications']) && $input['notifications'] == 'on' ? 1 : 0,
            'offers' => isset($input['offers']) && !empty($input['offers']) && $input['offers'] == 'on' ? 1 : 0,
            'group_id' => 0,
            'company' => $input['company'],
            'pin_code' => $input['pin_code'],
            'emergency_number' => $input['emergency_number'],
            'two_auth' => $input['two_auth'],
            'is_active' => $input['status'],
            'is_approved' => $input['status'],
            'status' => $input['status'],
            'membership_id' => $input['membership_id'],
            'channels' => serialize([$channel['id']]),
            'addons' => isset($input['addons']) && !empty($input['addons']) ? serialize($input['addons']) : null,
        ]);

        \DB::connection('main')->table('tenant_users')->insert([
            'tenant_id' => $tenant->id,
            'global_user_id' => $centralUser->global_id,
        ]);

        $addonsArr = [];
        if(isset($input['addons']) && !empty($input['addons'])){
            foreach ($input['addons'] as $key => $addonRow) {
                $addonsArr[] = $key;
                $addonDuration = strtotime('+1 month', strtotime(date('Y-m-d')));
                $addonDurationType = 1;
                if(isset($addonRow[2])){
                    $addonDuration = strtotime('+1 year', strtotime(date('Y-m-d')));
                    $addonDurationType = 2;
                }

                if($input['duration_type'] == 3){
                    $addonDuration = strtotime('+3 days', strtotime(date('Y-m-d')));
                    $addonDurationType = 3;
                }

                UserAddon::create([
                    'user_id' => $centralUser->id,
                    'addon_id' => $key,
                    'duration_type' => $addonDurationType,
                    'global_user_id' =>$centralUser->global_id,
                    'status' => 1,
                    'tenant_id' => $tenant->id,
                    'start_date' => $channel['start_date'],
                    'end_date' => date('Y-m-d',$addonDuration),
                    'created_at' => DATE_TIME,
                    'created_by' => USER_ID,
                ]);
            }
            CentralUser::where('id',$centralUser->id)->update(['addons' => !empty($addonsArr) ?  serialize($addonsArr) : null  ]);
        }

        $extraChannelData = $channel;
        $extraChannelData['tenant_id'] = $tenant->id;
        $extraChannelData['global_user_id'] = $centralUser->global_id;
        $generatedData = CentralChannel::generateNewKey($result['data']['channel']['id']); // [ generated Key , generated Token]
        $extraChannelData['instanceId'] = $generatedData[0];
        $extraChannelData['instanceToken'] = $generatedData[0];
        CentralChannel::create($extraChannelData);

        $user = $tenant->run(function() use(&$centralUser,$channel,$addonsArr,$input){
            UserChannels::create($channel);
            $userObj = User::create([
                'id' => $centralUser->id,
                'global_id' => $centralUser->global_id,
                'name' => $input['name'],
                'phone' => $input['phone'],
                'email' => $input['email'],
                'duration_type' => $input['duration_type'],
                'group_id' => 1,
                'status' => $input['status'],
                'domain' => $input['domain'],
                'sort' => 1,
                'channels' => serialize([$channel['id']]),
                'password' => Hash::make($input['password']),
                'is_active' => $input['status'],
                'is_approved' => $input['status'],
                'notifications' => isset($input['notifications']) && !empty($input['notifications']) && $input['notifications'] == 'on' ? 1:0,
                'offers' => isset($input['offers']) && !empty($input['offers']) && $input['offers'] == 'on' ? 1 : 0,
                'company' => $input['company'],
                'pin_code' => $input['pin_code'],
                'emergency_number' => $input['emergency_number'],
                'two_auth' => $input['two_auth'],
                'membership_id' => $input['membership_id'],
                'addons' => !empty($addonsArr) ?  serialize($addonsArr) : null,
            ]);

            $paymentInfoObj = new PaymentInfo;
            $paymentInfoObj->user_id = $userObj->id;
            $paymentInfoObj->address = $input['address'];
            $paymentInfoObj->address2 = $input['address2'];
            $paymentInfoObj->city = $input['city'];
            $paymentInfoObj->country = $input['country'];
            $paymentInfoObj->region = $input['region'];
            $paymentInfoObj->postal_code = $input['postal_code'];
            $paymentInfoObj->tax_id = $input['tax_id'];
            $paymentInfoObj->payment_method = $input['payment_method'];
            $paymentInfoObj->currency = $input['currency'];
            $paymentInfoObj->created_at = DATE_TIME;
            $paymentInfoObj->created_by = USER_ID;
            $paymentInfoObj->save();

            return $userObj;
        });

        try {
          dispatch(new TransferDays($channelObj->id,$channelObj->token,$channel['id'],1))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }
        
        // $transferDaysData = [
        //     'receiver' => $channel['id'],
        //     'days' => 3,
        //     'source' => $channelObj->id,
        // ];

        // $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        // $result = $updateResult->json();

        Session::forget('photos');
        CentralWebActions::newType(1,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = CentralUser::getOne($id);
        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/'.$this->getData()['mainData']['name'].'/'.$id);
        CentralWebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            if($col == 'email'){
                $userObj = CentralUser::checkUserBy('email',$item[2],$item[0]);
                if($userObj){
                    return \TraitsFunc::ErrorMessage(trans('main.emailFound',['email'=>$item[2]]));
                }
            }

            if($col == 'phone'){
                $userObj = CentralUser::checkUserBy('phone',$item[2],$item[0]);
                if($userObj){
                    return \TraitsFunc::ErrorMessage(trans('main.phoneFound',['phone'=>$item[2]]));
                }
            }

            $dataObj = CentralUser::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        CentralWebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = CentralUser::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Central.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            CentralUser::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
        }
        return \TraitsFunc::SuccessResponse(trans('main.sortSuccess'));
    }

    public function charts() {
        $input = \Request::all();
        $now = date('Y-m-d');
        $start = $now;
        $end = $now;
        $date = null;
        if(isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])){
            $start = $input['from'].' 00:00:00';
            $end = $input['to'].' 23:59:59';
            $date = 1;
        }

        $addCount = CentralWebActions::getByDate($date,$start,$end,1,$this->getData()['mainData']['modelName'])['count'];
        $editCount = CentralWebActions::getByDate($date,$start,$end,2,$this->getData()['mainData']['modelName'])['count'];
        $deleteCount = CentralWebActions::getByDate($date,$start,$end,3,$this->getData()['mainData']['modelName'])['count'];
        $fastEditCount = CentralWebActions::getByDate($date,$start,$end,4,$this->getData()['mainData']['modelName'])['count'];

        $data['chartData1'] = $this->getChartData($start,$end,1,$this->getData()['mainData']['modelName']);
        $data['chartData2'] = $this->getChartData($start,$end,2,$this->getData()['mainData']['modelName']);
        $data['chartData3'] = $this->getChartData($start,$end,4,$this->getData()['mainData']['modelName']);
        $data['chartData4'] = $this->getChartData($start,$end,3,$this->getData()['mainData']['modelName']);
        $data['counts'] = [$addCount , $editCount , $deleteCount , $fastEditCount];
        $data['designElems'] = $this->getData()['mainData'];

        return view('Central.User.Views.charts')->with('data',(object) $data);
    }

    public function getChartData($start=null,$end=null,$type,$moduleName){
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

        for($i=0;$i<$dataCount;$i++){
            if($dataCount == 1){
                $count = CentralWebActions::where('type',$type)->where('module_name',$moduleName)->where('created_at','>=',$datesArray[0].' 00:00:00')->where('created_at','<=',$datesArray[0].' 23:59:59')->count();
            }else{
                if($i < count($datesArray)){
                    $count = CentralWebActions::where('type',$type)->where('module_name',$moduleName)->where('created_at','>=',$datesArray[$i].' 00:00:00')->where('created_at','<=',$datesArray[$i].' 23:59:59')->count();
                }
            }
            $chartData[0][$i] = $datesArray[$i];
            $chartData[1][$i] = $count;
        }
        return $chartData;
    }

    public function uploadImage(Request $request,$id=false){
        $rand = rand() . date("YmdhisA");
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            Storage::put($rand,$files);
            Session::put('photos',$rand);
            return \TraitsFunc::SuccessResponse('');
        }
    }

    public function addImage($images,$nextID=false){
        $fileName = \ImagesHelper::UploadFile('central_'.$this->getData()['mainData']['name'], $images, $nextID);
        if($fileName == false){
            return false;
        }
        return $fileName;        
    }

    public function deleteImage($id){
        $id = (int) $id;
        $input = \Request::all();

        $menuObj = CentralUser::find($id);
        if($menuObj == null) {
            return \TraitsFunc::ErrorMessage(trans('main.userNotFound'));
        }

        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/central_'.$this->getData()['mainData']['name'].'/'.$id.'/'.$menuObj->image);
        $menuObj->image = '';
        $menuObj->save();
        return \TraitsFunc::SuccessResponse(trans('main.imgDeleted'));
    }

}
