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
use App\Models\BankTransfer;
use App\Models\BankAccount;
use App\Models\Coupon;
use App\Models\OAuthData;
use App\Jobs\SyncOldClient;
use App\Jobs\NewClient;
use Http;

use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;


class SubscriptionControllers extends Controller {

    use \TraitsFunc;
    // New Data
    // users
    // groups
    // groupNumbers
    // contacts
    // chat
    // bot
    // group_messages
    // quick_reply
    // tags
    // salla
    // zid

    public function sync(){
        if(Session::get('is_old') != 1){
            return redirect('/dashboard');
        }  

        $phone = Session::get('phone');
        $baseUrl = 'https://whatsloop.net/api/v1/';

        // Get User Details
        $mainURL = $baseUrl.'user-details';
        $token = '';
        $doSync = 0;

        $data = ['phone' =>  str_replace('+','',$phone)/*'966570116626'*/];
        $result =  Http::post($mainURL,$data);
        if($result->ok() && $result->json()){
            $data = $result->json();
            if($data['status'] == true){
                // Begin Sync
                $doSync = 1;
                $token = $data['UserData']['JWTToken'];

                // Get User Instace
                $moduleData = [];
                $mainURL = $baseUrl.'migration/user-instance';
                $result =  Http::withToken($token)->get($mainURL);
                if($result->ok() && $result->json()){
                    $data = $result->json();
                    if($data['status'] == true){
                        $modules = explode(',',$data['data']['Package']['Mod_Sections']);
                    }
                }
                // dd($modules);
                foreach($modules as $key){
                    if($key == 'NumbersGroups'){
                        $moduleData[] = 'groupNumbers';
                    }elseif($key == 'BotMsgs'){
                        $moduleData[] = 'bot';
                    }elseif($key == 'Contacts'){
                        $moduleData[] = 'contacts';
                    }elseif($key == 'LiveChat'){
                        $moduleData[] = 'chat';
                    }elseif($key == 'Labels'){
                        $moduleData[] = 'tags';
                    }elseif($key == 'Moderators'){
                        $moduleData[] = 'users';
                    }elseif($key == 'ModeratorsGroup'){
                        $moduleData[] = 'groups';
                    }elseif($key == 'GroupMsgs'){
                        $moduleData[] = 'group_messages';
                    }elseif($key == 'QuickReplies'){
                        $moduleData[] = 'quick_reply';
                    }elseif($key == '#SALLA'){
                        $moduleData[] = 'salla';
                    }elseif($key == '#ZID'){
                        $moduleData[] = 'zid';
                    }
                }
            }
        }
        
        if($doSync){
            return view('Tenancy.Dashboard.Views.V5.sync')->with('data', $moduleData);
        }else{
            Session::put('is_old',0);
            return Redirect('/dashboard');
        }
    }
    
