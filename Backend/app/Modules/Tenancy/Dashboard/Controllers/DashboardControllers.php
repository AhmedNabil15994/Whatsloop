<?php namespace App\Http\Controllers;


use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\WebActions;
use App\Models\Group;
use App\Models\Membership;
use App\Models\User;
use App\Models\UserTheme;
use App\Models\UserAddon;
use App\Models\UserExtraQuota;
use App\Models\Addons;
use App\Models\ExtraQuota;
use App\Models\Variable;
use App\Models\Invoice;
use App\Models\CentralUser;
use App\Models\CentralChannel;
use App\Models\PaymentInfo;
use App\Models\UserChannels;

class DashboardControllers extends Controller {

    use \TraitsFunc;

    public function Dashboard(){   
        $input = \Request::all();

        if(!Session::has('membership') || Session::get('membership') == null){
            $data['memberships'] = Membership::dataList(1)['data'];
            return view('Tenancy.Dashboard.Views.dashboard2')->with('data',(object) $data);
        }

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $result = $mainWhatsLoopObj->status();
        $result = $result->json();
        if(isset($result['data'])){
            if($result['data']['accountStatus'] == 'got qr code'){
                if(isset($result['data']['qrCode'])){
                    $image = '/uploads/instanceImage' . time() . '.png';
                    $destinationPath = public_path() . $image;
                    $qrCode =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $result['data']['qrCode']));
                    $succ = file_put_contents($destinationPath, $qrCode);   
                    // $data['qrImage'] = \URL::to('/public'.$image);
                    $data['qrImage'] = \URL::to('/').$image;
                }
            }
        }
        Session::forget('check_user_id');
        $now = date('Y-m-d');
        $start = $now;
        $end = $now;
        $date = null;
        if(isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])){
            $start = $input['from'].' 00:00:00';
            $end = $input['to'].' 23:59:59';
            $date = 1;
        }
        // $contactUs = ContactUs::getByDate($date,$start,$end);

        $data['contactUs'] = [];//$contactUs['data'];
        $data['contactUsCount'] = 0;//$contactUs['count'];
        $data['webActions'] =  [];
        $data['webActionsCount'] =  0;
        $data['chartData1'] = [];//$this->getChartData($start,$end,'\ContactUs');
        $data['chartData2'] = $this->getChartData($start,$end,'\User');
        $data['addCount'] = 0;
        $data['editCount'] = 0;
        $data['deleteCount'] = 0;
        $data['fastEditCount'] = 0;
        return view('Tenancy.Dashboard.Views.dashboard')->with('data',(object) $data);
    }

    public function getChartData($start=null,$end=null,$moduleName){
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
        $module = "\App\Models".$moduleName;
        for($i=0;$i<$dataCount;$i++){
            if($dataCount == 1){
                $count = $module::where('created_at','>=',$datesArray[0].' 00:00:00')->where('created_at','<=',$datesArray[0].' 23:59:59')->count();
            }else{
                if($i < count($datesArray)){
                    $count = $module::where('created_at','>=',$datesArray[$i].' 00:00:00')->where('created_at','<=',$datesArray[$i].' 23:59:59')->count();
                }
            }
            $chartData[0][$i] = $datesArray[$i];
            $chartData[1][$i] = $count;
        }
        return $chartData;
    }

    public function changeChannel(Request $request){
        if($request->ajax()){
            $userObj = User::getData(User::getOne(USER_ID));
            if(!Session::has('channel')){
                if(in_array($request->channel, $userObj->channelIDS)){
                    Session::put('channel', $request->channel);
                }
            }else{
                Session::forget('channel');
                if(in_array($request->channel, $userObj->channelIDS)){
                    Session::put('channel', $request->channel);
                }
            } 
            return Redirect::back();
        }
    }

    public function changeTheme(Request $request){
        if($request->ajax()){
            $type = $request->type;
            $value = $request->value;
            $dataObj = UserTheme::where('user_id',USER_ID)->first();
            if(!$dataObj){
                $dataObj = new UserTheme;
            }
            $dataObj->user_id = USER_ID;
            $dataObj->$type = $value;
            $dataObj->save();
            return Redirect::back();
        }
    }

    public function changeThemeToDefault(Request $request){
        if($request->ajax()){
            $type = $request->type;
            $value = $request->value;
            $dataObj = UserTheme::where('user_id',USER_ID)->first();
            $dataObj->theme = 'light';
            $dataObj->width = 'fluid';
            $dataObj->menus_position = 'fixed';
            $dataObj->sidebar_size = 'default';
            $dataObj->user_info = 'false';
            $dataObj->top_bar = 'dark';
            $dataObj->save();
            return Redirect::back();
        }
    }

    public function checkout(){
        $input = \Request::all();
        
        if(!isset($input['membership_id']) || empty($input['membership_id'])){
            Session::flash('error',trans('main.membershipValidate'));
            return redirect()->to('/dashboard');
        }
        
        $membershipObj = Membership::getOne($input['membership_id']);
        if(!$membershipObj || $membershipObj->id == 4){
            Session::flash('error',trans('main.membershipValidate'));
            return redirect()->to('/dashboard');
        }
        $data['addons'] = Addons::dataList(1)['data'];
        $data['membership'] = Membership::getData($membershipObj);
        $data['extraQuotas'] = ExtraQuota::dataList()['data'];
        $data['memberships'] = Membership::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.dashboard3')->with('data',(object) $data);
    }

    public function postCheckout(){
        $input = \Request::all();
        // dd($input);
        $myData   = json_decode($input['data']);
        $testData = [];
        $total = 0;
        foreach($myData as $key => $one){
            $testData[$key] = $one;
            if($one[1] == 'membership'){
                $dataObj = Membership::getOne($one[0]);
                $title = $dataObj->{'title_'.LANGUAGE_PREF};
            }else if($one[1] == 'addon'){
                $dataObj = Addons::getOne($one[0]);
                $title = $dataObj->{'title_'.LANGUAGE_PREF};
            }else if($one[1] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                $title = $dataObj->extra_count . ' '.$dataObj->extraTypeText . ' ' . ($dataObj->extra_type == 1 ? trans('main.msgPerDay') : '');
            }
            $testData[$key][2] = $title;
            $testData[$key][6] = $one[3] == 1 ? $dataObj->monthly_after_vat : $dataObj->annual_after_vat;
            $total+= $testData[$key][6];
        }
        
        $data['data'] = $testData;
        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;
        $data['totals'] = $input['totals'];

        Session::put('data',$testData);
        Session::put('totals',$data['totals']);
        return view('Tenancy.Dashboard.Views.dashboard4')->with('data',(object) $data);
    }

    public function completeOrder(){
        $input = \Request::all();
        $totals = Session::get('totals')[3];
        $cartData = Session::get('data');

        $cartObj = Variable::where('var_key','cartObj')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }
        $cartObj->var_key = 'cartObj';
        $cartObj->var_value = json_encode($cartData);
        $cartObj->save();
        
        $userObj = User::first();

        $paymentInfoObj = new PaymentInfo;
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
            'unit_price' => $totals,
            'quantity' => 1,
            'amount' => $totals,
            'other_charges' => 'VAT',
            'discount' => '',
            'payment_type' => 'mastercard',
            'OrderID' => 'whatsloop-'.$userObj->id,
            'SiteReturnURL' => \URL::to('/pushInvoice'),
        ];

        $paymentObj = new \PaymentHelper();        
        return $paymentObj->RedirectWithPostForm($invoiceData);
    }

    public function pushInvoice(){
        $input = \Request::all();
        return $this->activate();
        
        // dd($input);
        if (isset($input['cartId']) && !empty($input['cartId'])) {
            $postData['OrderID'] = $input['cartId'];
            $paymentObj = new \PaymentHelper();        
            $createPayment = $paymentObj->OpenURLWithPost($postData);
            $CreateaPage = json_decode($createPayment, TRUE);
        
            if ($CreateaPage['Code'] == "1001") {
                if ($CreateaPage['Data']['Status'] == "Success") {
                    $this->activate();
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

    public function activate(){
        $cartObj = Variable::getVar('cartObj');
        $cartObj = json_decode($cartObj);
        $userObj = User::first();
        $centralUser = CentralUser::find($userObj->id);
        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
        $tenant_id = $tenantObj->tenant_id;
        // dd($tenantObj);
        $items = [];
        $addons = [];
        $addonData = [];
        $extraQuotaData = [];
        $total = 0;
        $start_date = date('Y-m-d');

        foreach($cartObj as $key => $one){
            if($one[1] == 'membership'){
                $dataObj = Membership::getOne($one[0]);
                $userObj->update([
                    'membership_id' => $one[0],
                    'duration_type' => $one[3],
                ]);

                $centralUser->update([
                    'membership_id' => $one[0],
                    'duration_type' => $one[3],
                ]);

                $end_date =  $one[3] == 1 ? date('Y-m-d',strtotime('+1 month')) : date('Y-m-d',strtotime('+1 year'));
            }else if($one[1] == 'addon'){
                $dataObj = Addons::getOne($one[0]);
                $addon[] = $one[0];
                $addonData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $userObj->global_id,
                    'user_id' => $userObj->id,
                    'addon_id' => $one[0],
                    'duration_type' => $one[3],
                    'start_date' => date('Y-m-d'),
                    'end_date' => $one[3] == 1 ? date('Y-m-d',strtotime('+1 month')) : date('Y-m-d',strtotime('+1 year')), 
                ];

            }else if($one[1] == 'extra_quota'){
                $dataObj = ExtraQuota::getData(ExtraQuota::getOne($one[0]));
                $extraQuotaData[] = [
                    'tenant_id' => $tenant_id,
                    'global_user_id' => $userObj->global_id,
                    'user_id' => $userObj->id,
                    'extra_quota_id' => $one[0],
                    'duration_type' => $one[3],
                    'start_date' => date('Y-m-d'),
                    'end_date' => $one[3] == 1 ? date('Y-m-d',strtotime('+1 month')) : date('Y-m-d',strtotime('+1 year')), 
                ];
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
                ],
            ];
            $total+= $price_after_vat;
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

        $invoiceObj = new Invoice;
        $invoiceObj->client_id = $userObj->id;
        $invoiceObj->total = $total;
        $invoiceObj->due_date = date('Y-m-d');
        $invoiceObj->items = serialize($items);
        $invoiceObj->status = 1;
        $invoiceObj->payment_method = 2;
        $invoiceObj->sort = Invoice::newSortIndex();
        $invoiceObj->created_at = DATE_TIME;
        $invoiceObj->created_by = $userObj->id;
        $invoiceObj->save();

        UserAddon::insert($addonData);
        UserExtraQuota::insert($extraQuotaData);


        $channelObj = CentralChannel::first();
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

        CentralChannel::create($extraChannelData);
        UserChannels::create($channel);

        $transferDaysData = [
            'receiver' => $channel['id'],
            'days' => 3,
            'source' => $channelObj->id,
        ];

        $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        $result = $updateResult->json();

        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if(in_array(4,$addon)){
            Variable::insert([
                [
                    'var_key' => 'ZidURL',
                    'var_value' => 'https://api.zid.sa/v1',
                ],
            ]);
        }

        if(in_array(5,$addon)){
            Variable::insert([
                [
                    'var_key' => 'SallaURL',
                    'var_value' => 'https://api.salla.dev/admin/v2',
                ],
            ]);
        }


        // // Update User With Settings For Whatsapp Based On His Domain
        // $myData = [
        //     'sendDelay' => '0',
        //     'webhookUrl' => str_replace('://', '://'.$userObj->domain.'.', \URL::to('/')).'/whatsloop/webhooks/messages-webhook',
        //     'instanceStatuses' => 1,
        //     'webhookStatuses' => 1,
        //     'statusNotificationsOn' => 1,
        //     'ackNotificationsOn' => 1,
        //     'chatUpdateOn' => 1,
        //     'videoUploadOn' => 1,
        //     'guaranteedHooks' => 1,
        //     'parallelHooks' => 1,
        // ];
        // $updateResult = $mainWhatsLoopObj->setSettings($channel['id'],$channel['token'],$myData);
        // $result = $updateResult->json();
        // if($result['status']['status'] != 1){
        //     \Session::flash('error', $result['status']['message']);
        //     return back()->withInput();
        // }


        // dd($cartObj);
        Session::forget('user_id');
        return redirect()->to('/dashboard');
    }

}
