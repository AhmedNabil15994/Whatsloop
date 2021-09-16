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
        if($status == 3){
            if($oldStatus !== 2){
                $transferObj->status = $status;
            }
        }elseif($status == 2){
            $beginProcess = 1;
            $transferObj->status = $status;
        }

        $transferObj->updated_at = DATE_TIME;
        $transferObj->updated_by = USER_ID;
        $transferObj->save();

        if($beginProcess){

            $tenant = Tenant::find($transferObj->tenant_id);
            tenancy()->initialize($tenant);
            $cartObj = Variable::getVar('cartObj');
            $cartObj = json_decode(json_decode($cartObj));
            $userObj = User::first();

            tenancy()->end($tenant);

            $centralUser = CentralUser::find($userObj->id);
            $tenant_id = $transferObj->tenant_id;

            tenancy()->initialize($tenant);
            $userCreditsObj = Variable::getVar('userCredits');
            tenancy()->end($tenant);

            $start_date = date('Y-m-d');
            $userCredits = 0;
            if($userCreditsObj){
                tenancy()->initialize($tenant);
                $start_date = Variable::getVar('start_date');
                tenancy()->end($tenant);
                $userCredits = $userCreditsObj;
            } 
            // dd($cartObj);

            $items = [];
            $addons = [];
            $addonData = [];
            $extraQuotaData = [];
            $total = 0;

            $hasMembership = 0;
            foreach($cartObj as $key => $one){
                if($one[1] == 'membership'){
                    $disableUpdate = 0;
                    $hasMembership = 1;
                    $dataObj = Membership::getOne($one[0]);
                    $userObj->update([
                        'membership_id' => $one[0],
                        'duration_type' => $one[3],
                    ]);

                    $centralUser->update([
                        'membership_id' => $one[0],
                        'duration_type' => $one[3],
                    ]);

                    $end_date =  $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date));
                }else if($one[1] == 'addon'){
                    $dataObj = Addons::getOne($one[0]);
                    $addon[] = $one[0];
                    $addonData[] = [
                        'tenant_id' => $tenant_id,
                        'global_user_id' => $userObj->global_id,
                        'user_id' => $userObj->id,
                        'addon_id' => $one[0],
                        'status' => 1,
                        'duration_type' => $one[3],
                        'start_date' => $start_date,
                        'end_date' => $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date)), 
                    ];
                    $end_date = $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date));
                }else if($one[1] == 'extra_quota'){
                    $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                    for ($i = 0; $i < $one[7] ; $i++) {
                        $extraQuotaData[] = [
                            'tenant_id' => $tenant_id,
                            'global_user_id' => $userObj->global_id,
                            'user_id' => $userObj->id,
                            'extra_quota_id' => $one[0],
                            'duration_type' => $one[3],
                            'status' => 1,
                            'start_date' => $start_date,
                            'end_date' => $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date)), 
                        ];
                        $end_date = $one[3] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year'),strtotime($start_date));
                    }
                }
                $price = $dataObj->monthly_price;
                $price_after_vat = $dataObj->monthly_after_vat;
                if($one[3] == 2){
                    $price = $dataObj->annual_price;
                    $price_after_vat = $dataObj->annual_after_vat;
                }
                $item = [
                    'type' => $one[1],
                    'data' => [
                        'id' => $one[0],
                        'title_ar' => ($one[1] != 'extra_quota' ? $dataObj->title_ar : $dataObj->extra_count . ' '.$dataObj->extraTypeText . ' ' . ($dataObj->extra_type == 1 ? trans('main.msgPerDay') : '') ),
                        'title_en' => ($one[1] != 'extra_quota' ? $dataObj->title_en : $dataObj->extra_count . ' '.$dataObj->extraTypeText . ' ' . ($dataObj->extra_type == 1 ? trans('main.msgPerDay') : '') ),
                        'price' => $price,
                        'price_after_vat' => $price_after_vat,
                        'duration_type' => $one[3],
                        'quantity' => $one[7],
                    ],
                ];
                $total+= $price_after_vat * $one[7];
                $items[] = $item;
            }

            if(!empty($addon)){
                tenancy()->initialize($tenant);
                $userObj->update([
                    'addons' =>  serialize($addon),
                ]);
                tenancy()->end($tenant);

                $centralUser->update([
                    'addons' =>  serialize($addon),
                ]);
            }

            $invoiceObj = new Invoice;
            $invoiceObj->client_id = $userObj->id;
            $invoiceObj->transaction_id = $transferObj->order_no;
            $invoiceObj->payment_gateaway = trans('main.bankTransfer');  
            $invoiceObj->total = $total - $userCredits ;
            $invoiceObj->due_date = $start_date;
            $invoiceObj->paid_date = DATE_TIME;
            $invoiceObj->items = serialize($items);
            $invoiceObj->status = 1;
            $invoiceObj->payment_method = 2;
            $invoiceObj->sort = Invoice::newSortIndex();
            $invoiceObj->created_at = DATE_TIME;
            $invoiceObj->created_by = $userObj->id;
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

            tenancy()->initialize($tenant);
            $mainUserChannel = UserChannels::first();
            tenancy()->end($tenant);

            $channelObj = CentralChannel::first();
            if(!$mainUserChannel){
                $mainWhatsLoopObj = new \MainWhatsLoop($channelObj->id,$channelObj->token);
                $updateResult = $mainWhatsLoopObj->createChannel();
                $result = $updateResult->json();

            
                if($result['status']['status'] != 1){
                    \Session::flash('error', $result['status']['message']);
                    return back()->withInput();
                }

                $channel = [
                    'id' => $result['data']['channel']['id'],
                    'token' => $result['data']['channel']['token'],
                    'name' => 'Channel #'.$result['data']['channel']['id'],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                ];

                $extraChannelData = $channel;
                $extraChannelData['tenant_id'] = $tenant_id;
                $extraChannelData['global_user_id'] = $userObj->global_id;
                $generatedData = CentralChannel::generateNewKey($result['data']['channel']['id']); // [ generated Key , generated Token]
                $extraChannelData['instanceId'] = $generatedData[0];
                $extraChannelData['instanceToken'] = $generatedData[1];

                CentralChannel::create($extraChannelData);
                tenancy()->initialize($tenant);
                UserChannels::create($channel);
                tenancy()->end($tenant);
            }else{

                if($mainUserChannel->end_date > date('Y-m-d')){
                    $disableTransfer = 1;
                }

                if(!$disableUpdate){
                    tenancy()->initialize($tenant);
                    $mainUserChannel->start_date = $start_date;
                    $mainUserChannel->end_date = $end_date;
                    $mainUserChannel->save();
                    tenancy()->end($tenant);

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
            }
        
            if(!$disableTransfer){
                $transferDaysData = [
                    'receiver' => $channel['id'],
                    'days' => 1, // 3
                    'source' => $channelObj->id,
                ];

                $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
                $result = $updateResult->json();
            }
            
            tenancy()->initialize($tenant);
            $userObj->update([
                'channels' => serialize([$channel['id']]),
            ]);
            tenancy()->end($tenant);

            $centralUser->update([
                'channels' => serialize([$channel['id']]),
            ]);

            if(!empty($addon) && in_array(4,$addon)){
                tenancy()->initialize($tenant);
                $varObj = Variable::where('var_key','ZidURL')->first();
                if(!$varObj){
                    Variable::insert([
                        [
                            'var_key' => 'ZidURL',
                            'var_value' => 'https://api.zid.sa/v1',
                        ],
                    ]);
                }
                tenancy()->end($tenant);
            }

            if(!empty($addon) && in_array(5,$addon)){
                tenancy()->initialize($tenant);
                $varObj = Variable::where('var_key','SallaURL')->first();
                if(!$varObj){
                    Variable::insert([
                        [
                            'var_key' => 'SallaURL',
                            'var_value' => 'https://api.salla.dev/admin/v2',
                        ],
                    ]);
                }
                tenancy()->end($tenant);
            }

            $transferObj->invoice_id = $invoiceObj->id;
            $transferObj->save();
        }

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
        $dataObj = CentralDepartment::getOne($id);
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
