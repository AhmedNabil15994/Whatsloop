<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Addons;
use App\Models\UserAddon;
use App\Models\Group;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use App\Models\Variable;
use App\Models\UserChannels;
use App\Models\ChatMessage;
use App\Models\ChatDialog;
use App\Models\PaymentInfo;
use App\Models\CentralChannel;
use App\Models\ContactLabel;
use App\Models\ContactReport;
use App\Models\UserStatus;
use App\Models\ExtraQuota;
use App\Models\Membership;
use App\Models\UserExtraQuota;
use App\Models\CentralUser;
use App\Models\BankAccount;
use Storage;
use DataTables;
use Validator;
use App\Jobs\SyncMessagesJob;
use App\Jobs\ReadChatsJob;


class ProfileControllers extends Controller {

    use \TraitsFunc;

    public function index(Request $request) {
        $data['designElems']['mainData'] = [
            'title' => trans('main.myAccount'),
        ];
        return view('Tenancy.Profile.Views.index')->with('data', (object) $data);
    }

    public function personalInfo(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.account_setting'),
            'icon' => 'fa fa-user',
        ];
        $data['data'] = $userObj;
        $data['paymentInfo'] = $userObj->paymentInfo ;
        return view('Tenancy.Profile.Views.V5.personalInfo')->with('data', (object) $data);
    }

    public function updatePersonalInfo(){
        $input = \Request::all();
        $mainUserObj = User::getOne(USER_ID);
        $dataObj = User::getData($mainUserObj);
        $domainObj = \DB::connection('main')->table('domains')->where('domain',$dataObj->domain)->first();

        $oldDomainValue = $domainObj->domain;

        if(isset($input['email']) && !empty($input['email'])){
            $userObj = User::checkUserBy('email',$input['email'],USER_ID);
            if($userObj){
                Session::flash('error', trans('main.emailError'));
                return redirect()->back()->withInput();
            }
            $mainUserObj->email = $input['email'];
        }
        if(isset($input['phone']) && !empty($input['phone'])){
            $userObj = User::checkUserBy('phone',$input['phone'],USER_ID);
            if($userObj){
                Session::flash('error', trans('main.phoneError'));
                return redirect()->back()->withInput();
            }
            $mainUserObj->phone = $input['phone'];

            \DB::connection('main')->table('tenants')->where('id',$domainObj->tenant_id)->update([
                'phone' => $input['phone'],
            ]);
        }

        if(isset($input['pin_code']) && !empty($input['pin_code'])){
            $mainUserObj->pin_code = $input['pin_code'];
        }

        if(isset($input['two_auth']) && !empty($input['two_auth'])){
            $mainUserObj->two_auth = $input['two_auth'];
        }

        if(isset($input['emergency_number']) && !empty($input['emergency_number'])){
            $mainUserObj->emergency_number = $input['emergency_number'];
        }

        if(isset($input['domain']) && !empty($input['domain'])){
            $checkDomainObj = \DB::connection('main')->table('domains')->where('domain',$input['domain'])->first();
            if($checkDomainObj && $checkDomainObj->domain != $dataObj->domain){
                Session::flash('error', trans('main.domainValidate2'));
                return redirect()->back()->withInput();
            }

            $mainUserObj->domain = $input['domain'];
            \DB::connection('main')->table('domains')->where('tenant_id',$domainObj->tenant_id)->limit(1)->update([
                'domain' => $input['domain'],
            ]);

        }

        if(isset($input['company']) && !empty($input['company'])){
            $mainUserObj->company = $input['company'];
        }

        if(isset($input['name']) && !empty($input['name'])){
            $mainUserObj->name = $input['name'];
            \DB::connection('main')->table('tenants')->where('id',$domainObj->tenant_id)->update([
                'title' => $input['name'],
            ]);
        }

        $mainUserObj->save();

        $photos_name = Session::get('photos');
        if($photos_name){
            $photos = Storage::files($photos_name);
            if(count($photos) > 0){
                $images = self::addImage($photos[0],$mainUserObj->id);
                if ($images == false) {
                    Session::flash('error', trans('main.uploadProb'));
                    return redirect()->back()->withInput();
                }
                $mainUserObj->image = $images;
                $mainUserObj->save();  
            }
        }

        if($input['domain'] != $oldDomainValue){
            return redirect()->to(config('tenancy.protocol').$input['domain'].'.'.config('tenancy.central_domains')[0].'/login');
        }

        Session::forget('photos');
        WebActions::newType(2,'User');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function postChangePassword(){
        $input = \Request::all();
        $rules = [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ];

        $message = [
            'password.required' => trans('auth.passwordValidation'),
            'password.confirmed' => trans('auth.passwordValidation2'),
            'password_confirmation.required' => trans('auth.passwordValidation3'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
        }

        $password = $input['password'];
        $userObj = User::NotDeleted()->find(USER_ID);
        if($userObj == null){
            Session::flash('error', trans('auth.invalidUser'));
            return back()->withInput();
        }

        $userObj->password = Hash::make($password);
        $userObj->save();
        
        WebActions::newType(2,'User');
        Session::flash('success', trans('auth.passwordChanged'));
        return \Redirect::back()->withInput();
    }

    public function postPaymentInfo(){
        $input = \Request::all();
        $rules = [
            'address' => 'required',
        ];

        $message = [
            'address.required' => trans('main.addressValidation'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
        }
        $userObj = User::authenticatedUser();
        if($userObj->paymentInfo){
            $paymentInfoObj = $userObj->paymentInfo;
            $paymentInfoObj->updated_at = DATE_TIME;
            $paymentInfoObj->updated_by = USER_ID;
            $type = 2;
        }else{
            $paymentInfoObj = new PaymentInfo();
            $paymentInfoObj->created_at = DATE_TIME;
            $paymentInfoObj->created_by = USER_ID;
            $type = 1;
        }

        $paymentInfoObj->user_id = USER_ID;
        $paymentInfoObj->address = $input['address'];
        $paymentInfoObj->address2 = $input['address2'];
        $paymentInfoObj->city = $input['city'];
        $paymentInfoObj->payment_method = $input['payment_method'];
        $paymentInfoObj->currency = $input['currency'];
        $paymentInfoObj->region = $input['region'];
        $paymentInfoObj->country = $input['country'];
        $paymentInfoObj->postal_code = $input['postal_code'];
        $paymentInfoObj->tax_id = $input['tax_id'];
        $paymentInfoObj->save();

        WebActions::newType($type,'PaymentInfo');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function postNotifications(){
        $input = \Request::all();
        $userObj = User::getOne(USER_ID);
        $userObj->notifications = isset($input['notifications']) && !empty($input['notifications']) ? 1 : 0;
        $userObj->save();

        WebActions::newType(2,'User');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function postOffers(){
        $input = \Request::all();
        $userObj = User::getOne(USER_ID);
        $userObj->offers = isset($input['offers']) && !empty($input['offers']) ? 1 : 0;
        $userObj->save();

        WebActions::newType(2,'User');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function extraQuotas(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.extraQuotas'),
            'icon' => 'fas fa-star',
        ];
        $data['data'] = $userObj;
        $data['extraQuotas'] = ExtraQuota::dataList()['data'];
        $userQuotas = UserExtraQuota::getForUser($userObj->global_id);
        $data['userQuotas'] = reset($userQuotas[0]);
        $data['userQuotas2'] = array_unique($data['userQuotas']);
        // dd($data['userQuotas2']);
        return view('Tenancy.Profile.Views.extraQuotas')->with('data', (object) $data);
    }

    public function postExtraQuotas($extraQuota_id){
        $input = \Request::all();
        $extraQuota_id = (int) $extraQuota_id;
        $userObj = User::getOne(USER_ID);
        $extraQuotaObj = ExtraQuota::getOne($extraQuota_id);
        if(!$extraQuotaObj){
            return redirect('404');
        }
        $userExtraQuotaObj = UserExtraQuota::NotDeleted()->where('user_id',USER_ID)->where('extra_quota_id',$extraQuota_id)->first();
        if(!$userExtraQuotaObj){
            $userExtraQuotaObj = new UserExtraQuota;
        }
        $userExtraQuotaObj->user_id = USER_ID;
        $userExtraQuotaObj->extra_quota_id = $extraQuota_id;
        $userExtraQuotaObj->tenant_id = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first()->tenant_id;
        $userExtraQuotaObj->status = 1;
        $userExtraQuotaObj->global_user_id = $mainUser->global_id;
        $userExtraQuotaObj->duration_type = 1;
        $userExtraQuotaObj->start_date = date('Y-m-d');
        $userExtraQuotaObj->end_date = date('Y-m-d', strtotime("+1 month",strtotime(date('Y-m-d'))));
        $userExtraQuotaObj->created_by = USER_ID;
        $userExtraQuotaObj->created_at = DATE_TIME;
        $userExtraQuotaObj->save();


        WebActions::newType(2,'User');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function addons(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.addons'),
            'icon' => 'fas fa-star',
        ];
        $mainUser = User::first();
        $data['data'] = $userObj;
        $data['addons'] = Addons::dataList()['data'];
        $data['userAddons'] = $mainUser->addons != null ? unserialize($mainUser->addons) : [];;
        $data['userAddons2'] = UserAddon::getAllDataForUser($mainUser->id);
        return view('Tenancy.Profile.Views.addons')->with('data', (object) $data);
    }

    public function postAddons($addon_id){
        $input = \Request::all();
        $addon_id = (int) $addon_id;
        $userObj = User::getOne(USER_ID);
        $extraQuotaObj = Addons::getOne($addon_id);
        if(!$extraQuotaObj){
            return redirect('404');
        }

        $tryFlag = 0;
        $userExtraQuotaObj = UserAddon::NotDeleted()->where('user_id',USER_ID)->where('addon_id',$addon_id)->first();
        if(!$userExtraQuotaObj){
            $userExtraQuotaObj = new UserAddon;
            $start_date = date('Y-m-d');
            $tryFlag = 1;
        }else{
            $start_date = $userExtraQuotaObj->start_date;
        }

        $userExtraQuotaObj->user_id = USER_ID;
        $userExtraQuotaObj->addon_id = $addon_id;
        $userExtraQuotaObj->status = 1;
        $userExtraQuotaObj->tenant_id = \DB::connection('main')->table('tenant_users')->where('global_user_id',$userObj->global_id)->first()->tenant_id;
        $userExtraQuotaObj->global_user_id = $userObj->global_id;
        $userExtraQuotaObj->duration_type = isset($input['addons'][$addon_id][2]) ? 2 : 1;
        $userExtraQuotaObj->start_date = $start_date;
        $userExtraQuotaObj->end_date = date('Y-m-d', strtotime("+1 ".($userExtraQuotaObj->duration_type == 1 ? 'month' : 'year'),strtotime($start_date)));
        $userExtraQuotaObj->created_by = USER_ID;
        $userExtraQuotaObj->created_at = DATE_TIME;
        $userExtraQuotaObj->save();


        if($tryFlag){
            $oldAddons = $userObj->addons != null ? unserialize($userObj->addons) : [];
            $oldAddons[] = $addon_id;
            $userObj->addons = serialize($oldAddons);
            $userObj->save();
        }

        WebActions::newType(2,'User');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function getDiffs($end_date,$oldDuration,$monthly_after_vat,$annual_after_vat){
        $nextStartMonth = date('Y-m-d',strtotime('first day of +1 month',strtotime($end_date)));

        $endDate = strtotime($end_date);
        $datediff = strtotime($nextStartMonth) - $endDate;
        $daysLeft = (int) round($datediff / (60 * 60 * 24));
        
        
        $newPriceAfterVat = $monthly_after_vat;

        if($oldDuration == 1){
            $usedCost = ($monthly_after_vat / 30);
        }else if($oldDuration == 2){
            $usedCost = ($annual_after_vat / 365);
            $newPriceAfterVat = $annual_after_vat;
        }
        $membershipMustPaid = round( $daysLeft * $usedCost ,2);
        return [
            'mustPaid' => $membershipMustPaid,
            'daysLeft' => $daysLeft,
            'nextStartMonth' => $nextStartMonth,
        ];
    }

    public function transferPayment(){
        $mustPaid = 0;
        $dataObj = Membership::getData(Membership::getOne(Session::get('membership')));
        if(!$dataObj || $dataObj->id == 4){
            Session::flash('error',trans('main.membershipValidate'));
            return redirect()->to('/dashboard');
        }

        $userChannelObj = UserChannels::getUserChannels()['data'][0];
        $mainUserObj = User::first();
        $oldDuration = $mainUserObj->duration_type;

        $diffData = $this->getDiffs($userChannelObj->end_date,$oldDuration,$dataObj->monthly_after_vat,$dataObj->annual_after_vat);
        Variable::where('var_key','endDate')->firstOrCreate([
            'var_key' => 'endDate',
            'var_value' => $diffData['nextStartMonth'], 
        ]);

        $testData = [];

        $mustPaid += $diffData['mustPaid'];
        $testData[] = [
            $dataObj->id,
            'membership',
            $dataObj->title,
            1,
            $userChannelObj->start_date,
            date('Y-m-d',strtotime('+'.$diffData['daysLeft'].' days',strtotime($userChannelObj->end_date))),
            $diffData['mustPaid'],
            1,
        ];

        $userAddons = UserAddon::getActivated($mainUserObj->id);
        foreach($userAddons as $userAddon){
            if(!in_array(date('d',strtotime($userAddon->end_date)),[1,28,29,30,31])){
                $addonObj = Addons::getData(Addons::getOne($userAddon->addon_id));
                $addonDiffData = $this->getDiffs($userAddon->end_date,$userAddon->duration_type,$addonObj->monthly_after_vat,$addonObj->annual_after_vat);
                $mustPaid += $addonDiffData['mustPaid'];
                $testData[] = [
                    $addonObj->id,
                    'addon',
                    $addonObj->title,
                    1,
                    $userAddon->start_date,
                    date('Y-m-d',strtotime('+'.$addonDiffData['daysLeft'].' days',strtotime($userAddon->end_date))),
                    $addonDiffData['mustPaid'],
                    1,
                ];
            }   
        }

        $userExtraQuotas = UserExtraQuota::getActivated($mainUserObj->id);
        $duplicated = [];
        foreach($userExtraQuotas as $userExtraQuota){
            if(!in_array(date('d',strtotime($userExtraQuota->end_date)),[1,28,29,30,31])){
                if(isset($duplicated[$userExtraQuota->addon_id])){
                    $duplicated[$userExtraQuota->addon_id] = $duplicated[$userExtraQuota->addon_id] + 1;
                }else{
                    $duplicated[$userExtraQuota->addon_id] = 1;
                }

                $extraQuotaObj = ExtraQuota::getData(ExtraQuota::getOne($userExtraQuota->extra_quota_id));
                $extraQuotaDiffData = $this->getDiffs($userExtraQuota->end_date,$userExtraQuota->duration_type,$extraQuotaObj->monthly_after_vat,$extraQuotaObj->annual_after_vat);
                $mustPaid += $extraQuotaDiffData['mustPaid'] * $duplicated[$userExtraQuota->addon_id];
                $testData[] = [
                    $extraQuotaObj->id,
                    'extra_quota',
                    $extraQuotaObj->extraTypeText,
                    1,
                    $userExtraQuota->start_date,
                    date('Y-m-d',strtotime('+'.$extraQuotaDiffData['daysLeft'].' days',strtotime($userExtraQuota->end_date))),
                    $extraQuotaDiffData['mustPaid'],
                    $duplicated[$userExtraQuota->addon_id],
                ];        
            }
        }


        $data['data'] = $testData;
        $mustPaid = round($mustPaid,2);
        $tax = \Helper::calcTax($mustPaid);
        $data['totals'] = [
            $mustPaid-$tax,
            0,
            $tax,
            $mustPaid,
        ];

        $data['user'] = User::getOne(USER_ID);
        $data['countries'] = countries();
        $data['regions'] = [];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['bankAccounts'] = BankAccount::dataList(1)['data'];
        return view('Tenancy.Profile.Views.checkout')->with('data',(object) $data);
    }

    public function calcData($total,$cartData,$userObj){
        $total = json_decode($total);
        $totals = $total[3];


        Variable::firstOrCreate([
            'var_key' => 'cartObj',
            'var_value' => json_encode($cartData),
        ]);
        

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

    public function renewToFirst(){
        $input = \Request::all();
        if(!IS_ADMIN){
            return redirect()->to('/dashboard');
        }
        
        $userObj = User::first();
        $centralUser = CentralUser::getOne($userObj->id);

        if(isset($input['name']) && !empty($input['name'])){

            $names = explode(' ',$input['name']);
            if(count($names) < 2){
                Session::flash('error', trans('main.name2Validate'));
                return redirect()->back()->withInput();
            }

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
        // dd(json_decode($input['totals'])[3]);
        if($input['payType'] == 2){// Noon Integration
            $urlSecondSegment = '/noon';
            $noonData = [
                'returnURL' => str_replace('http:','https:',\URL::to('/pushInvoice2')),
                // 'returnURL' => \URL::to('/pushInvoice2'),  // For Local 
                'cart_id' => 'whatsloop-'.rand(1,100000),
                'cart_amount' => json_decode($input['totals'])[3],
                'cart_description' => 'Transfer Payment To 1st of month',
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

    public function pushInvoice2(){
        $input = \Request::all();
        $data['data'] = json_decode($input['data']);
        $data['status'] = json_decode($input['status']);
        // dd($data);
        if($data['status']->status == 1){
            return $this->activate($data['data']->transaction_id,$data['data']->paymentGateaway);
        }else{
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/');
        }
    }

    public function activate($transaction_id = null , $paymentGateaway = null){
        $cartObj = Variable::getVar('cartObj');
        $endDate = Variable::getVar('endDate');
        $start_date = Variable::getVar('start_date');
        $cartObj = json_decode(json_decode($cartObj));

        $paymentObj = new \SubscriptionHelper(); 
        $resultData = $paymentObj->newSubscription($cartObj,'new',$transaction_id,$paymentGateaway,$start_date,null,null,null,$endDate);   
        if($resultData[0] == 0){
            Session::flash('error',$resultData[1]);
            return back()->withInput();
        }         

        $userObj = User::first();
        User::setSessions($userObj);
        return redirect()->to('/dashboard');
    }


    public function services(){
        $data['designElems']['mainData'] = [
            'title' => trans('main.service_tethering'),
            'icon' => 'mdi mdi-lan-connect',
        ];
        return view('Tenancy.Profile.Views.V5.services')->with('data', (object) $data);
    }

    public function updateSalla(Request $request){
        $input = \Request::all();
        $rules = [
            'store_token' => 'required',
        ];

        $message = [
            'store_token.required' => trans('main.storeTokenValidation'),
        ];

         $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            if($request->ajax()){
                return \TraitsFunc::ErrorMessage($validate->messages()->first());
            }else{
                Session::flash('error', $validate->messages()->first());
                return back()->withInput();
            }
        }

        $sallaObj = Variable::NotDeleted()->where('var_key','SallaStoreToken')->first();
        if($sallaObj == null){
            $sallaObj = new Variable;
            $sallaObj->var_key = 'SallaStoreToken';
            $sallaObj->var_value = $input['store_token'];
            $sallaObj->created_at = DATE_TIME;
            $sallaObj->created_by = USER_ID;
            $sallaObj->save();
        }else{
            $sallaObj->var_value = $input['store_token'];
            $sallaObj->updated_at = DATE_TIME;
            $sallaObj->updated_by = USER_ID;
            $sallaObj->save();
        }

        if($request->ajax()){
            return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
        }else{
            Session::flash('success', trans('main.editSuccess'));
            return \Redirect::back()->withInput();
        }
    }

    public function updateZid(Request $request){
        $input = \Request::all();

        $rules = [
            'store_token' => 'required',
            'store_id' => 'required',
        ];

        $message = [
            'store_token.required' => trans('main.storeTokenValidation'),
            'store_id.required' => trans('main.storeIDValidation'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            if($request->ajax()){
                return \TraitsFunc::ErrorMessage($validate->messages()->first());
            }else{
                Session::flash('error', $validate->messages()->first());
                return back()->withInput();
            }
        }

        $zidStoreToken = Variable::NotDeleted()->where('var_key','ZidStoreToken')->first();
        if($zidStoreToken == null){
            $zidStoreToken = new Variable;
            $zidStoreToken->var_key = 'ZidStoreToken';
            $zidStoreToken->var_value = $input['store_token'];
            $zidStoreToken->created_at = DATE_TIME;
            $zidStoreToken->created_by = USER_ID;
            $zidStoreToken->save();
        }else{
            $zidStoreToken->var_value = $input['store_token'];
            $zidStoreToken->updated_at = DATE_TIME;
            $zidStoreToken->updated_by = USER_ID;
            $zidStoreToken->save();
        }

        $zidStoreID = Variable::NotDeleted()->where('var_key','ZidStoreID')->first();
        if($zidStoreID == null){
            $zidStoreID = new Variable;
            $zidStoreID->var_key = 'ZidStoreID';
            $zidStoreID->var_value = $input['store_id'];
            $zidStoreID->created_at = DATE_TIME;
            $zidStoreID->created_by = USER_ID;
            $zidStoreID->save();
        }else{
            $zidStoreID->var_value = $input['store_id'];
            $zidStoreID->updated_at = DATE_TIME;
            $zidStoreID->updated_by = USER_ID;
            $zidStoreID->save();
        }

        if($request->ajax()){
            return \TraitsFunc::SuccessResponse(trans('main.editSuccess'));
        }else{
            Session::flash('success', trans('main.editSuccess'));
            return \Redirect::back()->withInput();
        }
    }

    public function subscription(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.subscriptionManage'),
            'icon' => 'fa fa-cogs',
        ];

        // Perform Whatsapp Integration
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $updateResult = $mainWhatsLoopObj->me();
        $result = $updateResult->json();
        // dd($result);

        if($result == null){
            Session::flash('error', trans('main.loading'));
            return back()->withInput();
        }

        // if($result['status']['status'] != 1){
        //     Session::flash('error', $result['status']['message']);
        //     return back()->withInput();
        // }

        // Fetch Subscription Data
        $membershipObj = Session::get('membership') != null ?  Membership::getData(Membership::getOne(Session::get('membership'))) : [];
        $channelObj = Session::get('channel') != null ?  CentralChannel::getData(CentralChannel::getOne(Session::get('channel'))) : null;
        if($channelObj){
            $channelStatus = ($channelObj->leftDays > 0 && date('Y-m-d') <= $channelObj->end_date) ? 1 : 0;
        }

        $data['subscription'] = (object) [
            'package_id' => $channelObj ?  $membershipObj->id : '',
            'package_name' => $channelObj ?  $membershipObj->title : '',
            'channelStatus' => $channelObj ?  $channelStatus : '',
            'start_date' => $channelObj ?  $channelObj->start_date : '',
            'end_date' => $channelObj ?  $channelObj->end_date : '',
            'leftDays' => $channelObj ?  $channelObj->leftDays : '',
            'addons' => $channelObj ?  UserAddon::dataList(null,USER_ID,null,[1,2,3])['data'] : [],
            'extra_quotas' => $channelObj ?  UserExtraQuota::getForUser(GLOBAL_ID)[1] : [],
        ];

        $data['data'] = $userObj;
        $data['me'] = (object) ($result != null && isset($result['data']) ? $result['data'] : []);
        $userStatusObj = UserStatus::orderBy('id','DESC')->first();
        if($userStatusObj){
            $data['status'] = $channelObj ? UserStatus::getData($userStatusObj) : '';
        }else{
            $data['status'] = [];
        }

        $data['allMessages'] = ChatMessage::count();
        $data['sentMessages'] = ChatMessage::where('fromMe',1)->count();
        $data['incomingMessages'] = $data['allMessages'] - $data['sentMessages'];
        $data['channel'] = $channelObj ? CentralChannel::getData(CentralChannel::getOne(Session::get('channel'))) : null;
        $data['contactsCount'] = Contact::NotDeleted()->count();
        return view('Tenancy.Profile.Views.subscription')->with('data', (object) $data);
    }

    public function screenshot(){
        // Perform Whatsapp Integration
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $updateResult = $mainWhatsLoopObj->screenshot();
        $result = $updateResult->json();

        if($result['status']['status'] != 1){
            return \TraitsFunc::ErrorMessage($result['status']['message']);
        }
        $dataList['image'] = $result['data']['image'];
        $dataList['status'] = \TraitsFunc::SuccessResponse();
        return \Response::json((object) $dataList);           
    }

    public function reconnect(){
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $updateResult = $mainWhatsLoopObj->reboot();
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }
        Session::flash('success',trans('main.reconnectDone'));
        return redirect()->back();
    }

    public function closeConn(){
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $updateResult = $mainWhatsLoopObj->logout();
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }
        Session::flash('success',trans('main.logoutDone'));
        return redirect()->back();
    }

    public function sync(){
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $lastMessageObj = ChatMessage::orderBy('time','DESC')->first();
        if($lastMessageObj != null){
            $data['min_time'] = $lastMessageObj->time - 7200;
        }
        $updateResult = $mainWhatsLoopObj->messages($data);
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }

        dispatch(new SyncMessagesJob($result['data']['messages']));
        
        Session::flash('success',trans('main.syncInProgress'));
        return redirect()->back();
    }

    public function syncAll(){
        $lastMessageObj = ChatMessage::where('id','!=',null)->delete();
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->messages($data);
        $result = $updateResult->json();

        if($result != null && $result['status']['status'] != 1){
            Session::flash('error',$result['status']['message']);
            return redirect()->back();
        }

        dispatch(new SyncMessagesJob($result['data']['messages']));
        
        Session::flash('success',trans('main.syncInProgress'));
        return redirect()->back();
    }

    public function restoreAccountSettings(){

        $mainWhatsLoopObj = new \MainWhatsLoop();
        // // Update User With Settings For Whatsapp Based On His Domain
        $myData = [
            'sendDelay' => 0,
            'webhookUrl' => '',
            'instanceStatuses' => 0,
            'webhookStatuses' => 0,
            'statusNotificationsOn' => 0,
            'ackNotificationsOn' => 0,
            'chatUpdateOn' => 0,
            'videoUploadOn' => 0,
            'guaranteedHooks' => 0,
            'parallelHooks' => 0,
        ];
        $updateResult = $mainWhatsLoopObj->postSettings($myData);
        $result = $updateResult->json();

        $updateResult = $mainWhatsLoopObj->clearInstance();
        $result = $updateResult->json();
    
        $userObj = User::first();
        $centralUser = CentralUser::getOne($userObj->id);
        
        $userObj->setting_pushed = 0;
        $userObj->save();

        $centralUser->setting_pushed = 0;
        $centralUser->save();
        
        Variable::whereIn('var_key',[
            'MODULE_1','MODULE_2','MODULE_3','MODULE_4','MODULE_5',
            'MODULE_6','MODULE_7','MODULE_8','MODULE_9',
        ])->update(['var_value'=>0]);   

        Contact::where('id','!=',null)->delete();
        ChatMessage::where('id','!=',null)->delete();
        ChatDialog::where('id','!=',null)->delete();
        ContactLabel::where('id','!=',null)->delete();
        ContactReport::where('id','!=',null)->delete();
        UserStatus::where('id','!=',null)->delete();
     
        Session::flash('success',trans('main.logoutDone'));
        return redirect()->back();
    }

    public function read($status){
        $status = (int) $status;
        if(!in_array($status, [0,1])){
            return redirect('404');
        }

        $sending_status_text = 2;
        if($status == 1){
            $sending_status_text = 3;
        }

        $messages = ChatMessage::where('fromMe',0)->groupBy('chatId')->pluck('chatId');
        ChatMessage::whereIn('chatId',reset($messages))->update(['sending_status' => $sending_status_text]);
        dispatch(new ReadChatsJob(reset($messages),$status));

        Session::flash('success',trans('main.inPrgo'));
        return redirect()->back();
    }

    public function apiSetting(Request $request){
        $data['designElems']['mainData'] = [
            'title' => trans('main.api_setting'),
            'icon' => 'fas fa-handshake',
            'url' => 'profile/apiSetting',
            'name' => 'UserChannels',
            'nameOne' => 'UserChannel',
            'modelName' => 'UserChannels',
        ];

        $data['designElems']['searchData'] = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '0',
                'label' => trans('main.id'),
                'specialAttr' => '',
            ],
            'name' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.name'),
                'specialAttr' => '',
            ],
            'token' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '2',
                'label' => trans('main.token'),
                'specialAttr' => '',
            ],
            // 'from' => [
            //     'type' => 'text',
            //     'class' => 'form-control m-input datepicker',
            //     'index' => '3',
            //     'id' => 'datepicker1',
            //     'label' => trans('main.start_date') . ' '.trans('main.from'),
            // ],
            // 'to' => [
            //     'type' => 'text',
            //     'class' => 'form-control m-input datepicker',
            //     'index' => '3',
            //     'id' => 'datepicker2',
            //     'label' => trans('main.end_date') . ' '.trans('main.to'),
            // ],
            // 'from2' => [
            //     'type' => 'text',
            //     'class' => 'form-control m-input datepicker',
            //     'index' => '4',
            //     'id' => 'datepicker3',
            //     'label' => trans('main.start_date') . ' '.trans('main.from'),
            // ],
            // 'to2' => [
            //     'type' => 'text',
            //     'class' => 'form-control m-input datepicker',
            //     'index' => '4',
            //     'id' => 'datepicker2',
            //     'label' => trans('main.end_date') . ' '.trans('main.to'),
            // ],

        ];

        $data['designElems']['tableData'] = [
            'myId' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'name2' => [
                'label' => trans('main.name'),
                'type' => '',
                'className' => '',
                'data-col' => 'name2',
                'anchor-class' => '',
            ],
            'instanceToken' => [
                'label' => trans('main.token'),
                'type' => '',
                'className' => '',
                'data-col' => 'token',
                'anchor-class' => '',
            ],
            'start_date' => [
                'label' => trans('main.start_date'),
                'type' => '',
                'className' => '',
                'data-col' => 'start_date',
                'anchor-class' => '',
            ],
            'end_date' => [
                'label' => trans('main.end_date'),
                'type' => '',
                'className' => '',
                'data-col' => 'end_date',
                'anchor-class' => '',
            ],
        ];

        if($request->ajax()){
            $data = CentralChannel::dataList(Session::get('channel'));
            return Datatables::of($data['data'])->make(true);
        }
        $data['dis'] = true;
        return view('Tenancy.User.Views.index')->with('data', (object) $data);
    }

    public function webhookSetting(){
        // $userObj = User::authenticatedUser();        
        $data['designElems']['mainData'] = [
            'title' => trans('main.webhook_setting'),
            'icon' => 'mdi mdi-webhook',
        ];
        $data['data'] = [];
        return view('Tenancy.Profile.Views.V5.webhookSetting')->with('data', (object) $data);
    }

    public function postWebhookSetting(){
        $input = \Request::all();
        $varObj = Variable::NotDeleted()->where('var_key','WEBHOOK_ON')->first();
        if($varObj == null){
            $varObj = new Variable;
            $varObj->var_key = 'WEBHOOK_ON';
            $varObj->var_value = isset($input['webhook_on']) && !empty($input['webhook_on']) ? 1 : 0;
            $varObj->created_at = DATE_TIME;
            $varObj->created_by = USER_ID;
            $varObj->save();
        }else{
            $varObj->var_value = isset($input['webhook_on']) && !empty($input['webhook_on']) ? 1 : 0;
            $varObj->updated_at = DATE_TIME;
            $varObj->updated_by = USER_ID;
            $varObj->save();
        }

        $varObj = Variable::NotDeleted()->where('var_key','WEBHOOK_URL')->first();
        if($varObj == null){
            $varObj = new Variable;
            $varObj->var_key = 'WEBHOOK_URL';
            $varObj->var_value = $input['webhook_url'];
            $varObj->created_at = DATE_TIME;
            $varObj->created_by = USER_ID;
            $varObj->save();
        }else{
            $varObj->var_value = $input['webhook_url'];
            $varObj->updated_at = DATE_TIME;
            $varObj->updated_by = USER_ID;
            $varObj->save();
        }

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function apiGuide(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.api_guide'),
            'icon' => 'fas fa-code',
        ];
        $data['data'] = $userObj;
        return view('Tenancy.Profile.Views.apiGuide')->with('data', (object) $data);
    }

    public function uploadImage(Request $request,$id=false){
        $rand = rand() . date("YmdhisA");
        if ($request->hasFile('file')) {
            $files = $request->file('file');
            Storage::put($rand,$files);
            Session::put('photos',$rand);
            return \TraitsFunc::SuccessResponse('');
        }
    }

    public function addImage($images,$nextID=false){
        $fileName = \ImagesHelper::UploadFile('users', $images, $nextID);
        if($fileName == false){
            return false;
        }
        return $fileName;        
    }

    public function deleteImage(){
        $id = (int) USER_ID;
        $input = \Request::all();

        $menuObj = User::find($id);
        if($menuObj == null) {
            return \TraitsFunc::ErrorMessage(trans('main.userNotFound'));
        }

        \ImagesHelper::deleteDirectory(public_path('/').'/uploads/users/'.$id.'/'.$menuObj->image);
        $menuObj->image = '';
        $menuObj->save();
        return \TraitsFunc::SuccessResponse(trans('main.imgDeleted'));
    }

}
