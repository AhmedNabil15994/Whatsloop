<?php namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use DataTables;
use Storage;


class RepliesControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->title = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;
        
        $data['mainData'] = [
            'title' => trans('main.replies'),
            'url' => 'replies',
            'name' => 'replies',
            'nameOne' => 'reply',
            'modelName' => 'Reply',
            'icon' => ' far fa-comment-alt',
            'sortName' => 'name_'.LANGUAGE_PREF,
            'addOne' => trans('main.newReply'),
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
            // 'name_en' => 'required',
        ];

        $message = [
            'name_ar.required' => trans('main.titleArValidate'),
            // 'name_en.required' => trans('main.titleEnValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = Reply::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = Reply::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = Reply::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.replies') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        $data['timelines'] = WebActions::getByModule($data['designElems']['mainData']['modelName'],10)['data'];
        return view('Tenancy.User.Views.edit')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        // dd($input);
        $dataObj = Reply::NotDeleted()->find($id);
        if($dataObj == null) {
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
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.replies') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        $data['timelines'] = WebActions::getByModule($data['designElems']['mainData']['modelName'],10)['data'];
        return view('Tenancy.User.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }
        

        $dataObj = new Reply;
        $dataObj->channel = Session::get('channelCode');
        $dataObj->name_ar = $input['name_ar'];
        $dataObj->name_en = !empty($input['name_en']) ? $input['name_en'] : ' ';
        $dataObj->description_ar = $input['description_ar'];
        $dataObj->description_en = $input['description_en'];
        $dataObj->sort = Reply::newSortIndex();
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
        $dataObj = Reply::getOne($id);
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = Reply::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = Reply::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            Reply::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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
