<?php namespace App\Http\Controllers;

use App\Models\GroupNumber;
use App\Models\User;
use App\Models\Contact;
use App\Models\ChatDialog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use App\Jobs\CheckWhatsappJob;
use DataTables;
use Storage;
use Excel;
use App\Exports\ContactExport;

class ContactsControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $userObj = User::getData(User::getOne(USER_ID));
        $data['userObj'] = $userObj;
        $groups = GroupNumber::dataList(1)['data'];
        $data['mainData'] = [
            'title' => trans('main.contacts'),
            'url' => 'contacts',
            'name' => 'contacts',
            'nameOne' => 'contact',
            'modelName' => 'Contact',
            'icon' => 'fas fa-users',
            'sortName' => 'name',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
            ],
            'group_id' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $groups,
                'label' => trans('main.group'),
            ],
            'name' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '2',
                'label' => trans('main.name'),
            ],
            'email' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '3',
                'label' => trans('main.email'),
            ],
            'city' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '4',
                'label' => trans('main.city'),
            ],
            'country' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '5',
                'label' => trans('main.country'),
            ],
            'whats' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '6',
                'label' => trans('main.whats'),
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
                'className' => 'edits selects',
                'data-col' => 'group_id',
                'anchor-class' => 'editable badge badge-primary',
            ],
            'name' => [
                'label' => trans('main.name'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'name',
                'anchor-class' => 'editable',
            ],
            'email' => [
                'label' => trans('main.email'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'email',
                'anchor-class' => 'editable',
            ],
            'country' => [
                'label' => trans('main.country'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'country',
                'anchor-class' => 'editable',
            ],
            'city' => [
                'label' => trans('main.city'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'city',
                'anchor-class' => 'editable',
            ],
            'phone2' => [
                'label' => trans('main.whats'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'phone',
                'anchor-class' => 'editable',
            ],
            'created_at' => [
                'label' => trans('main.date'),
                'type' => 'date',
                'className' => 'edits dates',
                'data-col' => 'created_at',
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
        $rules = ['group_id' => 'required',];
        $message = ['group_id.required' => trans('main.groupValidate'),];
        $validate = \Validator::make($input, $rules, $message);
        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = Contact::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        $data['data'] = Contact::dataList()['data'];
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $contactObj = Contact::NotDeleted()->find($id);
        if($contactObj == null) {
            return Redirect('404');
        }

        $data['data'] = Contact::getData($contactObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.contacts') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['groups'] = GroupNumber::dataList(1,[1])['data'];
        $data['timelines'] = WebActions::getByModule($data['designElems']['mainData']['modelName'],10)['data'];
        return view('Tenancy.Contact.Views.edit')->with('data', (object) $data);
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        // dd($input);
        $dataObj = Contact::NotDeleted()->find($id);
        if($dataObj == null || $id == 1) {
            return Redirect('404');
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        if(!isset($input['phone']) || empty($input['phone'])){
            Session::flash('error', trans('main.phoneValidate'));
            return redirect()->back()->withInput();
        }

        $phone = $input['phone'];
        $contactObj = Contact::NotDeleted()->where('id','!=',$id)->where('group_id',$input['group_id'])->where('phone',$phone)->first();
        if($contactObj != null){
            Session::flash('error', trans('main.phoneError'));
            return redirect()->back();
        }

        $dataObj->name = !isset($input['name']) || empty($input['name']) ? $phone : $input['name'];
        $dataObj->phone = $phone;
        $dataObj->city = $input['city'];
        $dataObj->email = $input['email'];
        $dataObj->country = $input['country'];
        $dataObj->group_id = $input['group_id'];
        $dataObj->lang = $input['lang'];
        $dataObj->notes = $input['notes'];
        $dataObj->status = $input['status'];
        $dataObj->sort = Contact::newSortIndex();
        $dataObj->updated_by = USER_ID;
        $dataObj->updated_at = DATE_TIME;
        $dataObj->save();

        ChatDialog::where('id',str_replace('+', '', $dataObj->phone).'@c.us')->update(['name'=>$dataObj->name]);

        WebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.contacts') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['channels'] = $data['designElems']['userObj']->channels;
        $data['groups'] = GroupNumber::dataList(1,[1])['data'];
        $data['modelProps'] = ['name'=>trans('main.name'),'email'=>trans('main.email'),'country'=>trans('main.country'),'city'=>trans('main.city'),'phone'=>trans('main.whats')];
        $data['timelines'] = WebActions::getByModule($data['designElems']['mainData']['modelName'],10)['data'];
        return view('Tenancy.Contact.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }

        $groupObj = GroupNumber::getOne($input['group_id']);
        if($groupObj == null) {
            return Redirect('404');
        }

        $modelProps = ['name','email','country','city','phone'];
        $userInputs = $input;
        $consForQueue = [];

        $type = $input['vType'];

        if($type == 2){
            if(!isset($input['phone']) || empty($input['phone'])){
                Session::flash('error', trans('main.phoneValidate'));
                return redirect()->back()->withInput();
            }

            $contactObj = Contact::NotDeleted()->where('group_id',$input['group_id'])->where('phone',$input['phone'])->first();
            if(!$contactObj){
                $dataObj = new Contact;
                $dataObj->name = $input['client_name'];
                $dataObj->phone = $input['phone'];
                $dataObj->city = $input['city'];
                $dataObj->email = $input['email'];
                $dataObj->country = $input['country'];
                $dataObj->group_id = $input['group_id'];
                $dataObj->lang = $input['lang'];
                $dataObj->notes = $input['notes'];
                $dataObj->status = $input['status'];
                $dataObj->sort = Contact::newSortIndex();
                $dataObj->created_by = USER_ID;
                $dataObj->created_at = DATE_TIME;
                $dataObj->save();
                $consForQueue[] = $dataObj;
            }else{
                Session::flash('error', trans('main.phoneError'));
                return redirect()->back()->withInput();
            }
        }elseif($type == 3){
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
            for ($i = 0; $i < count($numbersArr) ; $i++) {
                $phone = '+'.str_replace('\r', '', $numbersArr[$i]);
                $contactObj = Contact::NotDeleted()->where('group_id',$input['group_id'])->where('phone',$phone)->first();
                if(!$contactObj){
                    $dataObj = new Contact;
                    $dataObj->phone = trim($phone);
                    $dataObj->group_id = $input['group_id'];
                    $dataObj->name = trim($phone);
                    $dataObj->status = $input['status'];
                    $dataObj->sort = Contact::newSortIndex();
                    $dataObj->created_by = USER_ID;
                    $dataObj->created_at = DATE_TIME;
                    $dataObj->save();
                    $consForQueue[] = $dataObj;
                }else{
                    $foundData[] = $phone;
                }
            }
        }elseif($input['vType'] == 4){
            unset($userInputs['status']);
            unset($userInputs['group_id']);
            unset($userInputs['_token']);
            unset($userInputs['email']);
            unset($userInputs['whatsappNo']);
            unset($userInputs['country']);
            unset($userInputs['city']);
            unset($userInputs['client_name']);
            unset($userInputs['lang']);
            unset($userInputs['notes']);
            unset($userInputs['vType']);
            unset($userInputs['whatsappNos']);

            $storeData = [];
            foreach ($userInputs as $key=> $userInput) {
                if(!in_array($key, $modelProps)){
                    Session::flash('error', trans('main.invalidColumn').' '.$key);
                    return redirect()->back();
                }

                for ($i = 0; $i < count($userInputs['phone']); $i++) {
                    if(!isset($storeData[$i])){
                        $storeData[$i] = [];
                    }
                    if(!isset($storeData[$i][$key])){
                        $storeData[$i][$key] = '';
                    }
                    $storeData[$i][$key] = $userInput[$i];
                    $storeData[$i]['status'] = $input['status'];
                    $storeData[$i]['group_id'] = $input['group_id'];
                    $storeData[$i]['created_at'] = DATE_TIME;
                    $storeData[$i]['created_by'] = USER_ID;
                    $storeData[$i]['sort'] = Contact::newSortIndex()+$i;

                }
            }

            if(count($storeData) > 100){
                Session::flash('error', trans('main.numberlimit',['number'=>100]));
                return redirect()->back()->withInput();
            }

            foreach ($storeData as $value) {
                $contactObj = Contact::NotDeleted()->where('group_id',$value['group_id'])->where('phone',"+".$value['phone'])->first();
                $phone = "+".$value['phone'];
                $phone = str_replace('\r', '', $phone);
                $foundData[] = $phone;
                if(!$contactObj){
                    if(!isset($value['name']) || empty($value['name'])){
                        $value['name'] = $phone;
                    }
                    $value['phone'] = trim($phone);
                    $value['country'] = \Helper::getCountryNameByPhone($value['phone']);
                    $consForQueue[] = $value;
                    Contact::insert($value);
                }else{
                    $foundData[] = $phone;
                }
            }
        }

        $chunks = 400;
        $contacts = array_chunk($consForQueue,$chunks);
        foreach ($contacts as $contact) {
            dispatch(new CheckWhatsappJob($contact));
        }

        WebActions::newType(1,$this->getData()['mainData']['modelName']);
        $returnText = trans('main.addSuccess');
        if(isset($foundData) && !empty($foundData)){
            $returnText = nl2br(trans('main.addSuccess') . '.! \n'.trans('main.whatsappNos').implode(',', $foundData).trans('main.ingroup'));
        }
        Session::flash('success', $returnText);
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = Contact::getOne($id);
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = Contact::find($item[0]);
            if($col == 'phone'){
                $item[2] = '+'.$item[2];
            }elseif ($col == 'name') {
                ChatDialog::where('id',str_replace('+', '', $dataObj->phone).'@c.us')->update(['name'=>$item[2]]);
            }
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = Contact::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            Contact::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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

    public function downloadContacts($group_id){
        $group_id = (int) $group_id;
        $dataObj = GroupNumber::getOne($group_id);
        if($dataObj == null){
            \Session::flash('error',trans('main.notFound'));
            return redirect()->back();
        }
        $count = Contact::NotDeleted()->where('group_id',$group_id)->count();
        if($count>0){
            return Excel::download(new ContactExport($group_id), $dataObj->name_en.' contacts.xlsx');
        }else{
            return redirect()->back();
        }
    }

}
