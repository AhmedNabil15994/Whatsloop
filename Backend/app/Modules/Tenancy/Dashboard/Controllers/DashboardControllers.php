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
use App\Models\FAQ;
use App\Models\CentralVariable;
use App\Models\Changelog;
use App\Models\CentralCategory;
use App\Models\Department;
use App\Models\Rate;
use App\Models\ModTemplate;

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
                    // $data['qrImage'] = \URL::to('/').'/engine/public'.$image;
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
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }
        
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
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

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
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        Session::put('data',$testData);
        Session::put('totals',$data['totals']);
        return view('Tenancy.Dashboard.Views.dashboard4')->with('data',(object) $data);
    }

    public function completeOrder(){
        $input = \Request::all();
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

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
                'returnURL' => \URL::to('/pushInvoice'),
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

        }elseif($input['payType'] == 3 || $input['payType'] == 4){// Noon Integration
            $businessId = 'digital_servers';
            $appName = 'whatsloop';
            // $appKey = '085f038ec4214c88a507341ac05ad432'; //For Test
            $appKey = 'a91fcf2c6adf4eddace3f15a41705743';
            // $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDowODVmMDM4ZWM0MjE0Yzg4YTUwNzM0MWFjMDVhZDQzMg=='; // For Test
            $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDphOTFmY2YyYzZhZGY0ZWRkYWNlM2YxNWE0MTcwNTc0Mw==';
            $dataArr = [
                'returnURL' => str_replace('http:','https:',\URL::to('/pushInvoice')),
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
            if($input['payType'] == 4){ // Noon Subscription Integration
                $urlSecondSegment = '/noon/subscription';
                $dataArr = array_merge($dataArr,[
                    'subs_name' => 'Whatsloop New Membership',
                    'subs_valid_till' => date('Y-m-d H:i:s',strtotime()),
                    'subs_type' => 1,
                ]);
            }   
            $paymentObj = new \PaymentHelper();        
            $result = $paymentObj->hostedPayment($dataArr,$urlSecondSegment,$extraHeaders);
            $result = json_decode($result);
            return redirect()->away($result->data->result->redirect_url);
        }
        
        // dd($input['payType']);
        // $invoiceData = [
        //     'title' => $userObj->name,
        //     'cc_first_name' => $names[0],
        //     'cc_last_name' => isset($names[1]) ? $names[1] : '',
        //     'email' => $userObj->email,
        //     'cc_phone_number' => '',
        //     'phone_number' => $userObj->phone,
        //     'products_per_title' => 'New Membership',
        //     'reference_no' => 'whatsloop-'.$userObj->id,
        //     'unit_price' => $totals,
        //     'quantity' => 1,
        //     'amount' => $totals,
        //     'other_charges' => 'VAT',
        //     'discount' => '',
        //     'payment_type' => 'mastercard',
        //     'OrderID' => 'whatsloop-'.$userObj->id,
        //     'SiteReturnURL' => \URL::to('/pushInvoice'),
        // ];

        // $paymentObj = new \PaymentHelper();        
        // return $paymentObj->RedirectWithPostForm($invoiceData);
    }

    public function pushInvoice(){
        $input = \Request::all();
        $data['data'] = json_decode($input['data']);
        $data['status'] = json_decode($input['status']);
        // dd($data);

        if($data['status']->status == 1){
            return $this->activate();
        }else{
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/');
        }
        
        // dd($data);
        // if (isset($input['cartId']) && !empty($input['cartId'])) {
        //     $postData['OrderID'] = $input['cartId'];
        //     $paymentObj = new \PaymentHelper();        
        //     $createPayment = $paymentObj->OpenURLWithPost($postData);
        //     $CreateaPage = json_decode($createPayment, TRUE);
        
        //     if ($CreateaPage['Code'] == "1001") {
        //         if ($CreateaPage['Data']['Status'] == "Success") {
        //             return $this->activate();
        //         }
        //         $UpdateOrder = [];
        //         if ($CreateaPage['Data']['Status'] == "Rejected") {
        //             $UpdateOrder['Status'] = "تم رفض العملية";
        //         }
        //         if ($CreateaPage['Data']['Status'] == "Canceled") {
        //             $UpdateOrder['Status'] = "تم الالغاء";
        //         }
        //         if ($CreateaPage['Data']['Status'] == "Expired Card") {
        //             $UpdateOrder['Status'] = "البطاقة المستخدمة منتهية";
        //         }
        //         \Session::flash('error',$UpdateOrder['Status']);
        //         return redirect()->to('/');
        //     }else{
        //         \Session::flash('error','حدثت مشكلة في عملية الدفع');
        //         return redirect()->to('/dashboard');
        //     }
        // }
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
                    'status' => 1,
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
                    'status' => 1,
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

        $mainUserChannel = UserChannels::first();
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
            UserChannels::create($channel);
        }else{

            $mainUserChannel->start_date = $start_date;
            $mainUserChannel->end_date = $end_date;
            $mainUserChannel->save();

            CentralChannel::where('id',$mainUserChannel->id)->update([
                'start_date' => $start_date,
                'end_date' => $end_date
            ]);

            $channel = [
                'id' => $mainUserChannel->id,
                'token' => $mainUserChannel->token,
                'name' => 'Channel #'.$mainUserChannel->id,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ];
        }
        

        // $transferDaysData = [
        //     'receiver' => $channel['id'],
        //     'days' => 3,
        //     'source' => $channelObj->id,
        // ];

        // $updateResult = $mainWhatsLoopObj->transferDays($transferDaysData);
        // $result = $updateResult->json();

        $userObj->update([
            'channels' => serialize([$channel['id']]),
        ]);

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
        ]);

        if(in_array(4,$addon)){
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

        if(in_array(5,$addon)){
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

    public function faqs(){   
        $data = FAQ::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.faqs')->with('data',(object) $data);
    }

    public function helpCenter(){   
        $data = FAQ::dataList(1);
        $data['changeLogs'] = Changelog::dataList(1)['data'];
        $data['categories'] = CentralCategory::dataList(1)['data'];
        $data['email'] = CentralVariable::getVar('TECH_EMAIL');
        $data['phone'] = CentralVariable::getVar('TECH_PHONE');
        $data['pin_code'] = $this->genNewPinCode(USER_ID);
        $data['clients'] = CentralUser::NotDeleted()->where('status',1)->where('global_id',GLOBAL_ID)->where('group_id',0)->get();
        $data['departments'] = Department::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.helpCenter')->with('data',(object) $data);
    }

    public function genNewPinCode($user_id){
        $newCode = rand(1,10000);
        $userObj = User::getOne($user_id);
        $userObj->pin_code = $newCode;
        $userObj->save();

        $userObj = CentralUser::getOne($user_id);
        $userObj->pin_code = $newCode;
        $userObj->save();
        return $newCode;
    }

    public function addRate(){
        $input = \Request::all();
        $rateObj = new Rate();
        $rateObj->user_id = USER_ID;
        $rateObj->tenant_id = TENANT_ID;
        $rateObj->changelog_id = (int) $input['id'];
        $rateObj->comment = (string) $input['comment'];
        $rateObj->rate = (int) $input['rate'];
        $rateObj->created_by = USER_ID;
        $rateObj->created_at = DATE_TIME;
        $rateObj->save();

        WebActions::newType(1,'Rate');
        return \TraitsFunc::SuccessResponse(trans('main.addSuccess'));
    }

    public function qrIndex(){
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
                    $data['qrImage'] = \URL::to('/').$image;
                }
            }
        }

        if(!isset($data['qrImage']) || $data['qrImage'] == null){
            return redirect('dashboard');
        }
        $userAddonsTutorial = [];
        $userAddons = array_unique(Session::get('addons'));
        $addonsTutorial = [1,2,4,5];
        for ($i = 0; $i < count($addonsTutorial) ; $i++) {
            if(in_array($addonsTutorial[$i],$userAddons)){
                $checkData = Variable::getVar('MODULE_'.$addonsTutorial[$i]);
                if($checkData == ''){
                    $varObj = new Variable;
                    $varObj->var_key = 'MODULE_'.$addonsTutorial[$i];
                    $varObj->var_value = 0;
                    $varObj->save();
                    $userAddonsTutorial[] = $addonsTutorial[$i];
                }elseif($checkData == 0){
                    $userAddonsTutorial[] = $addonsTutorial[$i];
                }
            }
        }

        $data['data'] = array_values($userAddonsTutorial);
        $names = Addons::NotDeleted()->whereIn('id',$data['data'])->pluck('title_'.LANGUAGE_PREF);
        $data['dataNames'] = reset($names);
        $data['channelName'] = UserChannels::first()->name;
        $data['dis'] = 0;
        if(count($data['data']) > 0){
            $data['templates'] = ModTemplate::dataList(null, ($data['data'][0] == 5 ? 1 : 2 )  )['data'];
        }else{
            $data['dis'] = 1;
        }
        return view('Tenancy.Dashboard.Views.qrData')->with('data',(object) $data);
    }

    public function updateName(){
        $input = \Request::all();
        if(!isset($input['name']) || empty($input['name'])){
            return \TraitsFunc::ErrorMessage(trans('main.channelNameValidate'));
        }

        // $mainWhatsLoopObj = new \MainWhatsLoop();
        // $result = $mainWhatsLoopObj->setName(['name' => $input['name']]);
        // $result = $result->json();

        // if($result['status']['status'] != 1){
        //     return \TraitsFunc::ErrorMessage($result['status']['message']);
        // }

        $channelObj =  UserChannels::first();
        $channelObj->name = $input['name'];
        $channelObj->save();
        CentralChannel::where('id',$channelObj->id)->update(['name' => $input['name']]);
        
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function getQR(){
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
                    $statusObj['data']['qrImage'] = \URL::to('/').$image;
                    $statusObj['status'] = \TraitsFunc::SuccessMessage();
                    return \Response::json((object) $statusObj);
                }
            }
        }
        
    }

    public function editTemplate(){
        $input = \Request::all();
        $rules = [
            'id' => 'required',
            'status' => 'required',
        ];

        $message = [
            'id.required' => '',
            'status.required' => '',
        ];

        $validate = \Validator::make($input, $rules, $message);
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $id = (int) $input['id'];
        $status = (int) $input['status'];

        $templateObj = ModTemplate::getOne($id);
        $templateObj->status = $status;
        $templateObj->save();
        
        return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
    }

    public function finishModID($modID){
        $modID = (int) $modID;
        Variable::where('var_key','MODULE_'.$modID)->update(['var_value'=> 1]);
        return redirect()->to('/QR');
    }
}
