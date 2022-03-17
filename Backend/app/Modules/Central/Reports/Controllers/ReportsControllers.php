<?php namespace App\Http\Controllers;

use App\Models\UserAddon;
use App\Models\CentralChannel;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\CentralWebActions;
use DataTables;


class ReportsControllers extends Controller {

    use \TraitsFunc;

    public function getData($type){
        $data['mainData'] = [
            'title' => trans('main.reports'),
            'url' => 'reports/'.$type,
            'name' => $type.'Reports',
            'nameOne' => $type.'Report',
            'modelName' => '',
            'icon' => 'far fa-file-alt',
            'sortName' => '',
        ];

        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
            'title_ar' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.name_ar'),
                'specialAttr' => '',
            ],
            'title_en' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '2',
                'label' => trans('main.name_en'),
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
            'instanceId' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'name' => [
                'label' => trans('main.name'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'count' => [
                'label' => trans('main.webhooksCount'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'invoice_id' => [
                'label' => trans('main.invoiceId'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'total' => [
                'label' => trans('main.total'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'paid_date' => [
                'label' => trans('main.paid_date'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'created_at' => [
                'label' => trans('main.date'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
        ];
        return $data;
    }


    public function zid(Request $request) {
        $data = [];
        $userAddons = UserAddon::with('Client')->where('addon_id',4)->where('setting_pushed','>=',0)->orderBy('start_date','DESC')->get();

        $i = 0;
        foreach($userAddons as $mainKey => $userData){
            try {
                tenancy()->initialize($userData->tenant_id);
                $webHooks = \DB::table('webhook_calls')->where('name','Zid')->select(\DB::raw('* ,count(id) as forThisMonth'))->groupBy(\DB::raw('MONTH(created_at)'))->get();
                tenancy()->end();
                } catch (Exception $e) {
                    
                }
            foreach ($webHooks as $key => $value) {
                $i++;
                $startDate = date('Y-m-01',strtotime($value->created_at));
                $endDate = date('Y-m-t',strtotime($value->created_at));

                $invoiceObj = Invoice::NotDeleted()->where('client_id',$userData->user_id)->whereBetween('due_date',[$startDate,$endDate])->where('status',1)->where('items','LIKE','%'. 's:8:"title_ar";s:4:"زد";s:8:"title_en";s:3:"Zid"' .'%')->first();
                if($invoiceObj){
                    $invoiceObj = Invoice::getData($invoiceObj);
                }

                $dataObj = new \stdClass();
                $dataObj->id = $i;
                $dataObj->tenant_id = $userData->tenant_id;
                $dataObj->instanceId = CentralChannel::where('tenant_id',$userData->tenant_id)->first()->instanceId;
                $dataObj->user_id = $userData->user_id;
                $dataObj->name = $userData->Client->name;
                $dataObj->count = $value->forThisMonth;
                $dataObj->paid_date = $invoiceObj != null ? $invoiceObj->paid_date : '';
                $dataObj->total = $invoiceObj != null ? $invoiceObj->total : '';
                $dataObj->invoice_id = $invoiceObj != null ? $invoiceObj->id + 10000 : '';
                $dataObj->created_at = $startDate . ' - ' . $endDate;
                $data[] = $dataObj;
            }
        }

        if($request->ajax()){
            return Datatables::of($data)->make(true);
        }
        $data['designElems'] = $this->getData('zid');
        return view('Central.User.Views.index')->with('data', (object) $data);
    }

    public function salla(Request $request) {
        $data = [];
        $userAddons = UserAddon::with('Client')->where('addon_id',5)->where('setting_pushed','>=',0)->orderBy('start_date','DESC')->get();

        $i = 0;
        foreach($userAddons as $mainKey => $userData){
            try {
                tenancy()->initialize($userData->tenant_id);
                $webHooks = \DB::table('webhook_calls')->where('name','Salla')->select(\DB::raw('* ,count(id) as forThisMonth'))->groupBy(\DB::raw('MONTH(created_at)'))->get();
                tenancy()->end();
                } catch (Exception $e) {
                    
                }
            foreach ($webHooks as $key => $value) {
                $i++;
                $startDate = date('Y-m-01',strtotime($value->created_at));
                $endDate = date('Y-m-t',strtotime($value->created_at));

                $invoiceObj = Invoice::NotDeleted()->where('client_id',$userData->user_id)->whereBetween('due_date',[$startDate,$endDate])->where('status',1)->where('items','LIKE','%'. 's:8:"title_ar";s:6:"سلة";s:8:"title_en";s:5:"Salla"')->first();
                if($invoiceObj){
                    $invoiceObj = Invoice::getData($invoiceObj);
                }

                $dataObj = new \stdClass();
                $dataObj->id = $i;
                $dataObj->tenant_id = $userData->tenant_id;
                $dataObj->instanceId = CentralChannel::where('tenant_id',$userData->tenant_id)->first()->instanceId;
                $dataObj->user_id = $userData->user_id;
                $dataObj->name = $userData->Client->name;
                $dataObj->count = $value->forThisMonth;
                $dataObj->paid_date = $invoiceObj != null ? $invoiceObj->paid_date : '';
                $dataObj->total = $invoiceObj != null ? $invoiceObj->total : '';
                $dataObj->invoice_id = $invoiceObj != null ? $invoiceObj->id + 10000 : '';
                $dataObj->created_at = $startDate . ' - ' . $endDate;
                $data[] = $dataObj;
            }
        }

        if($request->ajax()){
            return Datatables::of($data)->make(true);
        }
        $data['designElems'] = $this->getData('salla');
        return view('Central.User.Views.index')->with('data', (object) $data);
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
