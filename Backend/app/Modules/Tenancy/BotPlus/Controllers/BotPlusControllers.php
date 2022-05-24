<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Category;
use App\Models\BotPlus;
use App\Models\Bot;
use App\Models\UserExtraQuota;
use App\Models\Template;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use App\Models\UserAddon;
use DataTables;
use Storage;
use Redirect;

class BotPlusControllers extends Controller {

    use \TraitsFunc;
    public $addonId = '10';

    public function getData(){
        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $messageTypes=[
            [
                'id'=> '1',
                'title' => trans('main.equal'),
            ],
            [
                'id'=> '2',
                'title' => trans('main.part'),
            ],
        ];

        $data['mainData'] = [
            'title' => trans('main.botPlus'),
            'url' => 'botPlus',
            'name' => 'bots-plus',
            'nameOne' => 'bot-plus',
            'modelName' => 'BotPlus',
            'icon' => 'fas fa-robot',
            'sortName' => 'message',
            'addOne' => trans('main.newBot'),
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
            'message_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $messageTypes,
                'label' => trans('main.messageType'),
            ],
            'message' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '3',
                'label' => trans('main.clientMessage'),
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
            'message_type_text' => [
                'label' => trans('main.messageType'),
                'type' => '',
                'className' => '',
                'data-col' => 'message_type',
                'anchor-class' => '',
            ],
            'message' => [
                'label' => trans('main.clientMessage'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'message',
                'anchor-class' => 'editable',
            ],
            'buttons' => [
                'label' => trans('main.buttons'),
                'type' => '',
                'className' => '',
                'data-col' => 'buttons',
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
            'message_type' => 'required',
            'message' => 'required',
            'title' => 'required',
            'body' => 'required',
            'footer' => 'required',
            'buttons' => 'required',
        ];

        $message = [
            'message_type.required' => trans('main.messageTypeValidate'),
            'message.required' => trans('main.messageValidate'),
            'title.required' => trans('main.titleValidate'),
            'body.required' => trans('main.bodyValidate'),
            'footer.required' => trans('main.footerValidate'),
            'buttons.required' => trans('main.buttonsValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);
        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = BotPlus::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;
        $checkAvail = UserAddon::checkUserAvailability(USER_ID,$this->addonId);
        $dataObj = BotPlus::find($id);
        if($dataObj == null || !$checkAvail) {
            return Redirect('404');
        }

        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $data['data'] = BotPlus::getData($dataObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.botPlus') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['channels'] = $channels;
        $checkAsvail = UserAddon::checkUserAvailability(USER_ID,1);
        $data['bots'] = $checkAsvail ? Bot::dataList(1)['data'] : [];
        $data['botPlus'] = BotPlus::dataList(1)['data'];
        $data['templates'] = Template::dataList(1)['data'];
        $data['mods'] = User::getModerators()['data'];
        $data['labels'] = Category::dataList()['data'];
        return view('Tenancy.BotPlus.Views.edit')->with('data', (object) $data);      
    }

    public function copy($id) {
        $id = (int) $id;

        $dataObj = BotPlus::find($id);
        $checkAvail = UserAddon::checkUserAvailability(USER_ID,$this->addonId);
        if($dataObj == null || !$checkAvail) {
            return Redirect('404');
        }

        $newDataObj = $dataObj->replicate();
        $newDataObj->save();
        return Redirect::to('/botPlus/edit/'.$newDataObj->id);      
    }

    public function changeStatus($id) {
        $id = (int) $id;

        $dataObj = BotPlus::find($id);
        $checkAvail = UserAddon::checkUserAvailability(USER_ID,$this->addonId);
        if($dataObj == null || !$checkAvail) {
            return Redirect('404');
        }


        $dataObj->status = $dataObj->status == 1 ? 0 : 1;
        $dataObj->save();

        Session::flash('success', trans('main.editSuccess'));
        return Redirect::to('/botPlus/');      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        $botObj = BotPlus::find($id);
        if($botObj == null) {
            return Redirect('404');
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $myData = [];
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
        $botObj->message_type = $input['message_type'];
        $botObj->message = $input['message'];
        $botObj->title = $input['title'];
        $botObj->body = $input['body'];
        $botObj->footer = $input['footer'];
        $botObj->buttons = $input['buttons'];
        $botObj->buttonsData = serialize($myData);
        $botObj->status = $input['status'];
        $botObj->category_id = $input['category_id'];
        $botObj->moderator_id = $input['moderator_id'];
        $botObj->updated_at = DATE_TIME;
        $botObj->updated_by = USER_ID;
        $botObj->save();

        WebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $checkAvail = UserAddon::checkUserAvailability(USER_ID,$this->addonId);
        if(!$checkAvail){
            return redirect(404);
        }
        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.botPlus') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['channels'] = $channels;
        $checkAsvail = UserAddon::checkUserAvailability(USER_ID,1);
        $data['bots'] = $checkAsvail ? Bot::dataList(1)['data'] : [];
        $data['botPlus'] = BotPlus::dataList(1)['data'];
        $data['templates'] = Template::dataList(1)['data'];
        $data['mods'] = User::getModerators()['data'];
        $data['labels'] = Category::dataList()['data'];
        return view('Tenancy.BotPlus.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }

        //btn_text_1,btn_reply_type_1,btn_reply_1,btn_msg_1
        //invalidText,invalidType,invalidReply,invalidMsg
        $myData = [];
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
        
        $dataObj = new BotPlus;
        $dataObj->channel = Session::get('channelCode');
        $dataObj->message_type = $input['message_type'];
        $dataObj->message = $input['message'];
        $dataObj->title = $input['title'];
        $dataObj->body = $input['body'];
        $dataObj->footer = $input['footer'];
        $dataObj->buttons = $input['buttons'];
        $dataObj->buttonsData = serialize($myData);
        $dataObj->category_id = $input['category_id'];
        $dataObj->moderator_id = $input['moderator_id'];
        $dataObj->sort = BotPlus::newSortIndex();
        $dataObj->status = $input['status'];
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        WebActions::newType(1,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $checkAvail = UserAddon::checkUserAvailability(USER_ID,$this->addonId);
        if(!$checkAvail){
            return \TraitsFunc::SuccessResponse(trans('main.unAvail'));
        }

        $dataObj = BotPlus::getOne($id);
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        \ImagesHelper::deleteDirectory(public_path('/').'uploads/'.TENANT_ID.'/'.$this->getData()['mainData']['name'].'/'.$id);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        $checkAvail = UserAddon::checkUserAvailability(USER_ID,$this->addonId);
        if(!$checkAvail){
            return \TraitsFunc::SuccessResponse(trans('main.unAvail'));
        }
        
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = BotPlus::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = BotPlus::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            BotPlus::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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
}
