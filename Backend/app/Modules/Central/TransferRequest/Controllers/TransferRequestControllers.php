<?php namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\CentralUser;
use App\Models\Variable;
use App\Models\CentralChannel;
use App\Models\UserChannels;
use App\Models\UserExtraQuota;
use App\Models\UserAddon;
use App\Models\Invoice;
use App\Models\Membership;
use App\Models\Addons;
use App\Models\ExtraQuota;
use App\Models\Tenant;
use App\Models\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\CentralWebActions;
use DataTables;


class TransferRequestControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.transfers'),
            'url' => 'transfers',
            'name' => 'transfers',
            'nameOne' => 'transfers',
            'modelName' => 'BankTransfer',
            'icon' => ' dripicons-duplicate',
            'sortName' => 'user_id',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
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
            'order_no' => [
                'label' => trans('main.order_no'),
                'type' => '',
                'className' => '',
                'data-col' => 'order_no',
                'anchor-class' => '',
            ],
            'client' => [
                'label' => trans('main.client'),
                'type' => '',
                'className' => '',
                'data-col' => 'user_id',
                'anchor-class' => '',
            ],
            'total' => [
                'label' => trans('main.total'),
                'type' => '',
                'className' => '',
                'data-col' => 'total',
                'anchor-class' => '',
            ],
            'statusText' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'status',
                'anchor-class' => '',
            ],
            'created_at' => [
                'label' => trans('main.date'),
                'type' => '',
                'className' => '',
                'data-col' => 'created_at',
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

    public function index(Request $request) {
        if($request->ajax()){
            $data = BankTransfer::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Central.User.Views.index')->with('data', (object) $data);
    }

    public function view($id) {
        $id = (int) $id;

        $userObj = BankTransfer::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = BankTransfer::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.departments') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-eye';
        return view('Central.TransferRequest.Views.view')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        $transferObj = BankTransfer::NotDeleted()->find($id);
        $status = (int) $input['status'];
        if($transferObj == null || !in_array($status , [2,3])) {
            return Redirect('404');
        }

        $oldStatus = $transferObj->status;

        $beginProcess = 0;
        if($status == 2){
            $beginProcess = 1;
        }

        if($beginProcess){
            $tenant = Tenant::find($transferObj->tenant_id);
            tenancy()->initialize($tenant);
            $cartObj = Variable::getVar('cartObj');
            $cartObj = json_decode(json_decode($cartObj));
            tenancy()->end($tenant);

            $paymentObj = new \SubscriptionHelper(); 
            $resultData = $paymentObj->newSubscription($cartObj,'transferRequest',$transferObj->order_no,trans('main.bankTransfer'),date('Y-m-d'),null,$transferObj);   
            if($resultData[0] == 0){
                Session::flash('error',$resultData[1]);
                return back()->withInput();
            }  
        }

        // if($status == 3){
        //     if($oldStatus !== 2){
        //         $transferObj->status = $status;
        //     }
        // }elseif($status == 2){
        //     $transferObj->status = $status;
        // }
        $transferObj->status = $status;

        $transferObj->updated_at = DATE_TIME;
        $transferObj->updated_by = USER_ID;
        $transferObj->save();

        CentralWebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }


    // public function add() {
    //     $data['designElems'] = $this->getData();
    //     $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.departments') ;
    //     $data['designElems']['mainData']['icon'] = 'fa fa-plus';
    //     $data['emps'] = CentralUser::NotDeleted()->where('status',1)->whereNotIn('group_id',[0,1])->get();
    //     return view('Central.Department.Views.add')->with('data', (object) $data);
    // }

    // public function create() {
    //     $input = \Request::all();
    //     $validate = $this->validateInsertObject($input);
    //     if($validate->fails()){
    //         Session::flash('error', $validate->messages()->first());
    //         return redirect()->back()->withInput();
    //     }

    //     $dataObj = new CentralDepartment;
    //     $dataObj->title_ar = $input['title_ar'];
    //     $dataObj->title_en = $input['title_en'];
    //     if(isset($input['emps']) && !empty($input['emps'])){
    //         $dataObj->emps = serialize($input['emps']);
    //     }
    //     $dataObj->sort = CentralDepartment::newSortIndex();
    //     $dataObj->status = $input['status'];
    //     $dataObj->created_at = DATE_TIME;
    //     $dataObj->created_by = USER_ID;
    //     $dataObj->save();

    //     CentralWebActions::newType(1,$this->getData()['mainData']['modelName']);
    //     Session::flash('success', trans('main.addSuccess'));
    //     return redirect()->to($this->getData()['mainData']['url'].'/');
    // }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = BankTransfer::getOne($id);
        CentralWebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    // public function fastEdit() {
    //     $input = \Request::all();
    //     foreach ($input['data'] as $item) {
    //         $col = $item[1];
    //         $dataObj = CentralDepartment::find($item[0]);
    //         $dataObj->$col = $item[2];
    //         $dataObj->updated_at = DATE_TIME;
    //         $dataObj->updated_by = USER_ID;
    //         $dataObj->save();
    //     }

    //     CentralWebActions::newType(4,$this->getData()['mainData']['modelName']);
    //     return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    // }

    // public function arrange() {
    //     $data = CentralDepartment::dataList();
    //     $data['designElems'] = $this->getData()['mainData'];
    //     return view('Central.User.Views.arrange')->with('data', (Object) $data);;
    // }

    // public function sort(){
    //     $input = \Request::all();

    //     $ids = json_decode($input['ids']);
    //     $sorts = json_decode($input['sorts']);

    //     for ($i = 0; $i < count($ids) ; $i++) {
    //         CentralDepartment::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
    //     }
    //     return \TraitsFunc::SuccessResponse(trans('main.sortSuccess'));
    // }

    // public function charts() {
    //     $input = \Request::all();
    //     $now = date('Y-m-d');
    //     $start = $now;
    //     $end = $now;
    //     $date = null;
    //     if(isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])){
    //         $start = $input['from'].' 00:00:00';
    //         $end = $input['to'].' 23:59:59';
    //         $date = 1;
    //     }

    //     $addCount = CentralWebActions::getByDate($date,$start,$end,1,$this->getData()['mainData']['modelName'])['count'];
    //     $editCount = CentralWebActions::getByDate($date,$start,$end,2,$this->getData()['mainData']['modelName'])['count'];
    //     $deleteCount = CentralWebActions::getByDate($date,$start,$end,3,$this->getData()['mainData']['modelName'])['count'];
    //     $fastEditCount = CentralWebActions::getByDate($date,$start,$end,4,$this->getData()['mainData']['modelName'])['count'];

    //     $data['chartData1'] = $this->getChartData($start,$end,1,$this->getData()['mainData']['modelName']);
    //     $data['chartData2'] = $this->getChartData($start,$end,2,$this->getData()['mainData']['modelName']);
    //     $data['chartData3'] = $this->getChartData($start,$end,4,$this->getData()['mainData']['modelName']);
    //     $data['chartData4'] = $this->getChartData($start,$end,3,$this->getData()['mainData']['modelName']);
    //     $data['counts'] = [$addCount , $editCount , $deleteCount , $fastEditCount];
    //     $data['designElems'] = $this->getData()['mainData'];

    //     return view('Central.User.Views.charts')->with('data',(object) $data);
    // }

    // public function getChartData($start=null,$end=null,$type,$moduleName){
    //     $input = \Request::all();
        
    //     if(isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])){
    //         $start = $input['from'];
    //         $end = $input['to'];
    //     }

    //     $datediff = strtotime($end) - strtotime($start);
    //     $daysCount = round($datediff / (60 * 60 * 24));
    //     $datesArray = [];
    //     $datesArray[0] = $start;

    //     if($daysCount > 2){
    //         for($i=0;$i<$daysCount;$i++){
    //             $datesArray[$i] = date('Y-m-d',strtotime($start.'+'.$i."day") );
    //         }
    //         $datesArray[$daysCount] = $end;  
    //     }else{
    //         for($i=1;$i<24;$i++){
    //             $datesArray[$i] = date('Y-m-d H:i:s',strtotime($start.'+'.$i." hour") );
    //         }
    //     }

    //     $chartData = [];
    //     $dataCount = count($datesArray);

    //     for($i=0;$i<$dataCount;$i++){
    //         if($dataCount == 1){
    //             $count = CentralWebActions::where('type',$type)->where('module_name',$moduleName)->where('created_at','>=',$datesArray[0].' 00:00:00')->where('created_at','<=',$datesArray[0].' 23:59:59')->count();
    //         }else{
    //             if($i < count($datesArray)){
    //                 $count = CentralWebActions::where('type',$type)->where('module_name',$moduleName)->where('created_at','>=',$datesArray[$i].' 00:00:00')->where('created_at','<=',$datesArray[$i].' 23:59:59')->count();
    //             }
    //         }
    //         $chartData[0][$i] = $datesArray[$i];
    //         $chartData[1][$i] = $count;
    //     }
    //     return $chartData;
    // }
}
