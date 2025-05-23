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

class GroupMsgsControllers extends Controller {

    use \TraitsFunc;

    public function checkPerm(){
        $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        if(in_array(3,$disabled)){
            $dis = 1;
        }
        return $dis;
    }

    public function getData(){
        $groups = GroupNumber::dataList(1)['data'];
        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $messageTypes = [
            ['id'=>1,'title'=>trans('main.text')],
            ['id'=>2,'title'=>trans('main.photoOrFile')],
            ['id'=>4,'title'=>trans('main.sound')],
            ['id'=>5,'title'=>trans('main.link')],
            ['id'=>6,'title'=>trans('main.whatsappNos')],
        ];

        $sent_types = [
            ['id'=>1,'title'=>trans('main.sent')],
            ['id'=>2,'title'=>trans('main.received')],
            ['id'=>3,'title'=>trans('main.seen')],
        ];

        $data['mainData'] = [
            'title' => trans('main.groupMsgs'),
            'url' => 'groupMsgs',
            'name' => 'groupMessages',
            'nameOne' => 'group-message',
            'modelName' => 'GroupMsg',
            'icon' => 'mdi mdi-send',
            'sortName' => 'message',
            'addOne' => trans('main.newGroupMessage'),
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
            'group_id' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $groups,
                'label' => trans('main.group'),
            ],
            'message_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $messageTypes,
                'label' => trans('main.message_type'),
            ],
            'sent_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $sent_types,
                'label' => trans('main.sent_type'),
            ],
            'message' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '3',
                'label' => trans('main.message_content'),
            ],
            'from' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '7',
                'id' => 'datepicker1',
                'label' => trans('main.dateFrom'),
            ],
            'to' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '8',
                'id' => 'datepicker2',
                'label' => trans('main.dateTo'),
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
            'group' => [
                'label' => trans('main.group'),
                'type' => '',
                'className' => '',
                'data-col' => 'group_id',
                'anchor-class' => '',
            ],
            'message_type_text' => [
                'label' => trans('main.message_type'),
                'type' => '',
                'className' => '',
                'data-col' => 'message_type',
                'anchor-class' => '',
            ],
            'message' => [
                'label' => trans('main.message_content'),
                'type' => '',
                'className' => '',
                'data-col' => 'message',
                'anchor-class' => '',
            ],
            'sent_type' => [
                'label' => trans('main.sent_type'),
                'type' => '',
                'className' => '',
                'data-col' => 'sent_type',
                'anchor-class' => '',
            ],
            'contacts_count' => [
                'label' => trans('main.contacts_count'),
                'type' => '',
                'className' => '',
                'data-col' => 'contacts_count',
                'anchor-class' => '',
            ],
            'sent_msgs' => [
                'label' => trans('main.sent_msgs'),
                'type' => '',
                'className' => '',
                'data-col' => 'sent_msgs',
                'anchor-class' => '',
            ],
            'unsent_msgs' => [
                'label' => trans('main.unsent_msgs'),
                'type' => '',
                'className' => '',
                'data-col' => 'unsent_msgs',
                'anchor-class' => '',
            ],
            'viewed_msgs' => [
                'label' => trans('main.viewed_msgs'),
                'type' => '',
                'className' => '',
                'data-col' => 'viewed_msgs',
                'anchor-class' => '',
            ],
            'publish_at' => [
                'label' => trans('main.sentDate'),
                'type' => '',
                'className' => '',
                'data-col' => 'publish_at',
                'anchor-class' => '',
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
            'group_id' => 'required',
            'message_type' => 'required',
        ];

        $message = [
            'group_id.required' => trans('main.groupValidate'),
            'message_type.required' => trans('main.messageTypeValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);
        return $validate;
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

        if($this->checkPerm()){
            Session::flash('error','Please Re-activate Group Messages Addon');
            return redirect()->back();
        }

        $startDay = strtotime(date('Y-m-d 00:00:00'));
        $endDay = strtotime(date('Y-m-d 23:59:59'));
        $messagesCount = ChatMessage::where('fromMe',1)->whereNotIn('status',[null,'APP'])->where('time','>=',$startDay)->where('time','<=',$endDay)->count();
        $dailyCount = Session::get('dailyMessageCount');
        $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,1);
        if($dailyCount + $extraQuotas < $messagesCount){
            Session::flash('error', trans('main.messageQuotaError'));
            return redirect()->back()->withInput();
        }

        Session::forget('msgFile');
        $checkAvailBot = UserAddon::checkUserAvailability(USER_ID,1);
        $checkAvailBotPlus = UserAddon::checkUserAvailability(USER_ID,10);

        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.groupMsgs') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['groups'] = GroupNumber::dataList(1,[1])['data'];
        $data['contacts'] = Contact::dataList(1)['data'];
        $data['bots'] = $checkAvailBot ? Bot::dataList(1)['data'] : [];
        $data['botPlus'] = $checkAvailBotPlus ? BotPlus::dataList(1)['data'] : [];
        // $data['botPlus'] = $dataObj->type > 1 ? BotPlus::getData(BotPlus::find($dataObj->type)) : [];        
        $data['checkAvailBotPlus'] = $checkAvailBotPlus != null ? 1 : 0;        
        $data['checkAvailBot'] = $checkAvailBot != null ? 1 : 0;
        return view('Tenancy.GroupMsgs.Views.V5.add')->with('data', (object) $data);
    }

    protected function validateInsertBotPlusObject($input){
        $rules = [
            'title' => 'required',
            'body' => 'required',
            'footer' => 'required',
            'buttons' => 'required',
        ];

        $message = [
            'title.required' => trans('main.titleValidate'),
            'body.required' => trans('main.bodyValidate'),
            'footer.required' => trans('main.footerValidate'),
            'buttons.required' => trans('main.buttonsValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);
        return $validate;
    }

    public function create() {
        $input = \Request::all();
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }

        $myData = [];
        if($input['message_type'] == 1){
            if(!isset($input['messageText']) || empty($input['messageText'])){
                Session::flash('error', trans('main.messageValidate'));
                return redirect()->back()->withInput();
            }
        }elseif($input['message_type'] == 6){
            $validate = $this->validateInsertBotPlusObject($input);
            if($validate->fails()){
                Session::flash('error', $validate->messages()->first());
                return redirect()->back()->withInput();
            }

            for ($i = 0; $i < $input['buttons']; $i++) {
                if(!isset($input['btn_text_'.($i+1)]) || empty($input['btn_text_'.($i+1)]) || $input['btn_text_'.($i+1)] == null ){
                    Session::flash('error', trans('main.invalidText',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                if(!isset($input['btn_reply_type_'.($i+1)]) || empty($input['btn_reply_type_'.($i+1)]) || $input['btn_reply_type_'.($i+1)] == null ){
                    Session::flash('error', trans('main.invalidType',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                $replyType = (int)$input['btn_reply_type_'.($i+1)];
                if($replyType == 1 && ( !isset($input['btn_reply_'.($i+1)]) || empty($input['btn_reply_'.($i+1)]) )){
                    Session::flash('error', trans('main.invalidReply',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                if($replyType == 2 && ( !isset($input['btn_msg_'.($i+1)]) || empty($input['btn_msg_'.($i+1)]) )){
                    Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                $modelType = '';
                if($replyType == 2 && ( !isset($input['btn_msg_type_'.($i+1)]) || empty($input['btn_msg_type_'.($i+1)]) )){
                    Session::flash('error', trans('main.invalidMsg',['button'=>($i+1)]));
                    return redirect()->back()->withInput();
                }

                $modelType = (int)$input['btn_msg_type_'.($i+1)];
                $modelName = $modelType != '' ?  ($modelType == 1 ? '\App\Models\Bot' : '\App\Models\BotPlus')  : '';
                $msg = $replyType == 1 ? $input['btn_reply_'.($i+1)] : '';

                if($modelName != '' && $msg == ''){
                    $dataObj = $modelName::find($input['btn_msg_'.($i+1)]);
                    if($dataObj){
                        $msg = $dataObj->id;
                    }
                }

                $myData[] = [
                    'id' => $i + 1,
                    'text' => $input['btn_text_'.($i+1)],
                    'reply_type' => $input['btn_reply_type_'.($i+1)],
                    'msg_type' => $modelType,
                    'model_name' => $modelName,
                    'msg' => $msg,
                ];
            }
        }

        if($input['group_id'] == '@'){
            $groupObj = new GroupNumber;
            $groupObj->channel = Session::get('channelCode');
            $groupObj->name_ar = $input['name_ar'];
            $groupObj->name_en = isset($input['name_en']) && !empty($input['name_en']) ? $input['name_en'] : $input['name_ar'];
            $groupObj->description_ar = '';
            $groupObj->description_en = '';
            $groupObj->sort = GroupNumber::newSortIndex();
            $groupObj->status = 1;
            $groupObj->created_at = DATE_TIME;
            $groupObj->created_by = USER_ID;
            $groupObj->save();

            if(!isset($input['whatsappNos']) || empty($input['whatsappNos'])){
                Session::flash('error', trans('main.whatsappNosValidate'));
                return redirect()->back()->withInput();
            }   
            $input['whatsappNos'] = trim($input['whatsappNos']);
            $numbersArr = explode(PHP_EOL, $input['whatsappNos']);
            if(count($numbersArr) > 100){
                Session::flash('error', trans('main.numberlimit',['number'=>100]));
                return redirect()->back()->withInput();
            } 
            $myPhones = [];
            for ($i = 0; $i < count($numbersArr) ; $i++) {
                $phone = str_replace('\r', '', $numbersArr[$i]);
                $myPhones[] = str_replace('+', '', $phone);
                $contactObj = Contact::NotDeleted()->where('group_id',$groupObj->id)->where('phone',$phone)->first();
                if(!$contactObj){
                    $dataObj = new Contact;
                    $dataObj->phone = trim($phone);
                    $dataObj->group_id = $groupObj->id;
                    $dataObj->name = trim($phone);
                    $dataObj->status = $input['status'];
                    $dataObj->sort = Contact::newSortIndex();
                    $dataObj->created_by = USER_ID;
                    $dataObj->created_at = DATE_TIME;
                    $dataObj->save();
                }else{
                    $foundData[] = $phone;
                }
            }
        }else{
            $groupObj = GroupNumber::getOne($input['group_id']);
            if($groupObj == null){
                return redirect('404');
            }
        }
        

        $date = now();
        $flag = 0;
        if(isset($input['date']) && !empty($input['date'])){
            $date = $input['date'];
            $flag = 1;
        }

        $message = '';
        $checkMessage = '';
        if($input['message_type'] == 1){
            $message = $input['messageText'];
            $checkMessage = $input['messageText'];
        }else if($input['message_type'] == 2){
            $message = $input['message']; 
            $checkMessage = $input['message'];
        }else if($input['message_type'] == 4){
            $checkMessage = $input['url_desc'];
        }else if($input['message_type'] == 5){
            $checkMessage = $input['whatsapp_no'];
        }

        $contactsCount = Contact::NotDeleted()->where('group_id',$groupObj->id)->count();
        $messagesArr = 1;

        $startDay = strtotime(date('Y-m-d 00:00:00'));
        $endDay = strtotime(date('Y-m-d 23:59:59'));
        $messagesCount = ChatMessage::where('fromMe',1)->whereNotIn('status',[null,'APP'])->where('time','>=',$startDay)->where('time','<=',$endDay)->count();
        $dailyCount = Session::get('dailyMessageCount');
        $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,1);
        if($dailyCount + $extraQuotas < $messagesCount + $contactsCount ){
            Session::flash('error', trans('main.messageQuotaError'));
            return redirect()->back()->withInput();
        }


        $dataObj = new GroupMsg;
        $dataObj->channel = $groupObj->channel;
        $dataObj->group_id = $groupObj->id;
        $dataObj->message_type = $input['message_type'];
        $dataObj->message = $message;
        $dataObj->publish_at = $date;
        $dataObj->later = $flag;
        $dataObj->contacts_count = $contactsCount;
        $dataObj->messages_count = 1;
        $dataObj->sort = GroupMsg::newSortIndex();
        $dataObj->status = $input['status'];
        if($input['message_type'] == 5){
            $dataObj->whatsapp_no = $input['whatsapp_no'];
        }
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        if($input['message_type'] == 4){
            $dataObj->https_url = $input['https_url'];
            $dataObj->url_title = $input['url_title'];
            $dataObj->url_desc = $input['url_desc'];
            $file = Session::get('msgFile');
            if($file){
                $storageFile = Storage::files($file);
                if(count($storageFile) > 0){
                    $images = self::addImage($storageFile[0],$dataObj->id);
                    if ($images == false) {
                        Session::flash('error', trans('main.uploadProb'));
                        return redirect()->back()->withInput();
                    }
                    $dataObj->url_image = $images;
                    $dataObj->save();
                }
            }
        }

        if(in_array($input['message_type'], [2,3])){
            $file = Session::get('msgFile');
            if($file){
                $storageFile = Storage::files($file);
                if(count($storageFile) > 0){
                    $images = self::addImage($storageFile[0],$dataObj->id);
                    if ($images == false) {
                        Session::flash('error', trans('main.uploadProb'));
                        return redirect()->back()->withInput();
                    }
                    $dataObj->file_name = $images;
                    $dataObj->save();
                }
            }
        }

        if($input['message_type'] == 6){
            $botObj = new BotPlus;
            $botObj->channel = Session::get('channelCode');
            $botObj->message_type = 1;
            $botObj->message = 'Group Message '.$dataObj->id;
            $botObj->title = $input['title'];
            $botObj->body = $input['body'];
            $botObj->footer = $input['footer'];
            $botObj->buttons = $input['buttons'];
            $botObj->buttonsData = serialize($myData);
            $botObj->sort = BotPlus::newSortIndex();
            $botObj->status = 1;
            $botObj->deleted_by = 1;
            $botObj->deleted_at = DATE_TIME;
            $botObj->save();

            $dataObj->bot_plus_id = $botObj->id;
            $dataObj->save();
        }

        $dataObj = GroupMsg::getData($dataObj);
        $chunks = 100;

        // $contacts = Contact::NotDeleted()->where('group_id',$groupObj->id)->where('status',1)->chunk($chunks,function($data) use ($dataObj){
        //     foreach($data as $key => $oneChunk){
        //         try {
        //             $on = \Carbon\Carbon::now()->addSeconds($key*60);   
        //             dispatch(new GroupMessageJob($oneChunk,$dataObj))->onConnection('cjobs')->delay($on);
        //         } catch (Exception $e) {}
        //     }
        // });


        if($flag == 0){
            // Fire Job Queue
            $contacts = Contact::NotDeleted()->where('group_id',$groupObj->id)->where('status',1)->chunk($chunks,function($data) use ($dataObj){
                try {
                    dispatch(new GroupMessageJob(reset($data),$dataObj))->onConnection('cjobs');
                } catch (Exception $e) {
                    
                }
            });
        }else{


            $now = \Carbon\Carbon::now();
            $sendDate = \Carbon\Carbon::parse($date);
            $diff =  $sendDate->diffInSeconds($now);
            $on = \Carbon\Carbon::now()->addSeconds($diff);   

            $contacts = Contact::NotDeleted()->where('group_id',$groupObj->id)->where('status',1)->chunk($chunks,function($data) use ($dataObj,$on){
                try {
                    dispatch(new GroupMessageJob(reset($data),$dataObj))->onConnection('cjobs')->delay($on);
                } catch (Exception $e) {
                    
                }
            });
            // $contacts = Contact::NotDeleted()->where('group_id',$groupObj->id)->where('status',1)->get();
            // foreach ($contacts as $contact) {
            //     $reportObj = new ContactReport;
            //     $reportObj->contact = $contact->phone;
            //     $reportObj->group_id = $groupObj->id;
            //     $reportObj->group_message_id = $dataObj->id;
            //     $reportObj->status = 0;
            //     $reportObj->save();
            // }
        }

        Session::forget('msgFile');
        WebActions::newType(1,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/view/'.$dataObj->id);
    }

    public function view($id) {
        $id = (int) $id;
        $groupMsgObj = GroupMsg::NotDeleted()->find($id);
        if($groupMsgObj == null) {
            return Redirect('404');
        }

        $phone = str_replace("+", '', $groupMsgObj->Creator->phone);
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $updateResult = $mainWhatsLoopObj->me();
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] == 1 && isset($result['data'])){
            $phone = str_replace('@c.us', '', $result['data']['id']);
        }

        $data = Contact::getFullContactsInfo($groupMsgObj->group_id,$groupMsgObj->id);
        $checkAvailBotPlus = UserAddon::checkUserAvailability(USER_ID,10);

        $data['checkAvailBotPlus'] = $checkAvailBotPlus != null ? 1 : 0;        
        $data['msg'] = GroupMsg::getData($groupMsgObj);        
        $data['phone'] = $phone;
        $data['botPlus'] = $groupMsgObj->bot_plus_id > 1 ? BotPlus::getData(BotPlus::find($groupMsgObj->bot_plus_id)) : [];       
        $data['designElems']['mainData'] = $this->getData()['mainData'];
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.groupMsgs') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-eye';
        return view('Tenancy.GroupMsgs.Views.V5.view')->with('data', (object) $data);
    }

    public function resend($id,$status){
        $id = (int) $id;
        $groupMsgObj = GroupMsg::NotDeleted()->find($id);
        if($groupMsgObj == null) {
            return Redirect('404');
        }

        $dataObj = GroupMsg::getData($groupMsgObj);
        $chunks = 400;
        if($status == 1){
            $contacts = Contact::NotDeleted()->where('group_id',$groupMsgObj->group_id)->where('status',1)->chunk($chunks,function($data) use ($dataObj){
                try {
                    dispatch(new GroupMessageJob(reset($data),$dataObj))->onConnection('cjobs');
                } catch (Exception $e) {
                    
                }
            });
        }else{
            $contacts = Contact::NotDeleted()->where('group_id',$groupMsgObj->group_id)->whereHas('Reports',function($whereHasQuery) use ($id){
                $whereHasQuery->where('group_message_id',$id)->where('status',0);
            })->chunk($chunks,function($data) use ($dataObj){
                try {
                    dispatch(new GroupMessageJob(reset($data),$dataObj))->onConnection('cjobs');
                } catch (Exception $e) {
                    
                }
            });
        }

        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/view/'.$groupMsgObj->id);
    }

    public function refresh($id){
        $id = (int) $id;
        $groupMsgObj = GroupMsg::NotDeleted()->find($id);
        if($groupMsgObj == null) {
            return Redirect('404');
        }

        try {
            dispatch(new FixReport($id))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }

        Session::flash('success', trans('main.inPrgo'));
        return redirect()->back();
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

        $addCount = WebActions::getByDate($date,$start,$end,1,$this->getData()['mainData']['modelName'])['count'];
        $editCount = WebActions::getByDate($date,$start,$end,2,$this->getData()['mainData']['modelName'])['count'];
        $deleteCount = WebActions::getByDate($date,$start,$end,3,$this->getData()['mainData']['modelName'])['count'];
        $fastEditCount = WebActions::getByDate($date,$start,$end,4,$this->getData()['mainData']['modelName'])['count'];

        $data['chartData1'] = $this->getChartData($start,$end,1,$this->getData()['mainData']['modelName']);
        $data['chartData2'] = $this->getChartData($start,$end,2,$this->getData()['mainData']['modelName']);
        $data['chartData3'] = $this->getChartData($start,$end,4,$this->getData()['mainData']['modelName']);
        $data['chartData4'] = $this->getChartData($start,$end,3,$this->getData()['mainData']['modelName']);
        $data['counts'] = [$addCount , $editCount , $deleteCount , $fastEditCount];
        $data['designElems'] = $this->getData()['mainData'];

        return view('Tenancy.User.Views.charts')->with('data',(object) $data);
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
                $count = WebActions::where('type',$type)->where('module_name',$moduleName)->where('created_at','>=',$datesArray[0].' 00:00:00')->where('created_at','<=',$datesArray[0].' 23:59:59')->count();
            }else{
                if($i < count($datesArray)){
                    $count = WebActions::where('type',$type)->where('module_name',$moduleName)->where('created_at','>=',$datesArray[$i].' 00:00:00')->where('created_at','<=',$datesArray[$i].' 23:59:59')->count();
                }
            }
            $chartData[0][$i] = $datesArray[$i];
            $chartData[1][$i] = $count;
        }
        return $chartData;
    }

    public function uploadImage($type,Request $request){
        $rand = rand() . date("YmdhisA");
        $typeID = (int) $type;
        if(!in_array($typeID, [2,3,4,5])){
            return Redirect('404');
        }
        if ($request->hasFile('file')) {
            $files = $request->file('file');

            $file_size = $files->getSize();
            $file_size = $file_size/(1024 * 1024);
            $file_size = number_format($file_size,2);
            $uploadedSize = \Helper::getFolderSize(public_path().'/uploads/'.TENANT_ID.'/');
            $totalStorage = Session::get('storageSize');
            $extraQuotas = UserExtraQuota::getOneForUserByType(GLOBAL_ID,3);
            if($totalStorage + $extraQuotas < (doubleval($uploadedSize) + $file_size) / 1024){
                return \TraitsFunc::ErrorMessage(trans('main.storageQuotaError'));
            }

            
            $type = \ImagesHelper::checkFileExtension($files->getClientOriginalName());
            $fileSize = $files->getSize();
            if($fileSize >= 15000000){
                return \TraitsFunc::ErrorMessage(trans('main.file100kb'));
            }
            
            if( $typeID == 2 && !in_array($type, ['file','photo','video']) ){
                return \TraitsFunc::ErrorMessage(trans('main.selectFile'));
            }

            if( $typeID == 3 && $type != 'sound' ){
                return \TraitsFunc::ErrorMessage(trans('main.selectSound'));
            }

            if( $typeID == 4 && $type != 'photo' ){
                return \TraitsFunc::ErrorMessage(trans('main.urlImage'));
            }

            Storage::put($rand,$files);
            Session::put('msgFile',$rand);
            return \TraitsFunc::SuccessResponse('');
        }
    }

    public function addImage($images,$nextID=false){
        $fileName = \ImagesHelper::UploadFile($this->getData()['mainData']['name'], $images, $nextID);
        if($fileName == false){
            return false;
        }
        return $fileName;        
    }

}
