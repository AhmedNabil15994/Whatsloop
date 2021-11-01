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
use App\Jobs\SyncOldClient;
use App\Jobs\NewClient;
use Http;

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
            dispatch(new SyncOldClient($userObj,$requiredSync));
        }
        Session::put('is_old',0);
        Session::flash('success',trans('main.inPrgo'));
        return redirect()->to('/menu');
    }

    public function packages(){   
        $input = \Request::all();

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

        $data['user'] = User::getOne(USER_ID);
        $data['countries'] = countries();
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
        
        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;

        $data['data'] = $testData;
        $data['totals'] = $input['totals'];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['bankAccounts'] = BankAccount::dataList(1)['data'];
        $data['user'] = User::getOne(USER_ID);
        $data['countries'] = countries();
        $data['regions'] = [];
        if(!empty($data['payment']) && $data['payment']->country){
            $egypt = country($data['payment']->country); 
            $data['regions'] = $egypt->getDivisions(); 
        }
        
        return view('Tenancy.Dashboard.Views.V5.checkout')->with('data',(object) $data);
    }

    public function getCities(){
        $input = \Request::all();
        $egypt = country($input['id']); 

        $statusObj['regions'] = $egypt->getDivisions();
        $statusObj['status'] = \TraitsFunc::SuccessMessage();
        return \Response::json((object) $statusObj);
    }

    public function calcData($total,$cartData,$userObj){
        $total = json_decode($total);
        $totals = $total[3];

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

        // if($input['payType'] == 2){ // Paytabs Integration
        //     $profileId = '49334';
        //     $serverKey = 'SWJNLRLRKG-JBZZRMGMMM-GZKTBBLMNW';

        //     $dataArr = [
        //         'returnURL' => \URL::to('/pushInvoice'),
        //         'cart_id' => 'whatsloop-'.$userObj->id,
        //         'cart_amount' => $totals,
        //         'cart_description' => 'New',
        //         'paypage_lang' => LANGUAGE_PREF,
        //         'name' => $userObj->name,
        //         'email' => $userObj->email,
        //         'phone' => $userObj->phone,
        //         'street' => $paymentInfoObj->address,
        //         'city' => $paymentInfoObj->city,
        //         'state' => $paymentInfoObj->region,
        //         'country' => $paymentInfoObj->country,
        //         'postal_code' => $paymentInfoObj->postal_code,
        //     ];

        //     $extraHeaders = [
        //         'PROFILEID: '.$profileId,
        //         'SERVERKEY: '.$serverKey,
        //     ];

        //     $paymentObj = new \PaymentHelper();        
        //     $result = $paymentObj->hostedPayment($dataArr,'/paytabs',$extraHeaders);
        //     $result = json_decode($result);

        //     return redirect()->away($result->data->redirect_url);

        // }
        $url = \URL::to('/pushInvoice');
        if(isset($input['dataType']) && $input['dataType'] == 2){
            $url = \URL::to('/pushInvoice2');
            $nextStartMonth = date('Y-m-d',strtotime('first day of +1 month',strtotime(date('Y-m-d'))));

            Variable::where('var_key','endDate')->firstOrCreate([
                'var_key' => 'endDate',
                'var_value' => $nextStartMonth, 
            ]);
            Variable::where('var_key','start_date')->firstOrCreate([
                'var_key' => 'start_date',
                'var_value' => date('Y-m-d'), 
            ]);
        }
        if($input['payType'] == 2){// Noon Integration
            $urlSecondSegment = '/noon';
            $noonData = [
                'returnURL' => $url ,
                // 'returnURL' => \URL::to('/pushInvoice'),  // For Local 
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
            
            if(isset($request->name) && !empty($request->name)){

                $names = explode(' ',$request->name);
                if(count($names) < 2){
                    return \TraitsFunc::ErrorMessage(trans('main.name2Validate'));
                }

                $userObj->name = $request->name;
                $userObj->save();

                $centralUser->name = $request->name;
                $centralUser->save();
            }

            if(isset($request->company_name) && !empty($request->company_name)){
                $userObj->company = $request->company_name;
                $userObj->save();

                $centralUser->company = $request->company_name;
                $centralUser->save();
            }

            $files = $request->file('transfer_image');

            $bankTransferObj = BankTransfer::NotDeleted()->where('user_id',USER_ID)->where('status',1)->first();
            if(!$bankTransferObj){
                $bankTransferObj = new BankTransfer;
                $bankTransferObj->user_id = USER_ID;
                $bankTransferObj->tenant_id = TENANT_ID;
                $bankTransferObj->global_id = GLOBAL_ID;
                $bankTransferObj->domain = DOMAIN;
                $bankTransferObj->order_no = rand(1,100000);
                $bankTransferObj->status = 1;
                $bankTransferObj->sort = BankTransfer::newSortIndex();
                $bankTransferObj->created_at = DATE_TIME;
                $bankTransferObj->created_by = USER_ID;
            }
            $bankTransferObj->total = $request->total;
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
            \Session::flash('error',$data['status']->message);
            return redirect()->to('/');
        }
    }

    public function activate($transaction_id = null , $paymentGateaway = null){
        $cartObj = Variable::getVar('cartObj');
        $cartObj = json_decode(json_decode($cartObj));
        // dd($cartObj);

        dispatch(new NewClient($cartObj,'new',$transaction_id,$paymentGateaway,date('Y-m-d')));
        // $paymentObj = new \SubscriptionHelper(); 
        // $resultData = $paymentObj->newSubscription($cartObj,'new',$transaction_id,$paymentGateaway,date('Y-m-d'));   
        // if($resultData[0] == 0){
            // Session::flash('error',$resultData[1]);
            // return back()->withInput();
        // }         


        // Session::forget('userCredits');
        $userObj = User::first();
        User::setSessions($userObj);

        Session::put('hasJob',1);
        return redirect()->to('/dashboard');
    }

    public function completeJob(){
        Session::forget('hasJob');
        Session::forget('userCredits');
        
        $userObj = User::first();
        User::setSessions($userObj);
        return redirect()->to('/QR');
    }

    public function qrIndex(){
        $varObj = Variable::getVar('QRIMAGE');
        if($varObj){
            $data['qrImage'] = mb_convert_encoding($varObj, 'UTF-8', 'UTF-8');
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


        if((!isset($data['qrImage']) || $data['qrImage'] == null) && empty($userAddonsTutorial)){
            return redirect('dashboard');
        }

        $data['data'] = array_values($userAddonsTutorial);
        $names = Addons::NotDeleted()->whereIn('id',$data['data'])->pluck('title_'.LANGUAGE_PREF);
        $data['dataNames'] = reset($names);
        $data['channelName'] = trans('main.channel'). ' #'.Session::get('channelCode');
        $data['dis'] = 0;
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
                $usedCost = ($dataObj->monthly_after_vat / 30) * $usedDays;
            }else if($oldDuration == 2){
                $usedCost = ($dataObj->annual_after_vat / 365) * $usedDays;
                $newPriceAfterVat = $dataObj->annual_after_vat;
            }

            $data['userCredits'] = round($newPriceAfterVat - $usedCost,2);
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
            $testData[$key][6] = $one[3] == 1 ? $dataObj->monthly_after_vat : $dataObj->annual_after_vat;
            $total+= $testData[$key][6] * (int)$testData[$key][7];
        }
            
        $input['totals'] = json_decode($input['totals']);
        $input['totals'][3] = $total;
        $data['data'] = $testData;
        $data['totals'] = $input['totals'];
        $data['payment'] = PaymentInfo::where('user_id',USER_ID)->first();
        $data['user'] = User::first();
        $data['countries'] = countries();
        $data['regions'] = [];
        if(!empty($data['payment']) && $data['payment']->country){
            $egypt = country($data['payment']->country); 
            $data['regions'] = $egypt->getDivisions(); 
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
