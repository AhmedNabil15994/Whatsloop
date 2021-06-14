<?php namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\User;
use App\Models\CentralUser;
use App\Models\Membership;
use App\Models\Addons;
use App\Models\ExtraQuota;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\UserChannels;
use App\Models\CentralChannel;
use App\Models\Variable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use DataTables;


class TenantInvoiceControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.invoices'),
            'url' => 'invoices',
            'name' => 'invoices',
            'nameOne' => 'invoice',
            'modelName' => 'Invoice',
            'icon' => 'fas fa-file-invoice',
            'sortName' => 'id',
        ];
        $data['clients'] = User::NotDeleted()->where('status',1)->where('group_id',0)->get(['name','id']);
        $data['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
            'client_id' => [
                'type' => 'select',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.client'),
                'options' => $data['clients'],
            ],
            'due_date' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'index' => '2',
                'label' => trans('main.due_date'),
                'specialAttr' => '',
            ],
            'status' => [
                'type' => 'select',
                'class' => 'form-control m-input',
                'index' => '4',
                'label' => trans('main.status'),
                'options' => [
                    ['id' => 0 , 'title' => trans('main.invoice_status_0')],
                    ['id' => 1 , 'title' => trans('main.invoice_status_1')],
                    ['id' => 2 , 'title' => trans('main.invoice_status_2')],
                    ['id' => 3 , 'title' => trans('main.invoice_status_3')],
                    ['id' => 4 , 'title' => trans('main.invoice_status_4')],
                    ['id' => 5 , 'title' => trans('main.invoice_status_5')],
                ],
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
            'client' => [
                'label' => trans('main.client'),
                'type' => '',
                'className' => '',
                'data-col' => 'client_id',
                'anchor-class' => '',
            ],
            'due_date' => [
                'label' => trans('main.due_date'),
                'type' => '',
                'className' => '',
                'data-col' => 'due_date',
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
                'label' => trans('main.created_at'),
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
            $data = Invoice::dataList(null,User::first()->id);
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function edit($id) {
        $id = (int) $id;

        $userObj = Invoice::NotDeleted()->find($id);
        if($userObj == null || $userObj->client_id != User::first()->id) {
            return Redirect('404');
        }

        $data['data'] = Invoice::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['clients'] = $data['designElems']['clients'];
        $data['designElems']['mainData']['title'] = trans('main.edit') . ' '.trans('main.invoices') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-pencil-alt';
        return view('Tenancy.Invoice.Views.edit')->with('data', (object) $data);      
    }

    public function view($id) {
        $id = (int) $id;

        $userObj = Invoice::NotDeleted()->find($id);
        if($userObj == null || $userObj->client_id != User::first()->id) {
            return Redirect('404');
        }
        // dd('here');
        $data['data'] = Invoice::getData($userObj);
        $data['designElems'] = $this->getData();
        $data['clients'] = $data['designElems']['clients'];
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.invoices') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-eye';
        return view('Tenancy.Invoice.Views.view')->with('data', (object) $data);      
    }

    public function update($id) {
        $id = (int) $id;

        $input = \Request::all();
        $dataObj = Invoice::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $dataObj->client_id = $input['client_id'];
        $dataObj->due_date = $input['due_date'];
        $dataObj->total = $input['total'];
        $dataObj->items = serialize($input['items']);
        $dataObj->notes = $input['notes'];
        $dataObj->payment_method = $input['payment_method'];
        $dataObj->status = $input['status'];
        $dataObj->updated_at = DATE_TIME;
        $dataObj->updated_by = USER_ID;
        $dataObj->save();

        WebActions::newType(2,$this->getData()['mainData']['modelName']);
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function checkout($id){
        $id = (int) $id;

        $invoiceObj = Invoice::NotDeleted()->find($id);
        $userObj = User::first();
        if($invoiceObj == null || $invoiceObj->client_id != $userObj->id) {
            return Redirect('404');
        } 

        $names = explode(' ', $userObj->name ,2);
        $invoiceData = [
            'title' => $userObj->name,
            'cc_first_name' => $names[0],
            'cc_last_name' => isset($names[1]) ? $names[1] : '',
            'email' => $userObj->email,
            'cc_phone_number' => '',
            'phone_number' => $userObj->phone,
            'products_per_title' => 'New Membership',
            'reference_no' => 'whatsloop-'.$userObj->id,
            'unit_price' => $invoiceObj->total,
            'quantity' => 1,
            'amount' => $invoiceObj->total,
            'other_charges' => 'VAT',
            'discount' => '',
            'payment_type' => 'mastercard',
            'OrderID' => 'whatsloop-'.$userObj->id,
            'SiteReturnURL' => \URL::to('/invoices/'.$id.'/pushInvoice'),
        ];

        $paymentObj = new \PaymentHelper();        
        return $paymentObj->RedirectWithPostForm($invoiceData);
    }

    public function pushInvoice($id){
        $input = \Request::all();
        // return $this->activate($id);
        
        // dd($input);
        if (isset($input['cartId']) && !empty($input['cartId'])) {
            $postData['OrderID'] = $input['cartId'];
            $paymentObj = new \PaymentHelper();        
            $createPayment = $paymentObj->OpenURLWithPost($postData);
            $CreateaPage = json_decode($createPayment, TRUE);
        
            if ($CreateaPage['Code'] == "1001") {
                if ($CreateaPage['Data']['Status'] == "Success") {
                    return $this->activate($id);
                }
                $UpdateOrder = [];
                if ($CreateaPage['Data']['Status'] == "Rejected") {
                    $UpdateOrder['Status'] = "تم رفض العملية";
                }
                if ($CreateaPage['Data']['Status'] == "Canceled") {
                    $UpdateOrder['Status'] = "تم الالغاء";
                }
                if ($CreateaPage['Data']['Status'] == "Expired Card") {
                    $UpdateOrder['Status'] = "البطاقة المستخدمة منتهية";
                }
                \Session::flash('error',$UpdateOrder['Status']);
                return redirect()->to('/');
            }else{
                \Session::flash('error','حدثت مشكلة في عملية الدفع');
                return redirect()->to('/dashboard');
            }
        }
    }

    public function activate($id){
        $id = (int) $id;

        $invoiceObj = Invoice::NotDeleted()->find($id);
        if($invoiceObj == null) {
            return Redirect('404');
        }

        $cartObj = unserialize($invoiceObj->items);

        $userObj = User::first();
        $centralUser = CentralUser::find($userObj->id);
        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
        $tenant_id = $tenantObj->tenant_id;

        $addons = [];
        $start_date = date('Y-m-d');

        foreach($cartObj as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month')) : date('Y-m-d',strtotime('+1 year'));
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $userObj->update([
                    'membership_id' => $one['data']['id'],
                    'duration_type' => $one['data']['duration_type'],
                ]);

                $centralUser->update([
                    'membership_id' => $one['data']['id'],
                    'duration_type' => $one['data']['duration_type'],
                ]);

                $tenantChannel = UserChannels::first();
                $tenantChannel->start_date = date('Y-m-d');
                $tenantChannel->end_date = $end_date;
                $tenantChannel->save();

                $centralUserChannel = CentralChannel::where('id',$tenantChannel->id)->first();
                $centralUserChannel->start_date = $tenantChannel->start_date;
                $centralUserChannel->end_date = $tenantChannel->end_date;
                $centralUserChannel->save();

            }else if($one['type'] == 'addon'){
                $addons[] = $one['data']['id'];
                UserAddon::create([
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $userObj->global_id,
                    'user_id' => $userObj->id,
                    'addon_id' => $one['data']['id'],
                    'status' => 1,
                    'duration_type' => $one['data']['duration_type'],
                    'start_date' => date('Y-m-d'),
                    'end_date' => $end_date, 
                ]);

            }else if($one['type'] == 'extra_quota'){
                UserExtraQuota::create([
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $userObj->global_id,
                    'user_id' => $userObj->id,
                    'extra_quota_id' => $one['data']['id'],
                    'duration_type' => $one['data']['duration_type'],
                    'status' => 1,
                    'start_date' => date('Y-m-d'),
                    'end_date' => $end_date, 
                ]);
            }
        }

        if(!empty($addon)){
            $userObj->update([
                'addons' =>  serialize($addon),
            ]);

            $centralUser->update([
                'addons' =>  serialize($addon),
            ]);
        }

        $invoiceObj->status = 1;
        $invoiceObj->save();

        $mainUserChannel = UserChannels::first();
        $channelObj = CentralChannel::first();
  
        

        $transferDaysData = [
            'receiver' => $mainUserChannel->id,
            'days' => 3,
            'source' => $channelObj->id,
        ];

        $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        $result = $updateResult->json();

        $userObj->update([
            'channels' => serialize([$mainUserChannel->id]),
        ]);

        $centralUser->update([
            'channels' => serialize([$mainUserChannel->id]),
        ]);

        if(in_array(4,$addons)){
            $varObj = Variable::where('var_key','ZidURL')->first();
            if(!$varObj){
                Variable::insert([
                    [
                        'var_key' => 'ZidURL',
                        'var_value' => 'https://api.zid.sa/v1',
                    ],
                ]);
            }
        }

        if(in_array(5,$addons)){
            $varObj = Variable::where('var_key','SallaURL')->first();
            if(!$varObj){
                Variable::insert([
                    [
                        'var_key' => 'SallaURL',
                        'var_value' => 'https://api.salla.dev/admin/v2',
                    ],
                ]);
            }
        }

        Session::forget('user_id');
        return redirect()->to('/dashboard');
    }

    public function delete($id) {
        $id = (int) $id;
        $dataObj = Invoice::getOne($id);
        WebActions::newType(3,$this->getData()['mainData']['modelName']);
        return \Helper::globalDelete($dataObj);
    }

    public function fastEdit() {
        $input = \Request::all();
        foreach ($input['data'] as $item) {
            $col = $item[1];
            $dataObj = Invoice::find($item[0]);
            $dataObj->$col = $item[2];
            $dataObj->updated_at = DATE_TIME;
            $dataObj->updated_by = USER_ID;
            $dataObj->save();
        }

        WebActions::newType(4,$this->getData()['mainData']['modelName']);
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function arrange() {
        $data = Invoice::dataList();
        $data['designElems'] = $this->getData()['mainData'];
        return view('Tenancy.User.Views.arrange')->with('data', (Object) $data);;
    }

    public function sort(){
        $input = \Request::all();

        $ids = json_decode($input['ids']);
        $sorts = json_decode($input['sorts']);

        for ($i = 0; $i < count($ids) ; $i++) {
            Invoice::where('id',$ids[$i])->update(['sort'=>$sorts[$i]]);
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
