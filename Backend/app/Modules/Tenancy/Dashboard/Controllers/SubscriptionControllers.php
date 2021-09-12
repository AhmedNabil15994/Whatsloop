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
use App\Models\Bundle;

class SubscriptionControllers extends Controller {

    use \TraitsFunc;

    public function packages(){   
        $input = \Request::all();
        // $data['memberships'] = Membership::dataList(1)['data'];
        $data['bundles'] = Bundle::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.packages')->with('data',(object) $data);
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
        $data['extraQuotas'] = ExtraQuota::dataList(1)['data'];

        $data['membership'] = Membership::getData($membershipObj);
        $data['memberships'] = Membership::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.cart')->with('data',(object) $data);
    }

    public function postBundle($id){
        $id = (int) $id;
        $input = \Request::all();
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $bundleObj = Bundle::getOne($id);
        if(!$bundleObj){
            return redirect(404);
        }
        $membershipObj = Membership::getData($bundleObj->Membership);
        $addons = [];
        if($bundleObj->addons != null){
            $addons = Addons::dataList(1,unserialize($bundleObj->addons))['data'];
        }

        $testData = [];
        $total = $bundleObj->monthly_after_vat;
        $start_date = date('Y-m-d');

        $testData[] = [
            $membershipObj->id,
            'membership',
            $membershipObj->title,
            1,
            $start_date,
            date('Y-m-d',strtotime('+1 month',strtotime($start_date))),
            $membershipObj->monthly_after_vat,
            1,
        ];

        foreach($addons as $addon){
            $testData[] = [
                $addon->id,
                'addon',
                $addon->title,
                1,
                $start_date,
                date('Y-m-d',strtotime('+1 month',strtotime($start_date))),
                $addon->monthly_after_vat,
                1,
            ];
        }

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
            $total+= $testData[$key][6] * (int)$testData[$key][7];
        }
        
