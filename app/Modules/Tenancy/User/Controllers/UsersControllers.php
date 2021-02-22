<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use DataTables;
use Storage;


class UsersControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $groups = Group::dataList()['data'];
        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value;
            $channelObj->title = $value;
            $channels[] = $channelObj;
        }
        $data['mainData'] = [
            'title' => trans('main.users'),
            'url' => 'users',
            'name' => 'users',
            'nameOne' => 'user',
            'modelName' => 'User',
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
            'group_id' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '',
                'options' => $groups,
                'label' => trans('main.group'),
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
            'phone' => [
                'label' => trans('main.phone'),
                'type' => '',
                'className' => 'edits',
                'data-col' => 'phone',
                'anchor-class' => 'editable',
            ],
            'group' => [
                'label' => trans('main.group'),
                'type' => '',
                'className' => 'edits selects',
                'data-col' => 'group_id',
                'anchor-class' => 'editable',
            ],
            'channelCodes' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => 'edits selects',
                'data-col' => 'channels',
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
            'group_id' => [
                'type' => 'select',
                'class' => 'form-control',
                'options' => $groups,
                'label' => trans('main.group'),
                'specialAttr' => '',
            ],
            'name' => [
                'type' => 'text',
                'class' => 'form-control',
                'label' => trans('main.name'),
                'specialAttr' => '',
            ],
            'phone' => [
                'type' => 'number',
                'class' => 'form-control',
                'label' => trans('main.phone'),
                'specialAttr' => '',
            ],
            'password' => [
                'type' => 'password',
                'class' => 'form-control',
                'label' => trans('main.password'),
                'specialAttr' => '',
            ],
            'email' => [
                'type' => 'email',
                'class' => 'form-control',
                'label' => trans('main.email'),
                'specialAttr' => '',
            ],
            'channels' => [
                'type' => 'select',
                'class' => 'form-control',
                'options' => $channels,
                'label' => trans('main.channel'),
                'specialAttr' => '',
            ],
            'image' => [
                'type' => 'image',
                'class' => 'form-control',
                'label' => trans('main.image'),
                'specialAttr' => '',
            ],
            
        ];
        return $data;
    }

    protected function validateInsertObject($input){
        $rules = [
            'group_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'password' => 'required|min:6',
            'email' => 'required',
        ];

        $message = [
            'group_id.required' => trans('main.groupValidate'),
            'name.required' => trans('main.nameValidate'),
            'phone.required' => trans('main.phoneValidate'),
            'password.required' => trans('main.passwordValidate'),
            'password.min' => trans('main.passwordValidate2'),
            'email.required' => trans('main.emailValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    protected function validateUpdateObject($input){
        $rules = [
            'group_id' => 'required',
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ];

        $message = [
            'group_id.required' => trans('main.groupValidate'),
            'name.required' => trans('main.nameValidate'),
            'phone.required' => trans('main.phoneValidate'),
            'email.required' => trans('main.emailValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = User::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = User::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = User::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.users') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['permissions'] = \Helper::getPermissions(true);
        $data['timelines'] = WebActions::getByModule($data['designElems']['mainData']['modelName'],10)['data'];
        return view('Tenancy.User.Views.edit')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        // dd($input);
        $dataObj = User::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $validate = $this->validateUpdateObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $userObj = User::checkUserBy('email',$input['email'],$id);
        if($userObj){
            Session::flash('error', trans('main.emailError'));
            return redirect()->back()->withInput();
        }

        if(isset($input['phone']) && !empty($input['phone'])){
            $userObj = User::checkUserBy('phone','+'.$input['phone'],$id);
            if($userObj){
                Session::flash('error', trans('main.phoneError'));
                return redirect()->back()->withInput();
            }
        }

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

            $dataObj->password = \Hash::make($input['password']);
        }

        $permissionsArr = [];
        foreach ($input as $key => $oneItem) {
            if(strpos($key, 'permission') !== false){
                $morePermission = str_replace('permission','', $key);
                if($oneItem == 'on'){
                    $permissionsArr[] = $morePermission;
                }
            }
        }

        if(isset($input['channels']) && !empty(isset($input['channels']))){
            $dataObj->channels = serialize([$input['channels']]);
        }

        $dataObj->name = $input['name'];
        $dataObj->group_id = $input['group_id'];
        $dataObj->email = $input['email'];
        $dataObj->phone = '+'.$input['phone'];
        $dataObj->extra_rules = serialize($permissionsArr);
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        $photos_name = Session::get('photos');
        if($photos_name){
            $photos = Storage::files($photos_name);
            if(count($photos) > 0){
                $images = self::addImage($photos[0],$dataObj->id);
                if ($images == false) {
                    Session::flash('error', trans('main.uploadProb'));
                    return redirect()->back()->withInput();
                }
                $dataObj->image = $images;
                $dataObj->save();  
            }
        }

        Session::forget('photos');
        WebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.users') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['timelines'] = WebActions::getByModule($data['designElems']['mainData']['modelName'],10)['data'];
        $data['permissions'] = \Helper::getPermissions(true);
        return view('Tenancy.User.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }
        
        $userObj = User::checkUserBy('email',$input['email']);
        if($userObj){
            Session::flash('error', trans('main.emailError'));
            return redirect()->back()->withInput();
        }

        if(isset($input['phone']) && !empty($input['phone'])){
            $userObj = User::checkUserBy('phone','+'.$input['phone']);
            if($userObj){
                Session::flash('error', trans('main.phoneError'));
                return redirect()->back()->withInput();
            }
        }

        $permissionsArr = [];
        foreach ($input as $key => $oneItem) {
            if(strpos($key, 'permission') !== false){
                $morePermission = str_replace('permission','', $key);
                if($oneItem == 'on'){
                    $permissionsArr[] = $morePermission;
                }
            }
        }

        $dataObj = new User;
        if(isset($input['channels']) && !empty(isset($input['channels']))){
            $dataObj->channels = serialize([$input['channels']]);
        }

        $dataObj->name = $input['name'];
        $dataObj->group_id = $input['group_id'];
        $dataObj->email = $input['email'];
        $dataObj->phone = '+'.$input['phone'];
        $dataObj->password = \Hash::make($input['password']);
        $dataObj->extra_rules = serialize($permissionsArr);
        $dataObj->sort = User::newSortIndex();
        $dataObj->status = $input['status'];
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        $photos_name = Session::get('photos');
        if($photos_name){
            $photos = Storage::files($photos_name);
            if(count($photos) > 0){
                $images = self::addImage($photos[0],$dataObj->id);
                if ($images == false) {
                    Session::flash('error', trans('main.uploadProb'));
                    return redirect()->back()->withInput();
                }
                $dataObj->image = $images;
                $dataObj->save();  
            }
        }

        Session::forget('photos');
        WebActions::newType(1,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = User::getOne($id);
        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/'.$this->getData()['mainData']['name'].'/'.$id);
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            if($col == 'email'){
                $userObj = User::checkUserBy('email',$item[2],$item[0]);
                if($userObj){
                    return \TraitsFunc::ErrorMessage(trans('main.emailFound',['email'=>$item[2]]));
                }
            }

            if($col == 'phone'){
                $userObj = User::checkUserBy('phone',$item[2],$item[0]);
                if($userObj){
                    return \TraitsFunc::ErrorMessage(trans('main.phoneFound',['phone'=>$item[2]]));
                }
            }

            $dataObj = User::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = User::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            User::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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
        $fileName = \ImagesHelper::UploadFile($this->getData()['mainData']['name'], $images, $nextID);
        if($fileName == false){
            return false;
        }
        return $fileName;        
    }

    public function deleteImage($id){
        $id = (int) $id;
        $input = \Request::all();

        $menuObj = User::find($id);
        if($menuObj == null) {
            return \TraitsFunc::ErrorMessage(trans('main.userNotFound'));
        }

        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/'.$this->getData()['mainData']['name'].'/'.$id.'/'.$menuObj->image);
        $menuObj->image = '';
        $menuObj->save();
        return \TraitsFunc::SuccessResponse(trans('main.imgDeleted'));
    }

}
