<?php namespace App\Http\Controllers;

use App\Models\GroupNumber;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use App\Models\UserExtraQuota;
use App\Models\Variable;
use App\Jobs\CheckWhatsappJob;
use App\Exports\ContactImport;
use DataTables;
use Storage;
use Excel;


class GroupNumbersControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;
        
        $data['mainData'] = [
            'title' => trans('main.groupNumbers'),
            'url' => 'groupNumbers',
            'name' => 'groupNumbers',
            'nameOne' => 'group-number',
            'modelName' => 'GroupNumber',
            'icon' => 'fas fa-users',
            'sortName' => 'name_'.LANGUAGE_PREF,
            'addOne' => trans('main.newGroupNumber'),
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
            ],
            'name_ar' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.titleAr'),
            ],
            'name_en' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '2',
                'label' => trans('main.titleEn'),
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
            'name_ar' => [
                'label' => trans('main.titleAr'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'name_ar',
                'anchor-class' => 'editable',
            ],
            'name_en' => [
                'label' => trans('main.titleEn'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'name_en',
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

        $data['modelData'] = [
            'name_ar' => [
                'type' => 'text',
                'class' => 'form-control',
                'label' => trans('main.titleAr'),
                'specialAttr' => '',
            ],
            'name_en' => [
                'type' => 'text',
                'class' => 'form-control',
                'label' => trans('main.titleEn'),
                'specialAttr' => '',
            ],
            'description_ar' => [
                'type' => 'textarea',
                'class' => 'form-control',
                'label' => trans('main.descriptionAr'),
                'specialAttr' => '',
            ],
            'description_en' => [
                'type' => 'textarea',
                'class' => 'form-control',
                'label' => trans('main.descriptionEn'),
                'specialAttr' => '',
            ],

        ];
        return $data;
    }

    protected function validateInsertObject($input){
        $rules = [
            'name_ar' => 'required',
            'name_en' => 'required',
        ];

        $message = [
            'name_ar.required' => trans('main.titleArValidate'),
            'name_en.required' => trans('main.titleEnValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = GroupNumber::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = GroupNumber::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = GroupNumber::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.groupNumbers') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        return view('Tenancy.User.Views.edit')->with('data', (object) $data);
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        // dd($input);
        $dataObj = GroupNumber::NotDeleted()->find($id);
        if($dataObj == null || $id == 1) {
            return Redirect('404');
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $dataObj->channel = Session::get('channelCode');
        $dataObj->name_ar = $input['name_ar'];
        $dataObj->name_en = $input['name_en'];
        $dataObj->description_ar = $input['description_ar'];
        $dataObj->description_en = $input['description_en'];
        $dataObj->updated_at = DATE_TIME;
        $dataObj->updated_by = USER_ID;
        $dataObj->save();

        WebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.groupNumbers') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        return view('Tenancy.User.Views.add')->with('data', (object) $data);
    }

    public function create(Request $request) {
        $input = \Request::all();

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            if($request->ajax()){
                return \TraitsFunc::ErrorMessage($validate->messages()->first());
            }
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }



        $dataObj = new GroupNumber;
        $dataObj->channel = Session::get('channelCode');
        $dataObj->name_ar = $input['name_ar'];
        $dataObj->name_en = $input['name_en'];
        if($request->ajax()){
            $input['status'] = 1;
            $input['description_ar'] = '';
            $input['description_en'] = '';
        }

        $dataObj->description_ar = $input['description_ar'];
        $dataObj->description_en = $input['description_en'];
        $dataObj->sort = GroupNumber::newSortIndex();
        $dataObj->status = $input['status'];
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        WebActions::newType(1,$this->getData()['mainData']['modelName']);
        if($request->ajax()){
            return \Response::json((object) GroupNumber::getData($dataObj));
        }
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = GroupNumber::getOne($id);
        if($dataObj == null || $id == 1) {
            return \TraitsFunc::ErrorMessage(trans('main.notDeleted'));
        }
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = GroupNumber::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = GroupNumber::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            GroupNumber::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
        }
        return \TraitsFunc::SuccessResponse(trans('main.sortSuccess'));
    }

    public function addGroupNumbers(){
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.addGroupNumbers') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['groups'] = GroupNumber::dataList(1,[1])['data'];
        $data['channels'] =[];
        $data['channels'][0] = Session::get('channelCode');
        $data['modelProps'] = ['name'=>trans('main.name'),'email'=>trans('main.email'),'country'=>trans('main.country'),'city'=>trans('main.city'),'phone'=>trans('main.whats')];
        return view('Tenancy.GroupNumbers.Views.V5.add')->with('data', (object) $data);
    }

    public function checkFile(Request $request){
        if ($request->hasFile('file')) {
            $rows = Excel::toArray(new ContactImport, $request->file('file'));
            $headers = $rows[0][0];
            $data = array_slice($rows[0], 1, 10);
            $varObj = Variable::where('var_key','grpFile')->first();
            if($varObj){
                $varObj->delete();
            }
            Variable::create(['var_value' => json_encode($rows),'var_key'=>'grpFile']);
            
            return response()->json(["headers"=>$headers,'data'=>$data,'files'=>json_encode([])]);
        }
    }

    public function postAddGroupNumbers(){
        $input = \Request::all();
        if(!isset($input['group_id']) && empty($input['group_id'])){
            Session::flash('error', trans('main.groupValidate'));
            return redirect()->back();
        }

        if($input['group_id'] == '@'){
            $dataObj = new GroupNumber;
            $dataObj->channel = Session::get('channelCode');
            $dataObj->name_ar = $input['name_ar'];
            $dataObj->name_en = $input['name_en'];
            $dataObj->description_ar = '';
            $dataObj->description_en = '';
            $dataObj->sort = GroupNumber::newSortIndex();
            $dataObj->status = 1;
            $dataObj->created_at = DATE_TIME;
            $dataObj->created_by = USER_ID;
            $dataObj->save();
            $input['group_id'] = $dataObj->id;
        }
        
        $groupObj = GroupNumber::getOne($input['group_id']);
        // dd($groupObj);
        if($groupObj == null) {
            return Redirect('404');
        }

        if(!isset($input['files']) && empty($input['files'])){
            Session::flash('error', trans('main.pleaseAttachExcel'));
            return redirect()->back();
        }
        
        $varObj = Variable::where('var_key','grpFile')->first();
        $rows = json_decode($varObj->var_value);
        // $rows = json_decode($input['files']);
        $mainData = $rows[0];

        $modelProps = ['name','email','country','city','phone','Phone'];
        $userInputs = $input;
        unset($userInputs['status']);
        unset($userInputs['group_id']);
        unset($userInputs['_token']);
        unset($userInputs['file']);
        unset($userInputs['files']);

        $storeData = [];
        $consForQueue = [];
        $myData = [];
        foreach ($userInputs as $key=> $userInput) {
            if(in_array(strtolower($key), $modelProps)){
                $myData[strtolower($key)] = $userInputs[$key];
            }
            // unset($userInputs[$key]);    
        }
        
        $dateTime = DATE_TIME;
        $rows =  array_slice($rows[0], 1);
        for ($i = 1; $i < count($mainData); $i++) {
            $header = $mainData[0];
            for ($x = 0; $x < count($header); $x++) {
                foreach ($myData as $key=> $userInput) {
                    if(!isset($storeData[$i])){
                        $storeData[$i] = [];
                    }
                    if(!isset($storeData[$i][$key])){
                        $storeData[$i][$key] = '';
                    }
                    if($key == strtolower($header[$x])){
                        $storeData[$i][$key] = $mainData[$i][$x];
                    }
                }
            }
            $storeData[$i]['status'] = $input['status'];
            $storeData[$i]['group_id'] = $input['group_id'];
            $storeData[$i]['created_at'] = $dateTime;
            $storeData[$i]['created_by'] = USER_ID;
            // $storeData[$i]['sort'] = Contact::newSortIndex()+$i;
        }

        $contsArr = [];
        $phones = [];
        foreach ($storeData as $value) {
            if(isset($value['phone']) && $value['phone'] != null){
                $phone = str_replace('+','',$value['phone']);
                $phone = str_replace('\r', '', $phone);
                // $contactObj = Contact::NotDeleted()->where('group_id',$value['group_id'])->where('phone',$phone)->first();
                // if(!$contactObj){
                if(!isset($value['name']) || empty($value['name'])){
                    $value['name'] = $phone;
                }
                // if(isset($value['email']) && !empty($value['email'])){
                //     $value['email'] = $input['email'];
                // }
                // if(isset($value['country']) && !empty($value['country'])){
                //     $value['country'] = $input['country'];
                // }
                // if(isset($value['city']) && !empty($value['city'])){
                //     $value['city'] = $input['city'];
                // }
                $value['phone'] = trim(str_replace('\r', '', $phone));
                $value['status'] = 1;
                $phones[] = $value['phone'];
                $item = [];
                foreach($value as $attr => $val){
                    $item[$attr]= $val;
                }
                $contsArr[] = $item;
                // }
                $consForQueue[] = $item;
            }
        }
        $totals = count(array_unique($phones));
        $varObj = Variable::where('var_key','check_'.$input['group_id'].'_'.$dateTime)->first();
        if(!$varObj){
            Variable::create([
                'var_key' => 'check_'.$input['group_id'].'_'.$dateTime,
                'var_value' => $totals,
            ]);
        }else{
            $varObj->update([
                'var_key' => 'check_'.$input['group_id'].'_'.$dateTime,
                'var_value' => $totals,
            ]);
        }

        try {
            dispatch(new CheckWhatsappJob($consForQueue))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }
        // $chunks = 400;
        // $contacts = array_chunk($consForQueue,$chunks);
        // foreach ($contacts as $contact) {
        //     try {
        //         dispatch(new CheckWhatsappJob($contact));//->onConnection('cjobs');
        //     } catch (Exception $e) {
                
        //     }
        // }

        WebActions::newType(1,'Contact');
        \Session::forget('rows');
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to('/groupNumberReports');
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