    public function postSync(){
        $input = \Request::all();
        $userObj = User::getData(User::NotDeleted()->first());
        $requiredSync  = json_decode($input['data']);
        if($requiredSync){
            try {
              dispatch(new SyncOldClient($userObj,$requiredSync))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
        }
        Session::put('is_synced',1);
        Session::flash('success',trans('main.inPrgo'));
        return redirect()->to('/menu');
    }

    public function getCities(){
        $input = \Request::all();
        $statusObj['regions'] = \DB::connection('main')->table('cities')->where('Country_id',$input['id'])->get();
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }

    public function addCoupon(){
        $input = \Request::all();

        $availableCoupons = Coupon::availableCoupons();
        $availableCoupons = reset($availableCoupons);        
        $coupon = $input['coupon'];

        if($coupon != null){
            if(count($availableCoupons) > 0 && !in_array($coupon, $availableCoupons)){
                return \TraitsFunc::ErrorMessage('هذا الكود ('.$coupon.') غير متاح حاليا');
            }

            if(in_array($coupon, $availableCoupons)){
                $couponObj = Coupon::NotDeleted()->where('code',$coupon)->where('status',1)->first();
                if($couponObj){
                    $invoiceObj = Invoice::NotDeleted()->where('client_id',USER_ID)->where('status',0)->orderBy('id','DESC')->first();
                    if($invoiceObj){
                        $invoiceObj->discount_type = $couponObj->discount_type;
                        $invoiceObj->discount_value = $couponObj->discount_value;
                        $invoiceObj->save();
                    }
                    $statusObj['data'] = Coupon::getData($couponObj);
                    $statusObj['status'] = \TraitsFunc::SuccessMessage(trans('main.addSuccess'));
                    return \Response::json((object) $statusObj);
                }
            }
        }
    }

    public function packages(){   
        $input = \Request::all();
        $mainUser = User::first();
        $arr = [
            TENANT_ID,
            $mainUser->domain,
            $mainUser->id,
            $mainUser->phone,

        ];
        $oauthDataObj = OAuthData::where('type','salla')->where('domain','null')->where('phone',$arr[3])->first();
        if($oauthDataObj){
            $oauthDataObj->user_id = $arr[2];
            $oauthDataObj->domain = $arr[1];
            $oauthDataObj->tenant_id = $arr[0];
            $oauthDataObj->save();
        }

        if(Session::get('membership') != null){
            return redirect('/dashboard');
        }

        $bankTransferObj = BankTransfer::NotDeleted()->where('user_id',USER_ID)->where('status',1)->orderBy('id','DESC')->first();
        if($bankTransferObj){
            $data['msg'] = trans('main.transferSuccess');
            $data['phone'] = CentralVariable::getVar('TECH_PHONE');
            $data['transfer'] = $bankTransferObj;
        }
        // $data['memberships'] = Membership::dataList(1)['data'];
        $data['bundles'] = Bundle::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.V5.packages')->with('data',(object) $data);
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

        $data['membership'] = $membershipObj;
        $data['memberships'] = Membership::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.V5.cart')->with('data',(object) $data);
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
        if($bundleObj->addons != null && !empty(unserialize($bundleObj->addons))){
            $addons = Addons::dataList(1,unserialize($bundleObj->addons))['data'];
        }

        $testData = [];
        $start_date = date('Y-m-d');
        $annual = isset($input['annual']) ? 2 : 1;  
        $total = $annual == 1 ? $bundleObj->monthly_after_vat : $bundleObj->annual_after_vat;
        $testData[] = [
            $membershipObj->id,
            'membership',
            $membershipObj->title,
            $annual,
            $start_date,
            $annual == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date))),
            $annual == 1 ? $membershipObj->monthly_after_vat : $membershipObj->annual_after_vat,
            1,
        ];

        foreach($addons as $addon){
            $testData[] = [
                $addon->id,
                'addon',
                $addon->title,
                $annual,
                $start_date,
                $annual == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($start_date))) : date('Y-m-d',strtotime('+1 year',strtotime($start_date))),
                $annual == 1 ? $addon->monthly_after_vat : $addon->annual_after_vat,
                1,
            ];
        }

        $data['data'] = $testData;
        $botObj = Addons::getOne(1);
        $tax = \Helper::calcTax($total);
        $data['totals'] = [
            $total-$tax,
            (in_array($bundleObj->id,[5,6]) ? ($annual == 1 ? $botObj->monthly_after_vat : $botObj->annual_after_vat ) : 0),
            $tax,
            $total,
        ];

        $userObj = User::getOne(USER_ID);
        if($userObj->membership_id == null){
            $type = 'NewClient';
        }

        $cartObj = Variable::where('var_key','inv_status')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }
        $cartObj->var_key = 'inv_status';
        $cartObj->var_value = $type;
        $cartObj->save();

        $paymentObj = new \SubscriptionHelper(); 
        $invoice = $paymentObj->setInvoice($testData,USER_ID,TENANT_ID,GLOBAL_ID,$type,$total);   

        $inv_id = Variable::where('var_key','inv_id')->first();
        if(!$inv_id){
            $inv_id = new Variable();
        }
        $inv_id->var_key = 'inv_id';
        $inv_id->var_value = $invoice->id;
        $inv_id->save();

        $data['user'] = $userObj;
        $data['invoice'] = Invoice::getData($invoice);
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];

        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers), // seller name        
            new TaxNumber($data['companyAddress']->tax_id), // seller tax number
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['invoice']->due_date))), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($total), // invoice total amount
            new InvoiceTaxAmount($tax) // invoice tax amount
            // TODO :: Support others tags
        ])->render();

        $data['countries'] = \DB::connection('main')->table('country')->get();
        $data['regions'] = [];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['bankAccounts'] = BankAccount::dataList(1)['data'];
        return view('Tenancy.Dashboard.Views.V5.checkout')->with('data',(object) $data);
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
        
        $userObj = User::getOne(USER_ID);
        if($userObj->membership_id == null){
            $type = 'NewClient';
        }
        $cartObj = Variable::where('var_key','inv_status')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }
        $cartObj->var_key = 'inv_status';
        $cartObj->var_value = $type;
        $cartObj->save();

        $paymentObj = new \SubscriptionHelper(); 
        $invoice = $paymentObj->setInvoice($testData,USER_ID,TENANT_ID,GLOBAL_ID,$type);   

        $inv_id = Variable::where('var_key','inv_id')->first();
        if(!$inv_id){
            $inv_id = new Variable();
        }
        $inv_id->var_key = 'inv_id';
        $inv_id->var_value = $invoice->id;
        $inv_id->save();

        $data['user'] = $userObj;
        $data['invoice'] = Invoice::getData($invoice);
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];

        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;

        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers), // seller name        
            new TaxNumber($data['companyAddress']->tax_id), // seller tax number
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['invoice']->due_date))), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($input['totals'][3]), // invoice total amount
            new InvoiceTaxAmount($input['totals'][2]) // invoice tax amount
            // TODO :: Support others tags
        ])->render();

        $data['data'] = $testData;
        $data['totals'] = $input['totals'];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['bankAccounts'] = BankAccount::dataList(1)['data'];
        $data['countries'] = \DB::connection('main')->table('country')->get();
        $data['regions'] = [];
        if(!empty($data['payment']) && $data['payment']->country){
            $data['regions'] = \DB::connection('main')->table('cities')->where('Country_id',$data['payment']->country)->get();
        }
        return view('Tenancy.Dashboard.Views.V5.checkout')->with('data',(object) $data);
    }

    public function calcData($total,$cartData,$userObj){
        $total = json_decode($total);
        $totals = $total[3];

        $cartObj = Variable::where('var_key','cartObj')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }
        $cartObj->var_key = 'cartObj';
        $cartObj->var_value = json_encode($cartData);
        $cartObj->save();

        if(Session::has('userCredits')){
            $userCreditsObj = Variable::where('var_key','userCredits')->first();
            if(!$userCreditsObj){
                $userCreditsObj = new Variable();
            }
            $userCreditsObj->var_value = Session::get('userCredits');
            $userCreditsObj->var_key = 'userCredits';
            $userCreditsObj->save();
        }
        
        $paymentInfoObj = PaymentInfo::NotDeleted()->where('user_id',$userObj->id)->first();
        if(!$paymentInfoObj){
            $paymentInfoObj = new PaymentInfo;
        }
        if(isset($request->address) && !empty($request->address)){
            $paymentInfoObj->user_id = $userObj->id;
            $paymentInfoObj->address = $request->address;
            $paymentInfoObj->address2 = $request->address2;
            $paymentInfoObj->city = $request->city;
            $paymentInfoObj->country = $request->country;
            $paymentInfoObj->region = $request->region;
            $paymentInfoObj->postal_code = $request->postal_code;
            $paymentInfoObj->tax_id = $request->tax_id;
            $paymentInfoObj->created_at = DATE_TIME;
            $paymentInfoObj->created_by = $userObj->id;
            $paymentInfoObj->save();
        }
    }

    public function completeOrder(){
        $input = \Request::all();
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }
        
        $userObj = User::first();
        $centralUser = CentralUser::getOne($userObj->id);

        if(isset($input['name']) && !empty($input['name'])){

            $names = explode(' ',$input['name']);
            // if(count($names) < 2){
            //     Session::flash('error', trans('main.name2Validate'));
            //     return redirect()->back()->withInput();
            // }

            $userObj->name = $input['name'];
            $userObj->save();

            $centralUser->name = $input['name'];
            $centralUser->save();
        }

        if(isset($input['company_name']) && !empty($input['company_name'])){
            $userObj->company = $input['company_name'];
            $userObj->save();

            $centralUser->company = $input['company_name'];
            $centralUser->save();
        }

        $cartData = $input['data'];
        
        $this->calcData($input['totals'],$cartData,$userObj);


        $url = \URL::to('/pushInvoice');
        // if(isset($input['dataType']) && $input['dataType'] > 1){
        //     $url = \URL::to('/pushInvoice2');
        //     if($input['dataType'] == 2){
        //         $nextStartMonth = date('Y-m-d',strtotime('first day of +1 month',strtotime(date('Y-m-d'))));

        //         Variable::where('var_key','endDate')->firstOrCreate([
        //             'var_key' => 'endDate',
        //             'var_value' => $nextStartMonth, 
        //         ]);
        //     }else{
        //         $nextStartMonth = date('Y-m-d',strtotime('+1 month',strtotime(date('Y-m-d'))));

        //         Variable::where('var_key','endDate')->firstOrCreate([
        //             'var_key' => 'endDate',
        //             'var_value' => $nextStartMonth, 
        //         ]);
        //     }
        //     Variable::where('var_key','start_date')->firstOrCreate([
        //         'var_key' => 'start_date',
        //         'var_value' => date('Y-m-d'), 
        //     ]);
        // }

        if($input['payType'] == 2){// Noon Integration
            $urlSecondSegment = '/noon';
            $noonData = [
                'returnURL' => $url ,
                'cart_id' => 'whatsloop-'.rand(1,100000),
                'cart_amount' => json_decode($input['totals'])[3],
                'cart_description' => 'New Membership',
                'paypage_lang' => LANGUAGE_PREF,
                'description' => 'WhatsLoop Membership For User '.$userObj->id,
            ];

            $paymentObj = new \PaymentHelper(); 
            $resultData = $paymentObj->initNoon($noonData);            
                   
            $result = $paymentObj->hostedPayment($resultData['dataArr'],$urlSecondSegment,$resultData['extraHeaders']);
            $result = json_decode($result);
            // dd($result);
            if(($result->data) && $result->data->result->redirect_url){
                return redirect()->away($result->data->result->redirect_url);
            }
        }
    }

    public function bankTransfer(Request $request) {
        if ($request->hasFile('transfer_image')) {
            
            $userObj = User::first();
            $centralUser = CentralUser::getOne($userObj->id);

            $files = $request->file('transfer_image');

            $bankTransferObj = BankTransfer::NotDeleted()->where('user_id',USER_ID)->where('status',1)->first();
            $invoiceObj = Invoice::find($request->invoice_id);
            if(!$bankTransferObj){
                $bankTransferObj = new BankTransfer;
                $bankTransferObj->user_id = USER_ID;
                $bankTransferObj->tenant_id = TENANT_ID;
                $bankTransferObj->global_id = GLOBAL_ID;
                $bankTransferObj->invoice_id = $invoiceObj->id;
                $bankTransferObj->domain = DOMAIN;
                $bankTransferObj->order_no = rand(1,100000);
                $bankTransferObj->status = 1;
                $bankTransferObj->sort = BankTransfer::newSortIndex();
                $bankTransferObj->created_at = DATE_TIME;
                $bankTransferObj->created_by = USER_ID;
            }
            $bankTransferObj->total = $invoiceObj->total;
            $bankTransferObj->save();

            $fileName = \ImagesHelper::uploadFileFromRequest('bank_transfers', $files,$bankTransferObj->id);
            if($fileName == false){
                return false;
            }

            $bankTransferObj->image = $fileName;
            $bankTransferObj->save();

            $cartData = $request->data;
            
            $this->calcData($request->totals,$cartData,$userObj);
            
            $statusObj['data'] = \URL::to('/dashboard');
            $statusObj['status'] = \TraitsFunc::SuccessMessage(trans('main.addSuccess'));
            Session::flash('success',trans('main.transferSuccess'));
            return \Response::json((object) $statusObj);
        }       
    }

    public function pushInvoice(){
        $input = \Request::all();
        $data['data'] = json_decode($input['data']);
        $data['status'] = json_decode($input['status']);
        if($data['status']->status == 1){
            return $this->activate($data['data']->transaction_id,$data['data']->paymentGateaway);
        }else{
            $userObj = User::first();
            User::setSessions($userObj);
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/paymentError')->withInput();
        }
    }
    
    public function paymentError(){
        return view('Tenancy.Dashboard.Views.V5.paymentError');
    }

    public function activate($transaction_id = null , $paymentGateaway = null){
        $cartObj = Variable::getVar('cartObj');
        $cartObj = json_decode(json_decode($cartObj));
        $inv_id = Variable::getVar('inv_id');
        $invoiceObj = Invoice::find($inv_id);
        $type = Variable::getVar('inv_status');

        $data = [
            'cartObj' => $cartObj, 
            'type' => $type,
            'transaction_id' => $transaction_id,
            'paymentGateaway' => $paymentGateaway,
            'start_date' => null,
            'invoiceObj' => $invoiceObj,
            'transferObj' => null,
            'arrType' => null,
            'myEndDate' => null,
        ];
        
        try {
            dispatch(new NewClient($data))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }

        // Session::forget('userCredits');
        $userObj = User::first();
        User::setSessions($userObj);
        if($type == 'NewClient'){
            Session::put('hasJob',1);
        }
        return redirect()->to('/dashboard');
    }

    public function completeJob(){
        $checkHasJob = Session::has('hasJob') ? 1 : 0;
        Session::forget('hasJob');
        Session::forget('userCredits');
        
        $userObj = User::first();
        Session::flush();

        User::setSessions($userObj);
        if($checkHasJob){
            return redirect()->to('/QR');
        }else{
            return redirect()->to('/dashboard');
        }
    }

    public function qrIndex(){
        $varObj = Variable::getVar('QRIMAGE');
        $data['dis'] = 0;
        if($varObj){
            $data['qrImage'] = mb_convert_encoding($varObj, 'UTF-8', 'UTF-8');
            $data['dis'] = 1;
        }

        
        $userAddonsTutorial = [];
        $userAddons = array_unique(Session::get('addons'));
        $addonsTutorial = [1,2,4,5];
        $userObj = User::first();
        for ($i = 0; $i < count($addonsTutorial) ; $i++) {
            if(in_array($addonsTutorial[$i],$userAddons) && UserAddon::where('status',1)->where('addon_id',$addonsTutorial[$i])->where('user_id',$userObj->id)->first()){
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


        if((!isset($data['qrImage']) || $data['qrImage'] == null) && empty($userAddonsTutorial)){
            return redirect('dashboard');
        }

        $data['data'] = array_values($userAddonsTutorial);
        $names = Addons::NotDeleted()->whereIn('id',$data['data'])->pluck('title_'.LANGUAGE_PREF);
        $data['dataNames'] = reset($names);
        $data['channelName'] = trans('main.channel'). ' #'.Session::get('channelCode');
        if(count($data['data']) > 0){
            $data['templates'] = ModTemplate::dataList(null, ($data['data'][0] == 5 ? 1 : 2 )  )['data'];
        }else{
            $data['dis'] = 1;
        }
        return view('Tenancy.Dashboard.Views.V5.qrData')->with('data',(object) $data);
    }

    public function updateName(){
        $input = \Request::all();
        if(!isset($input['name']) || empty($input['name'])){
            return \TraitsFunc::ErrorMessage(trans('main.channelNameValidate'));
        }

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
        $channelObj =  UserChannels::first();

        if(isset($result['data'])){
            if($result['data']['accountStatus'] == 'got qr code'){
                if(isset($result['data']['qrCode'])){
                    $image = '/uploads/instance'.$channelObj->id.'Image' . time() . '.png';
                    $destinationPath = public_path() . $image;
                    $qrCode =  base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $result['data']['qrCode']));
                    $succ = file_put_contents($destinationPath, $qrCode);   
                    $statusObj['data']['qrImage'] = mb_convert_encoding($result['data']['qrCode'], 'UTF-8', 'UTF-8');
                    $statusObj['status'] = \TraitsFunc::SuccessMessage();
                    return \Response::json((object) $statusObj);
                }
            }else if($result['data']['accountStatus'] == 'authenticated'){
                $statusObj['data']['qrImage'] = 'auth';
                $statusObj['status'] = \TraitsFunc::SuccessMessage();
                return \Response::json((object) $statusObj);
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
        if( (!isset($input['type']) || empty($input['type'])) || !in_array($input['type'], ['membership','addon','new','extra_quota'])){
            return redirect(404);    
        }

        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }

        $dataObj = null;
        $data['memberships'] = Session::has('invoice_id') && Session::get('invoice_id') != 0 ? Membership::dataList(1)['data'] : [];
        $data['addons'] = [];
        $data['extraQuotas'] = [];
        $data['userCredits'] = 0;

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
                $oldPriceDay = $dataObj->monthly_after_vat / 30;
            }else if($oldDuration == 2){
                $oldPriceDay = $dataObj->annual_after_vat / 365;
            }

            if($userChannelObj->leftDays > 0){
                $data['userCredits'] = round($oldPriceDay * $userChannelObj->leftDays ,2);
                Session::put('userCredits',$data['userCredits']);
            }

            $data['membership'] = Membership::getData($dataObj);
            $data['memberships'] = Membership::dataList(1)['data'];
            $data['start_date'] = $userChannelObj->start_date;
            $data['end_date'] = $userChannelObj->end_date;
        }else if($input['type'] == 'addon'){
            $data['userCredits'] = 0;
            $data['start_date'] = date('Y-m-d');
            $addons = UserAddon::NotDeleted()->where('user_id',USER_ID)->whereIn('status',[1,3])->where('end_date','>=',date('Y-m-d'))->pluck('addon_id');
            $addons = reset($addons);
            if(in_array(4,$addons) || in_array(5,$addons)){
                $addons[] = 1;
            }
            $addons = array_unique($addons);
            $data['addons'] = Addons::dataList(1,null,$addons)['data'];
            if(Session::has('invoice_id') && Session::get('invoice_id') != 0){
                $data['membership'] = (object)['id'=>0]; //Membership::getData(Membership::getOne(Session::get('membership')));
            }
        }else if($input['type'] == 'extra_quota'){
            $data['userCredits'] = 0;
            $data['start_date'] = date('Y-m-d');
            $data['extraQuotas'] = ExtraQuota::dataList(1,null)['data'];
        }else{
            $data['addons'] = Addons::dataList(1)['data'];
            $data['extraQuotas'] = ExtraQuota::dataList(1)['data'];

            $data['membership'] = null;
            $data['memberships'] = Membership::dataList(1)['data'];
            $data['start_date'] = date('Y-m-d');
            $data['userCredits'] = 0;
        }

        return view('Tenancy.Profile.Views.V5.cart')->with('data',(object) $data);
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

        $total = Session::has('userCredits') ? - floatval(Session::get('userCredits'))  : 0;
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

            if(abs($total) > 0 && $one[1] == 'membership'){
                $userChannelObj = UserChannels::getUserChannels()['data'][0];
                $testData[$key][4] = $userChannelObj->start_date;
                $testData[$key][5] = $userChannelObj->end_date;

                if($one[3] == 1){
                    $newPriceDay = $dataObj->monthly_after_vat / 30;
                }else if($oldDuration == 2){
                    $newPriceAfterVat = $dataObj->annual_after_vat;
                    $newPriceDay = $dataObj->annual_after_vat / 365;
                }
                $input['totals'] = json_decode($input['totals']);
                $input['totals'][0] = $newPriceDay * $userChannelObj->leftDays + $total;
                $tax = \Helper::calcTax($input['totals'][0]);
                $totalArr = [
                    number_format((float)$input['totals'][0] - $tax, 2, '.', ''),
                    0,
                    number_format((float)$tax, 2, '.', ''),
                    number_format((float)$input['totals'][0], 2, '.', ''),
                ];


                $input['totals'] = json_encode($totalArr);
                $testData[$key][6] = $newPriceDay * $userChannelObj->leftDays + $total;
                $total+= $newPriceDay * $userChannelObj->leftDays ;

            }else{
                $testData[$key][6] = $one[3] == 1 ? $dataObj->monthly_after_vat : $dataObj->annual_after_vat;
                $total+= $testData[$key][6] * (int)$testData[$key][7];
            }
        }


        $cartObj = Variable::where('var_key','inv_status')->first();
        if(!$cartObj){
            $cartObj = new Variable();
        }
        $cartObj->var_key = 'inv_status';
        $cartObj->var_value = !Session::has('userCredits') ? 'SubscriptionChanged' : 'Upgraded';
        $cartObj->save();

        $paymentObj = new \SubscriptionHelper(); 
        $invoice = $paymentObj->setInvoice($testData,USER_ID,TENANT_ID,GLOBAL_ID,'SubscriptionChanged');   

        $inv_id = Variable::where('var_key','inv_id')->first();
        if(!$inv_id){
            $inv_id = new Variable();
        }
        $inv_id->var_key = 'inv_id';
        $inv_id->var_value = $invoice->id;
        $inv_id->save();

        $userObj = User::find(USER_ID);
        $data['user'] = $userObj;
        $data['invoice'] = Invoice::getData($invoice);
        $data['companyAddress'] = (object) [
            'servers' => CentralVariable::getVar('servers'),
            'address' => CentralVariable::getVar('address'),
            'region' => CentralVariable::getVar('region'),
            'city' => CentralVariable::getVar('city'),
            'postal_code' => CentralVariable::getVar('postal_code'),
            'country' => CentralVariable::getVar('country'),
            'tax_id' => CentralVariable::getVar('tax_id'),
        ];
            
        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;

        $data['qrImage'] = GenerateQrCode::fromArray([
            new Seller($data['companyAddress']->servers), // seller name        
            new TaxNumber($data['companyAddress']->tax_id), // seller tax number
            new InvoiceDate(date('Y-m-d\TH:i:s\Z',strtotime($data['invoice']->due_date))), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
            new InvoiceTotalAmount($input['totals'][3]), // invoice total amount
            new InvoiceTaxAmount($input['totals'][2]) // invoice tax amount
            // TODO :: Support others tags
        ])->render();
        
        $data['data'] = $testData;
        $data['totals'] = $input['totals'];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['countries'] =  \DB::connection('main')->table('country')->get();
        $data['regions'] = [];
        $data['disDelete'] = true;
        if(!empty($data['payment']) && $data['payment']->country){
            $data['regions'] = \DB::connection('main')->table('cities')->where('Country_id',$data['payment']->country)->get();
        }
        $data['bankAccounts'] = BankAccount::dataList(1)['data'];
        return view('Tenancy.Profile.Views.V5.checkout')->with('data',(object) $data);
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
            $tax = \Helper::calcTax($price);
            $totalArr = [
                number_format((float)$price - $tax, 2, '.', ''),
                0,
                number_format((float)$tax, 2, '.', ''),
                number_format((float)$price, 2, '.', ''),
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
            $tax = \Helper::calcTax($price);
            $totalArr = [
                number_format((float)$price - $tax, 2, '.', ''),
                0,
                number_format((float)$tax, 2, '.', ''),
                number_format((float)$price, 2, '.', ''),
            ];
            return $this->postUpdateSubscription($request,json_encode($dataArr),json_encode($totalArr));
        }
        
        User::setSessions(User::getOne($userExtraQuotaObj->user_id));
        Session::flash('success',trans('main.editSuccess'));
        return redirect()->back();
    }
}
