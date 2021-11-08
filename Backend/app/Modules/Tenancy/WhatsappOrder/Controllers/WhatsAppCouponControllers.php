<?php namespace App\Http\Controllers;

use App\Models\WhatsAppCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\CentralWebActions;
use DataTables;


class WhatsAppCouponControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.coupons'),
            'url' => 'whatsappOrders/coupons',
            'name' => 'coupons',
            'nameOne' => 'coupon',
            'modelName' => 'WhatsAppCoupon',
            'icon' => ' fas fa-star',
            'sortName' => 'code',
            'addOne' => trans('main.newCoupon'),
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
            'code' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.coupon_code'),
                'specialAttr' => '',
            ],
            'discount_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '2',
                'options' => [
                    ['id' => 1 , 'title' => trans('main.discount_type_1')],
                    ['id' => 2 , 'title' => trans('main.discount_type_2')],
                ],
                'label' => trans('main.discount_type'),
                'specialAttr' => ' data-toggle="select2"',
            ],
            'discount_value' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '3',
                'label' => trans('main.discount_value'),
                'specialAttr' => '',
            ],
            'valid_type' => [
                'type' => 'select',
                'class' => 'form-control',
                'index' => '4',
                'options' => [
                    ['id' => 1 , 'title' => trans('main.valid_type_1')],
                    ['id' => 2 , 'title' => trans('main.valid_type_2')],
                ],
                'label' => trans('main.valid_type'),
                'specialAttr' => ' data-toggle="select2"',
            ],
            'valid_value' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '5',
                'label' => trans('main.valid_value'),
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
            'code' => [
                'label' => trans('main.coupon_code'),
                'type' => '',
                'className' => '',
                'data-col' => 'code',
                'anchor-class' => '',
            ],
            'discount_typeText' => [
                'label' => trans('main.discount_type'),
                'type' => '',
                'className' => '',
                'data-col' => 'discount_type',
                'anchor-class' => '',
            ],
            'discount_value' => [
                'label' => trans('main.discount_value'),
                'type' => '',
                'className' => '',
                'data-col' => 'discount_value',
                'anchor-class' => '',
            ],
            'valid_typeText' => [
                'label' => trans('main.valid_type'),
                'type' => '',
                'className' => '',
                'data-col' => 'valid_type',
                'anchor-class' => '',
            ],
            'valid_value' => [
                'label' => trans('main.valid_value'),
                'type' => '',
                'className' => '',
                'data-col' => 'valid_value',
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
            'code' => 'required',
            'discount_type' => 'required',
            'discount_value' => 'required',
            'valid_type' => 'required',
            'valid_value' => 'required',
        ];

        $message = [
            'code.required' => trans('main.codeValidate'),
            'discount_type.required' => trans('main.discountTypeValidate'),
            'discount_value.required' => trans('main.discountValueValidate'),
            'valid_type.required' => trans('main.validTypeValidate'),
            'valid_value.required' => trans('main.validValueVatValidate'),
        ];

        $validate = \Validator::make($input, $rules, $message);

        return $validate;
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = WhatsAppCoupon::dataList();
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = WhatsAppCoupon::NotDeleted()->find($id);
        if($userObj == null) {
            return Redirect('404');
        }

        $data['data'] = WhatsAppCoupon::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.coupons') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        return view('Tenancy.Coupon.Views.edit')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        $dataObj = WhatsAppCoupon::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back();
        }

        $checkObj = WhatsAppCoupon::checkCouponByCode($input['code'],$id);
        if($checkObj != null){
            \Session::flash('error', trans('main.codeFound'));
            return redirect()->back()->withInput();
        }

        $dataObj->code = $input['code'];
        $dataObj->discount_type = $input['discount_type'];
        $dataObj->discount_value = $input['discount_value'];
        $dataObj->valid_type = $input['valid_type'];
        $dataObj->valid_value = $input['valid_value'];
        $dataObj->status = $input['status'];
        $dataObj->updated_at = DATE_TIME;
        $dataObj->updated_by = USER_ID;
        $dataObj->save();

        CentralWebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function add() {
        $data['designElems'] = $this->getData();
        $data['designElems']['mainData']['title'] = trans('main.add') . ' '.trans('main.coupons') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-plus';
        return view('Tenancy.Coupon.Views.add')->with('data', (object) $data);
    }

    public function create() {
        $input = \Request::all();
        $validate = $this->validateInsertObject($input);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return redirect()->back()->withInput();
        }
        
        $checkObj = WhatsAppCoupon::checkCouponByCode($input['code']);
        if($checkObj != null){
            \Session::flash('error', trans('main.codeFound'));
            return redirect()->back()->withInput();
        }

        $dataObj = new WhatsAppCoupon;
        $dataObj->code = $input['code'];
        $dataObj->discount_type = $input['discount_type'];
        $dataObj->discount_value = $input['discount_value'];
        $dataObj->valid_type = $input['valid_type'];
        $dataObj->valid_value = $input['valid_value'];
        $dataObj->sort = WhatsAppCoupon::newSortIndex();
        $dataObj->status = $input['status'];
        $dataObj->created_at = DATE_TIME;
        $dataObj->created_by = USER_ID;
        $dataObj->save();

        CentralWebActions::newType(1,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.addSuccess'));
        return redirect()->to($this->getData()['mainData']['url'].'/');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = WhatsAppCoupon::getOne($id);
        CentralWebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = WhatsAppCoupon::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        CentralWebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = WhatsAppCoupon::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Central.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            WhatsAppCoupon::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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
}
