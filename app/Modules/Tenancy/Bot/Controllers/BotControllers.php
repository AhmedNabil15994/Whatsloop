<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bot;
use App\Models\Template;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use DataTables;
use Storage;
use Redirect;

class BotControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value;
            $channelObj->title = $value;
            $channels[] = $channelObj;
        }

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

        $replyTypes = [
            ['id'=>1,'title'=>trans('main.text')],
            ['id'=>2,'title'=>trans('main.photoOrFile')],
            ['id'=>3,'title'=>trans('main.video')],
            ['id'=>4,'title'=>trans('main.sound')],
            ['id'=>5,'title'=>trans('main.link')],
            ['id'=>6,'title'=>trans('main.whatsappNos')],
            ['id'=>7,'title'=>trans('main.mapLocation')],
            ['id'=>8,'title'=>trans('main.webhook')],
        ];

        $data['mainData'] = [
            'title' => trans('main.bot'),
            'url' => 'bots',
            'name' => 'bots',
            'nameOne' => 'bot',
            'modelName' => 'Bot',
            'icon' => 'fas fa-robot',
            'sortName' => 'message',
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
            'reply_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $replyTypes,
                'label' => trans('main.replyType'),
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
            'channel' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => 'edits selects',
                'data-col' => 'channel',
                'anchor-class' => 'editable badge badge-secondary',
            ],
            'message_type_text' => [
                'label' => trans('main.messageType'),
                'type' => '',
                'className' => 'edits selects',
                'data-col' => 'message_type',
                'anchor-class' => 'editable badge badge-secondary',
            ],
            'message' => [
                'label' => trans('main.clientMessage'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'message',
                'anchor-class' => 'editable badge badge-secondary',
            ],
            'reply_type_text' => [
                'label' => trans('main.replyType'),
                'type' => '',
                'className' => 'edits selects',
                'data-col' => 'reply_type',
                'anchor-class' => 'editable badge badge-secondary',
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
            'channel' => 'required',
            'message_type' => 'required',
            'message' => 'required',
            'reply_type' => 'required',
        ];

        $message = [
            'channel.required' => trans('main.channelValidate'),
            'message_type.required' => trans('main.messageTypeValidate'),
            'message.required' => trans('main.messageValidate'),
            'reply_type.required' => trans('main.replyTypeValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);
        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = Bot::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $dataObj = Bot::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value;
            $channelObj->title = $value;
            $channels[] = $channelObj;
        }

        $data['data'] = Bot::getData($dataObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.bot') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['channels'] = $channels;
        $data['bots'] = Bot::dataList(1)['data'];
        $data['templates'] = Template::dataList(1)['data'];
        return view('Tenancy.Bot.Views.edit')->with('data', (object) $data);      
    }

    public function copy($id) {
        $id = (int) $id;

        $dataObj = Bot::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $newDataObj = $dataObj->replicate();
        $newDataObj->save();
        return Redirect::to('/bots/edit/'.$newDataObj->id);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        $dataObj = Bot::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        if($input['reply_type'] == 1){
            if(!isset($input['replyText']) || empty($input['replyText'])){
                Session::flash('error', trans('main.replyValidate'));
                return redirect()->back()->withInput();
            }
        }

        $dataObj->channel = $input['channel'];
        $dataObj->message_type = $input['message_type'];
        $dataObj->message = $input['message'];
        $dataObj->reply_type = $input['reply_type'];
        $dataObj->status = $input['status'];
        $dataObj->updated_by_at = DATE_TIME;
        $dataObj->updated_by_by = USER_ID;
        $dataObj->save();

        if($input['reply_type'] == 1){
            $dataObj->reply = $input['replyText'];
        }else if($input['reply_type'] == 2){
            $dataObj->reply = $input['reply']; 
        }else if($input['reply_type'] == 5){
            $dataObj->https_url = $input['https_url'];
            $dataObj->url_title = $input['url_title'];
            $dataObj->url_desc = $input['url_desc'];
            $file = Session::get('botFile');
            if($file){
                $storageFile = Storage::files($file);
                if(count($storageFile) > 0){
                    $images = self::addImage($storageFile[0],$dataObj->id);
                    if ($images == false) {
                        Session::flash('error', trans('main.uploadProb'));
                        return redirect()->back()->withInput();
                    }
                    $dataObj->url_image = $images;
                }
            }
        }else if($input['reply_type'] == 6){
            $dataObj->whatsapp_no = $input['whatsapp_no'];
        }else if($input['reply_type'] == 7){
            $dataObj->lat = $input['lat'];
            $dataObj->lng = $input['lng'];
            $dataObj->address = $input['address'];
        }else if($input['reply_type'] == 8){
            $dataObj->webhook_url = $input['webhook_url'];
            if(isset($input['templates']) && !empty($input['templates'])){
                $dataObj->templates = serialize($input['templates']);
            }
        }

        if(in_array($input['reply_type'], [2,3,4])){
            $file = Session::get('botFile');
            if($file){
                $storageFile = Storage::files($file);
                if(count($storageFile) > 0){
                    $images = self::addImage($storageFile[0],$dataObj->id);
                    if ($images == false) {
                        Session::flash('error', trans('main.uploadProb'));
                        return redirect()->back()->withInput();
                    }
                    $dataObj->file_name = $images;
                }
            }
        }

        $dataObj->save();

        Session::forget('botFile');
        WebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value;
            $channelObj->title = $value;
            $channels[] = $channelObj;
        }

        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.bot') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $userObj = User::getData(User::getOne(USER_ID));
        $data['channels'] = $channels;
        $data['bots'] = Bot::dataList(1)['data'];
        $data['templates'] = Template::dataList(1)['data'];
        return view('Tenancy.Bot.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }

        if($input['reply_type'] == 1){
            if(!isset($input['replyText']) || empty($input['replyText'])){
                Session::flash('error', trans('main.replyValidate'));
                return redirect()->back()->withInput();
            }
        }

        $dataObj = new Bot;
        $dataObj->channel = $input['channel'];
        $dataObj->message_type = $input['message_type'];
        $dataObj->message = $input['message'];
        $dataObj->reply_type = $input['reply_type'];
        $dataObj->sort = Bot::newSortIndex();
        $dataObj->status = $input['status'];
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        if($input['reply_type'] == 1){
            $dataObj->reply = $input['replyText'];
            $dataObj->save();
        }else if($input['reply_type'] == 2){
            $dataObj->reply = $input['reply']; 
            $dataObj->save();
        }else if($input['reply_type'] == 5){
            $dataObj->https_url = $input['https_url'];
            $dataObj->url_title = $input['url_title'];
            $dataObj->url_desc = $input['url_desc'];
            $file = Session::get('botFile');
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
        }else if($input['reply_type'] == 6){
            $dataObj->whatsapp_no = $input['whatsapp_no'];
            $dataObj->save();
        }else if($input['reply_type'] == 7){
            $dataObj->lat = $input['lat'];
            $dataObj->lng = $input['lng'];
            $dataObj->address = $input['address'];
            $dataObj->save();
        }else if($input['reply_type'] == 8){
            $dataObj->webhook_url = $input['webhook_url'];
            if(isset($input['templates']) && !empty($input['templates'])){
                $dataObj->templates = serialize($input['templates']);
                $dataObj->save();
            }
        }

        if(in_array($input['reply_type'], [2,3,4])){
            $file = Session::get('botFile');
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

        Session::forget('botFile');
        WebActions::newType(1,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = Bot::getOne($id);
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/'.$this->getData()['mainData']['name'].'/'.$id);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = Bot::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = Bot::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            Bot::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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

    public function uploadImage($id=null,$type,Request $request){
        $rand = rand() . date("YmdhisA");
        $typeID = (int) $type;
        if(!in_array($typeID, [2,3,4,5])){
            return Redirect('404');
        }
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            $type = \ImagesHelper::checkFileExtension($files->getClientOriginalName());
            
            if( $typeID == 2 && !in_array($type, ['file','image']) ){
                return \TraitsFunc::ErrorMessage(trans('main.selectFile'));
            }

            if( $typeID == 3 && $type != 'video' ){
                return \TraitsFunc::ErrorMessage(trans('main.selectVideo'));
            }

            if( $typeID == 4 && $type != 'sound' ){
                return \TraitsFunc::ErrorMessage(trans('main.selectSound'));
            }

            if( $typeID == 5 && $type != 'image' ){
                return \TraitsFunc::ErrorMessage(trans('main.urlImage'));
            }

            Storage::put($rand,$files);
            Session::put('botFile',$rand);
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

    public function deleteImage($id){
        $id = (int) $id;
        $input = \Request::all();
        $file = $input['type'];
        $menuObj = Bot::find($id);
        if($menuObj == null) {
            return \TraitsFunc::ErrorMessage(trans('main.botNotFound'));
        }

        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/'.$this->getData()['mainData']['name'].'/'.$id.'/'.$menuObj->$file);
        $menuObj->$file = '';
        $menuObj->save();
        return \TraitsFunc::SuccessResponse(trans('main.imgDeleted'));
    }
}
