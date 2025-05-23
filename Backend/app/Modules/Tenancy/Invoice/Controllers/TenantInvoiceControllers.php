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
use App\Models\BankAccount;
use App\Models\CentralChannel;
use App\Models\Variable;
use App\Models\PaymentInfo;
use App\Models\CentralVariable;
use App\Models\OldMembership;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use DataTables;
use App\Jobs\NewClient;

use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use PDF;

class TenantInvoiceControllers extends Controller {

    use \TraitsFunc;

    public function getData(){
        $data['mainData'] = [
            'title' => trans('main.subs_invoices'),
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
            'roTtotal' => [
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
            'paid_date' => [
                'label' => trans('main.paid_date'),
                'type' => '',
                'className' => '',
                'data-col' => 'paid_date',
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
    
    public function downloadPDF($id){
        $id = (int) $id;

        $invoiceObj = Invoice::NotDeleted()->find($id);
        if($invoiceObj == null || $invoiceObj->client_id != User::first()->id) {
            return Redirect('404');
        }

        $data['invoice'] = Invoice::getData($invoiceObj);
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];
        $tax = \Helper::calcTax($data['invoice']->roTtotal);
        $paymentObj = PaymentInfo::NotDeleted()->where('user_id',$invoiceObj->client_id)->first();
        if($paymentObj){
            $data['paymentObj'] = PaymentInfo::getData($paymentObj);
        }
        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers), // seller name        
            new TaxNumber($data['companyAddress']->tax_id), // seller tax number
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['invoice']->due_date))), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($data['invoice']->roTtotal), // invoice total amount
            new InvoiceTaxAmount($tax) // invoice tax amount
            // TODO :: Support others tags
        ])->render();

        $pdf = PDF::loadView('Tenancy.Invoice.Views.V5.invoicePDF',['data'=> (object)$data])
                ->setPaper('a4', 'portrait')
                ->setOption('margin-bottom', '0mm')
                ->setOption('margin-top', '0mm')
                ->setOption('margin-right', '0mm')
                ->setOption('margin-left', '0mm');
        return $pdf->download('invoice #'.($id+10000).'.pdf');
    }

    public function index(Request $request) {
        if($request->ajax()){
            $data = Invoice::dataList(null,User::first()->id);
            return Datatables::of($data['data'])->make(true);
        }
        $data['designElems'] = $this->getData();

        // Fetch Subscription Data
        $membershipObj = Session::get('membership') != null ?  Membership::getData(Membership::getOne(Session::get('membership'))) : [];
        $channelObj = Session::get('channel') != null ?  CentralChannel::getData(CentralChannel::getOne(Session::get('channel'))) : null;
        if($channelObj){
            $channelStatus = ($channelObj->leftDays > 0 && date('Y-m-d') <= $channelObj->end_date) ? 1 : 0;
        }

        $data['subscription'] = (object) [
            'package_name' => $channelObj ? $membershipObj->title : '',
            'channelStatus' => $channelObj ? $channelStatus : '',
            'start_date' => $channelObj ? $channelObj->start_date : '',
            'end_date' => $channelObj ? $channelObj->end_date : '',
            'leftDays' => $channelObj ? $channelObj->leftDays : '',
        ];
        return view('Tenancy.Invoice.Views.V5.index')->with('data', (object) $data);
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
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];
        $data['designElems'] = $this->getData();
        $data['clients'] = $data['designElems']['clients'];
        $data['designElems']['mainData']['title'] = trans('main.view') . ' '.trans('main.invoices') ;
        $data['designElems']['mainData']['icon'] = 'fa fa-eye';
        $tax = \Helper::calcTax($data['data']->roTtotal);
        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers), // seller name        
            new TaxNumber($data['companyAddress']->tax_id), // seller tax number
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['data']->due_date))), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($data['data']->roTtotal), // invoice total amount
            new InvoiceTaxAmount($tax) // invoice tax amount
            // TODO :: Support others tags
        ])->render();
        return view('Tenancy.Invoice.Views.V5.view')->with('data', (object) $data);      
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
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $invoiceObj = Invoice::NotDeleted()->find($id);
        $userObj = User::first();
        if($invoiceObj == null || $invoiceObj->client_id != $userObj->id) {
            return Redirect('404');
        } 
        
        $myData   = unserialize($invoiceObj->items);
       
        $invoiceObj = Invoice::getData($invoiceObj);
        $discount = $invoiceObj->discount;
        $testData = [];
        $total = 0;
        $main = 0;
        $start_date = $invoiceObj->due_date < date('Y-m-d') && $invoiceObj->status == 2 ? date('Y-m-d') : $invoiceObj->due_date;
        foreach($myData as $key => $one){
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $title = $dataObj->{'title_'.LANGUAGE_PREF};
                $main = 1;
            }else if($one['type'] == 'addon'){
                $dataObj = Addons::getOne($one['data']['id']);
                $title = $dataObj->{'title_'.LANGUAGE_PREF};
            }else if($one['type'] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one['data']['id']));
                $title = $dataObj->extra_count . ' '.$dataObj->extraTypeText . ' ' . ($dataObj->extra_type == 1 ? trans('main.msgPerDay') : '');
            }
            $testData[$key] = [
                $dataObj->id,
                $one['type'],
                $title,
                $one['data']['duration_type'],
                $start_date,
                $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date))),
                $one['data']['duration_type'] == 1 ? $dataObj->monthly_after_vat : $dataObj->annual_after_vat,
                (int)$one['data']['quantity'],
            ];
            $total+= $testData[$key][6] * (int)$one['data']['quantity'];
        }

        $userObj = User::getOne(USER_ID);
        $type = 'PayInvoice';
        if(Session::has('invoice_id') && Session::get('invoice_id') > 0){
            $type = 'Suspended';
        }
        $cartObj = Variable::where('var_key','inv_status')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }
        $cartObj->var_key = 'inv_status';
        $cartObj->var_value = $type;
        $cartObj->save();

        // $paymentObj = new \SubscriptionHelper(); 
        // $invoice = $paymentObj->setInvoice($testData,USER_ID,TENANT_ID,GLOBAL_ID,$type);   

        $data['user'] = $userObj;
        $data['invoice'] = $invoiceObj;
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];

        $data['data'] = $testData;
        $tax = $invoiceObj->tax;
        $data['totals'] = [
            $invoiceObj->grandTotal,
            $invoiceObj->discount,
            $invoiceObj->tax,
            $invoiceObj->roTtotal,
        ];

        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers), // seller name        
            new TaxNumber($data['companyAddress']->tax_id), // seller tax number
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['invoice']->due_date))), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($total - $discount), // invoice total amount
            new InvoiceTaxAmount($tax) // invoice tax amount
            // TODO :: Support others tags
        ])->render();


        if($invoiceObj->discount_value){
            $data['totals'] = [
                $invoiceObj->grandTotal,
                $invoiceObj->discount,
                $invoiceObj->tax,
                $invoiceObj->roTtotal,
            ];
        }
        // dd($data['totals']);
        $data['countries'] = \DB::connection('main')->table('country')->get();
        $data['regions'] = [];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['bankAccounts'] = BankAccount::dataList(1)['data'];
        $data['disDelete'] = true;
        return view('Tenancy.Dashboard.Views.V5.checkout')->with('data',(object) $data);
    }

    public function postCheckout($id){
        $id = (int) $id;
        $input = \Request::all();
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $invoiceObj = Invoice::NotDeleted()->find($id);
        $userObj = User::first();
        if($invoiceObj == null || $invoiceObj->client_id != $userObj->id) {
            return Redirect('404');
        } 

        $total = json_decode($input['totals']);
        $totals = $total[3];
        $cartData = $input['data'];

        if(Session::has('userCredits')){
            $userCreditsObj = Variable::where('var_key','userCredits')->first();
            if(!$userCreditsObj){
                $userCreditsObj = new Variable();
            }
            $userCreditsObj->var_value = Session::get('userCredits');
            $userCreditsObj->var_key = 'userCredits';
            $userCreditsObj->save();
        }
        
        $userObj = User::first();

        $paymentInfoObj = PaymentInfo::NotDeleted()->where('user_id',$userObj->id)->first();
        if(!$paymentInfoObj){
            $paymentInfoObj = new PaymentInfo;
        }
        if(isset($input['address']) && !empty($input['address'])){
            $paymentInfoObj->user_id = $userObj->id;
            $paymentInfoObj->address = $input['address'];
            $paymentInfoObj->address2 = $input['address2'];
            $paymentInfoObj->city = $input['city'];
            $paymentInfoObj->country = $input['country'];
            $paymentInfoObj->region = $input['region'];
            $paymentInfoObj->postal_code = $input['postal_code'];
            $paymentInfoObj->tax_id = $input['tax_id'];
            $paymentInfoObj->created_at = DATE_TIME;
            $paymentInfoObj->created_by = $userObj->id;
            $paymentInfoObj->save();
        }

        $names = explode(' ', $userObj->name ,2);
        if($input['payType'] == 2){// Noon Integration
            $urlSecondSegment = '/noon';
            $noonData = [
                'returnURL' => \URL::to('/invoices/'.$id.'/pushInvoice'),
                // 'returnURL' => \URL::to('/pushInvoice'),  // For Local 
                'cart_id' => 'invoice-'.$invoiceObj->id,
                'cart_amount' => $totals,
                'cart_description' => 'New Invoice Payment',
                'paypage_lang' => LANGUAGE_PREF,
                'description' => 'Paying Invoice '.$invoiceObj->id,
            ];

            $paymentObj = new \PaymentHelper(); 
            $resultData = $paymentObj->initNoon($noonData);            
                   
            $result = $paymentObj->hostedPayment($resultData['dataArr'],$urlSecondSegment,$resultData['extraHeaders']);
            $result = json_decode($result);

            if(($result->data) && $result->data->result->redirect_url){
                return redirect()->away($result->data->result->redirect_url);
            }
        }
    }

    public function pushInvoice($id){
        $input = \Request::all();
        $id = (int) $id;
        $data['data'] = json_decode($input['data']);
        $data['status'] = json_decode($input['status']);

        if($data['status']->status == 1){
            return $this->activate($id,$data['data']->transaction_id,$data['data']->paymentGateaway);
        }else{
            $userObj = User::first();
            User::setSessions($userObj);
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/paymentError')->withInput();
        }
    }

    public function activate($id,$transaction_id = null , $paymentGateaway = null){
        $id = (int) $id;

        $invoiceObj = Invoice::NotDeleted()->find($id);
        if($invoiceObj == null) {
            return Redirect('404');
        }

        $cartObj = unserialize($invoiceObj->items);
        $type = Variable::getVar('inv_status');
        $data = [
            'cartObj' => $cartObj, 
            'type' => $type,
            'transaction_id' => $transaction_id,
            'paymentGateaway' => $paymentGateaway,
            'start_date' => $type == 'Suspended' ? date('Y-m-d') : $invoiceObj->due_date,
            'invoiceObj' => $invoiceObj,
            'transferObj' => null,
            'arrType' => 'old',
            'myEndDate' => null,
        ];

        try {
            dispatch(new NewClient($data))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }
        // $paymentObj = new \SubscriptionHelper(); 
        // $resultData = $paymentObj->newSubscription($cartObj,'payInvoice',$transaction_id,$paymentGateaway,$invoiceObj->due_date,$invoiceObj,null,'old');   
        // if($resultData[0] == 0){
        //     Session::flash('error',$resultData[1]);
        //     return back()->withInput();
        // }     
        $userObj = User::first();
        User::setSessions($userObj);
        return redirect()->to('/invoices/view/'.$id);
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