        $data['data'] = $testData;
        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;
        $data['totals'] = $input['totals'];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        return view('Tenancy.Dashboard.Views.checkout')->with('data',(object) $data);
    }

    public function completeOrder(){
        $input = \Request::all();
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $total = json_decode($input['totals']);
        $totals = $total[3];
        $cartData = $input['data'];
        $cartObj = Variable::where('var_key','cartObj')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }

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

        }elseif($input['payType'] == 4 || $input['payType'] == 5 || $input['payType'] == 6){// Noon Integration
            $businessId = 'digital_servers';
            $appName = 'whatsloop';
            // $appKey = '085f038ec4214c88a507341ac05ad432'; //For Test
            $appKey = 'a91fcf2c6adf4eddace3f15a41705743';
            // $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDowODVmMDM4ZWM0MjE0Yzg4YTUwNzM0MWFjMDVhZDQzMg=='; // For Test
            $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDphOTFmY2YyYzZhZGY0ZWRkYWNlM2YxNWE0MTcwNTc0Mw==';
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
            if($input['payType'] == 5){ // Noon Subscription Integration
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
    }

    public function pushInvoice(){
        $input = \Request::all();
        $data['data'] = json_decode($input['data']);
        $data['status'] = json_decode($input['status']);

        if($data['status']->status == 1){
            return $this->activate($data['data']->transaction_id,$data['data']->paymentGateaway);
        }else{
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/');
        }
    }

    public function activate($transaction_id = null , $paymentGateaway = null){
        $cartObj = Variable::getVar('cartObj');
        $cartObj = json_decode(json_decode($cartObj));
        $userObj = User::first();
        $centralUser = CentralUser::find($userObj->id);
        $tenantObj = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first();
        $tenant_id = $tenantObj->tenant_id;
        $userCreditsObj = Variable::getVar('userCredits');
        $start_date = date('Y-m-d');
        $userCredits = 0;
        if($userCreditsObj){
            $start_date = Variable::getVar('start_date');
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
            $userObj->update([
                'addons' =>  serialize($addon),
            ]);

            $centralUser->update([
                'addons' =>  serialize($addon),
            ]);
        }

        $invoiceObj = new Invoice;
        $invoiceObj->client_id = $userObj->id;
        $invoiceObj->transaction_id = $transaction_id;
        $invoiceObj->payment_gateaway = $paymentGateaway;  
        $invoiceObj->total = $total - $userCredits ;
        $invoiceObj->due_date = $start_date;
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
        }
        

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
            'channels' => serialize([$channel['id']]),
        ]);

        $centralUser->update([
            'channels' => serialize([$channel['id']]),
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
        Session::flush();
        User::setSessions($userObj);
        return redirect()->to('/dashboard');
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

    public function updateSubscription(){
        $input = \Request::all();
        Session::forget('userCredits');
        if( (!isset($input['type']) || empty($input['type'])) || !in_array($input['type'], ['membership','addon','extra_quota'])){
            return redirect(404);    
        }

        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $dataObj = null;
        $data['memberships'] = [];
        $data['addons'] = [];
        $data['extraQuotas'] = [];

        if($input['type'] == 'membership'){
            $dataObj = Membership::getOne(Session::get('membership'));
            if(!$dataObj || $dataObj->id == 4){
                Session::flash('error',trans('main.membershipValidate'));
                return redirect()->to('/dashboard');
            }

            $userChannelObj = UserChannels::getUserChannels()['data'][0];
            $usedDays = $userChannelObj->usedDays;
            $oldDuration = User::first()->duration_type;
            $newPriceAfterVat = $dataObj->monthly_after_vat;

            if($oldDuration == 1){
                $usedCost = floor(($dataObj->monthly_after_vat / 30) * $usedDays);
            }else if($oldDuration == 2){
                $usedCost = floor(($dataObj->annual_after_vat / 365) * $usedDays);
                $newPriceAfterVat = $dataObj->annual_after_vat;
            }

            $data['userCredits'] = $newPriceAfterVat - $usedCost;
            Session::put('userCredits',$data['userCredits']);
            $data['membership'] = Membership::getData($dataObj);
            $data['memberships'] = Membership::dataList(1)['data'];
            $data['start_date'] = $userChannelObj->start_date;
        }else if($input['type'] == 'addon'){
            $data['userCredits'] = 0;
            $data['start_date'] = date('Y-m-d');
            $addons = UserAddon::NotDeleted()->where('user_id',USER_ID)->whereIn('status',[1,3])->where('end_date','>=',date('Y-m-d'))->pluck('addon_id');
            $data['addons'] = Addons::dataList(1,null,reset($addons))['data'];
        }
        else if($input['type'] == 'extra_quota'){
            $data['userCredits'] = 0;
            $data['start_date'] = date('Y-m-d');
            $data['extraQuotas'] = ExtraQuota::dataList(1,null)['data'];
        }

        return view('Tenancy.Profile.Views.cart')->with('data',(object) $data);
    }

    public function postUpdateSubscription(Request $request,$dataArr=null,$totalArr=null){
        $input = \Request::all();
        if($dataArr != null){
            $input['data'] = $dataArr;
            $input['totals'] = $totalArr;
        }

        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $myData   = json_decode($input['data']);
        $testData = [];
        $total = Session::has('userCredits') ? - (int) Session::get('userCredits') : 0;
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
            $total+= $testData[$key][6] * (int)$testData[$key][7];
        }
            
        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;
        $data['data'] = $testData;
        $data['totals'] = $input['totals'];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        return view('Tenancy.Profile.Views.checkout')->with('data',(object) $data);
    }

    public function updateAddonStatus(Request $request,$addon_id,$status){
        $status = (int) $status;
        $addon_id = (int) $addon_id;
        if(!in_array($status,[1,3,4,5])){
            return redirect('404');
        }

        $userAddonObj = UserAddon::getOne($addon_id);
        if(!$userAddonObj || $userAddonObj->user_id != USER_ID){
            return redirect('404');
        }   
        // dd($status);

        $userObj = User::getOne($userAddonObj->user_id);
        if($status == 5){
            $centralUserObj = CentralUser::getOne($userAddonObj->user_id);

            $addonsArr = unserialize($userObj->addons);
            $addonsArr = array_diff( $addonsArr, [$userAddonObj->addon_id] );

            $userObj->addons =  serialize($addonsArr);
            $userObj->save();

            $centralUserObj->addons =  serialize($addonsArr);
            $centralUserObj->save();

            $userAddonObj->deleted_by = USER_ID;
            $userAddonObj->deleted_at = DATE_TIME;
            $userAddonObj->save();
        }elseif($status == 4){
            $addonObj = Addons::getData(Addons::getOne($userAddonObj->addon_id));
            $price = $addonObj->{$userObj->duration_type == 1 ? 'monthly_after_vat' : 'annual_after_vat'};
            $dataArr = [
                [
                    $userAddonObj->addon_id,
                    'addon',
                    $addonObj->title,
                    $userObj->duration_type,
                    date('Y-m-d'),
                    $userObj->duration_type == 1 ? date('Y-m-d',strtotime('+1 month',strtotime(date('Y-m-d')))) : date('Y-m-d',strtotime('+1 year'),strtotime(date('Y-m-d'))),
                    $price,
                    1
                ]
            ];
            $totalArr = [
                $price,
                0,
                0,
                $price,
            ];
            return $this->postUpdateSubscription($request,json_encode($dataArr),json_encode($totalArr));
        }elseif($status == 3){
            $userAddonObj->status = 3;
            $userAddonObj->save();
        }elseif($status == 1){
            $userAddonObj->status = 1;
            $userAddonObj->save();
        }
        
        User::setSessions(User::getOne($userAddonObj->user_id));
        Session::flash('success',trans('main.editSuccess'));
        return redirect()->back();
    }

    public function updateQuotaStatus(Request $request,$extra_quota_id,$status){
        $status = (int) $status;
        $extra_quota_id = (int) $extra_quota_id;
        if(!in_array($status,[4,5])){
            return redirect('404');
        }

        $userExtraQuotaObj = UserExtraQuota::getOne($extra_quota_id);
        if(!$userExtraQuotaObj || $userExtraQuotaObj->user_id != USER_ID){
            return redirect('404');
        }

        $userObj = User::getOne($userExtraQuotaObj->user_id);
        if($status == 5){
            $userExtraQuotaObj->deleted_by = USER_ID;
            $userExtraQuotaObj->deleted_at = DATE_TIME;
            $userExtraQuotaObj->save();
        }elseif($status == 4){
            $extraQuotaObj = ExtraQuota::getData(ExtraQuota::getOne($userExtraQuotaObj->extra_quota_id));
            $price = $extraQuotaObj->{$userObj->duration_type == 1 ? 'monthly_after_vat' : 'annual_after_vat'};
            $dataArr = [
                [
                    $userExtraQuotaObj->extra_quota_id,
                    'extra_quota',
                    $extraQuotaObj->extra_count . ' '.$extraQuotaObj->extraTypeText . ' ' . ($extraQuotaObj->extra_type == 1 ? trans('main.msgPerDay') : ''),
                    $userObj->duration_type,
                    date('Y-m-d'),
                    $userObj->duration_type == 1 ? date('Y-m-d',strtotime('+1 month',strtotime(date('Y-m-d')))) : date('Y-m-d',strtotime('+1 year'),strtotime(date('Y-m-d'))),
                    $price,
                    1
                ]
            ];
            $totalArr = [
                $price,
                0,
                0,
                $price,
            ];
            return $this->postUpdateSubscription($request,json_encode($dataArr),json_encode($totalArr));
        }
        
        User::setSessions(User::getOne($userExtraQuotaObj->user_id));
        Session::flash('success',trans('main.editSuccess'));
        return redirect()->back();
    }
}
