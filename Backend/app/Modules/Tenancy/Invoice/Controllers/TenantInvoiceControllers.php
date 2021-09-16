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
use App\Models\PaymentInfo;
use App\Models\CentralVariable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use DataTables;


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
        return view('Tenancy.Invoice.Views.index')->with('data', (object) $data);
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
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $invoiceObj = Invoice::NotDeleted()->find($id);
        $userObj = User::first();
        if($invoiceObj == null || $invoiceObj->client_id != $userObj->id) {
            return Redirect('404');
        } 

        $myData   = unserialize($invoiceObj->items);
        $testData = [];
        $total = 0;
        $start_date = $invoiceObj->due_date;
        foreach($myData as $key => $one){
            if($one['type'] == 'membership'){
                $dataObj = Membership::getOne($one['data']['id']);
                $title = $dataObj->{'title_'.LANGUAGE_PREF};
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
        
        $data['data'] = $testData;
        $data['data'] = $testData;
        $tax = \Helper::calcTax($total);
        $data['totals'] = [
            $total-$tax,
            0,
            $tax,
            $total,
        ];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        return view('Tenancy.Dashboard.Views.checkout')->with('data',(object) $data);
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

            $startDateObj = Variable::where('var_key','start_date')->first();
            if(!$startDateObj){
                $startDateObj = new Variable();
            }
            $startDateObj->var_value = json_decode($cartData)[0][4];
            $startDateObj->var_key = 'start_date';
            $startDateObj->save();
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
        if($input['payType'] == 2){ // Paytabs Integration
            $profileId = '49334';
            $serverKey = 'SWJNLRLRKG-JBZZRMGMMM-GZKTBBLMNW';

            $dataArr = [
                'returnURL' => \URL::to('/invoices/'.$id.'/pushInvoice'),
                'cart_id' => 'whatsloop-'.$userObj->id,
                'cart_amount' => $totals,
                'cart_description' => 'New',
                'paypage_lang' => LANGUAGE_PREF,
                'name' => $userObj->name,
                'email' => $userObj->email,
                'phone' => $userObj->phone,
                'street' => $paymentInfoObj->address,
                'city' => $paymentInfoObj->city,
                'state' => $paymentInfoObj->region,
                'country' => $paymentInfoObj->country,
                'postal_code' => $paymentInfoObj->postal_code,
            ];

            $extraHeaders = [
                'PROFILEID: '.$profileId,
                'SERVERKEY: '.$serverKey,
            ];

            $paymentObj = new \PaymentHelper();        
            $result = $paymentObj->hostedPayment($dataArr,'/paytabs',$extraHeaders);
            $result = json_decode($result);

            return redirect()->away($result->data->redirect_url);

        }elseif($input['payType'] == 3){// Noon Integration
            $businessId = 'digital_servers';
            $appName = 'whatsloop';
            $appKey = '085f038ec4214c88a507341ac05ad432'; //For Test
            $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDowODVmMDM4ZWM0MjE0Yzg4YTUwNzM0MWFjMDVhZDQzMg=='; // For Test
            // $appKey = 'a91fcf2c6adf4eddace3f15a41705743';
            // $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDphOTFmY2YyYzZhZGY0ZWRkYWNlM2YxNWE0MTcwNTc0Mw==';
            $dataArr = [
                'returnURL' => \URL::to('/pushInvoice'),
                'cart_id' => 'whatsloop-'.$userObj->id,
                'cart_amount' => $totals,
                'cart_description' => 'New Membership',
                'paypage_lang' => LANGUAGE_PREF,
            ];

            $extraHeaders = [
                'BUSINESSID: '.$businessId,
                'APPNAME: '.$appName,
                'APPKEY: '.$appKey,
                'AUTHKEY: '.$authKey,
            ];
            $urlSecondSegment = '/noon';
            // if($input['payType'] == 5){ // Noon Subscription Integration
            //     $urlSecondSegment = '/noon/subscription';
            //     $dataArr = array_merge($dataArr,[
            //         'subs_name' => 'Whatsloop New Membership',
            //         'subs_valid_till' => date('Y-m-d H:i:s',strtotime()),
            //         'subs_type' => 1,
            //     ]);
            // }   
            $paymentObj = new \PaymentHelper();        
            $result = $paymentObj->hostedPayment($dataArr,$urlSecondSegment,$extraHeaders);
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
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/');
        }
    }

    public function activate($id,$transaction_id = null , $paymentGateaway = null){
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

        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = 0;
        $hasMembership = 0;
        $start_date = $invoiceObj->due_date;

        foreach($cartObj as $key => $one){
            $end_date =  $one['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date));
            if($one['type'] == 'membership'){
                $disableUpdate = 0;
                $hasMembership = 1;
                $dataObj = Membership::getOne($one['data']['id']);
                $userObj->update([
                    'membership_id' => $one['data']['id'],
                    'duration_type' => $one['data']['duration_type'],
                ]);

                $centralUser->update([
                    'membership_id' => $one['data']['id'],
                    'duration_type' => $one['data']['duration_type'],
                ]);
            }else if($one['type'] == 'addon'){
                $dataObj = Addons::getOne($one['data']['id']);
                $addon[] = $one['data']['id'];
                $addonData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $userObj->global_id,
                    'user_id' => $userObj->id,
                    'addon_id' => $one['data']['id'],
                    'status' => 1,
                    'duration_type' => $one['data']['duration_type'],
                    'start_date' => $start_date,
                    'end_date' => $end_date, 
                ];
            }else if($one['type'] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                for ($i = 0; $i < $one['data']['quantity'] ; $i++) {
                    $extraQuotaData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $userObj->global_id,
                        'user_id' => $userObj->id,
                        'extra_quota_id' => $one['data']['id'],
                        'duration_type' => $one['data']['duration_type'],
                        'status' => 1,
                        'start_date' => $start_date,
                        'end_date' => $end_date, 
                    ];
                }
            }

            $price = $dataObj->monthly_price;
            $price_after_vat = $dataObj->monthly_after_vat;
            if($one['data']['duration_type'] == 2){
                $price = $dataObj->annual_price;
                $price_after_vat = $dataObj->annual_after_vat;
            }
            $item = $one;
            $total+= $price_after_vat * $one['data']['quantity'];
            $items[] = $item;
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
        $invoiceObj->paid_date = DATE_TIME;
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->save();

        $disableTransfer = 0;

        foreach($addonData as $oneAddonData){
            $userAddonObj = UserAddon::where([
                ['user_id',$oneAddonData['user_id']],
                ['addon_id',$oneAddonData['addon_id']],
                ['status',2],
            ])->orWhere([
                ['user_id',$oneAddonData['user_id']],
                ['addon_id',$oneAddonData['addon_id']],
                ['end_date','<',date('Y-m-d')],
            ])->first();
            if($userAddonObj){
                $userAddonObj->update($oneAddonData);
                $disableUpdate = 1;
            }else{
                UserAddon::insert($oneAddonData);
                if(!$hasMembership){
                    $disableUpdate = 1;
                }
            }

        }

        foreach($extraQuotaData as $oneItemData){
            $userExtraQuotaObj = UserExtraQuota::where([
                ['user_id',$oneItemData['user_id']],
                ['extra_quota_id',$oneItemData['extra_quota_id']],
                ['status',2],
            ])->orWhere([
                ['user_id',$oneItemData['user_id']],
                ['extra_quota_id',$oneItemData['extra_quota_id']],
                ['end_date','<',date('Y-m-d')],
            ])->first();
            if($userExtraQuotaObj){
                $userExtraQuotaObj->update($oneItemData);
                $disableUpdate = 1;
            }else{
                UserExtraQuota::insert($oneItemData);
                if(!$hasMembership){
                    $disableUpdate = 1;
                }
            }
        }

        $mainUserChannel = UserChannels::first();
        $channelObj = CentralChannel::first();

        if($mainUserChannel->end_date > date('Y-m-d')){
            $disableTransfer = 1;
        }

        if(!$disableUpdate){
            $mainUserChannel->start_date = $start_date;
            $mainUserChannel->end_date = $end_date;
            $mainUserChannel->save();

            CentralChannel::where('id',$mainUserChannel->id)->update([
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);
        }

        $channel = [
            'id' => $mainUserChannel->id,
            'token' => $mainUserChannel->token,
            'name' => 'Channel #'.$mainUserChannel->id,
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];
  
        if(!$disableTransfer){
            $transferDaysData = [
                'receiver' => $channel['id'],
                'days' => 3, // 3
                'source' => $channelObj->id,
            ];

            $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
            $result = $updateResult->json();
        }

        $userObj->update([
            'channels' => serialize([$mainUserChannel->id]),
        ]);

        $centralUser->update([
            'channels' => serialize([$mainUserChannel->id]),
        ]);

        if(!empty($addon) && in_array(4,$addon)){
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

        if(!empty($addon) && in_array(5,$addon)){
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
