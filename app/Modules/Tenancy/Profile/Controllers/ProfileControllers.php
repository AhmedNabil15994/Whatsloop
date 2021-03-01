<?php namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\Models\WebActions;
use App\Models\Variable;
use App\Models\PaymentInfo;
use Storage;
use Validator;


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
        return view('Tenancy.Profile.Views.personalInfo')->with('data', (object) $data);
    }

    public function updatePersonalInfo(){
        $input = \Request::all();
        $mainUserObj = User::getOne(USER_ID);

        if(isset($input['email']) && !empty($input['email'])){
            $userObj = User::checkUserBy('email',$input['email'],USER_ID);
            if($userObj){
                Session::flash('error', trans('main.emailError'));
                return redirect()->back()->withInput();
            }
            $mainUserObj->email = $input['email'];
        }
        if(isset($input['phone']) && !empty($input['phone'])){
            $userObj = User::checkUserBy('phone','+'.$input['phone'],USER_ID);
            if($userObj){
                Session::flash('error', trans('main.phoneError'));
                return redirect()->back()->withInput();
            }
            $mainUserObj->phone = '+'.$input['phone'];
        }

        if(isset($input['company']) && !empty($input['company'])){
            $mainUserObj->company = $input['company'];
        }

        if(isset($input['name']) && !empty($input['name'])){
            $mainUserObj->name = $input['name'];
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

        Session::forget('photos');
        WebActions::newType(2,'User');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function changePassword(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.changePassword'),
            'icon' => 'fas fa-user-lock',
        ];
        $data['data'] = $userObj;
        return view('Tenancy.Profile.Views.changePassword')->with('data', (object) $data);
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

    public function paymentInfo(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.payment_setting'),
            'icon' => 'mdi mdi-credit-card',
        ];
        $data['data'] = $userObj;
        $data['paymentInfo'] = $userObj->paymentInfo ;
        return view('Tenancy.Profile.Views.paymentInfo')->with('data', (object) $data);
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
        $paymentInfoObj->region = $input['region'];
        $paymentInfoObj->country = $input['country'];
        $paymentInfoObj->postal_code = $input['postal_code'];
        $paymentInfoObj->save();

        WebActions::newType($type,'PaymentInfo');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function taxInfo(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.tax_setting'),
            'icon' => 'mdi mdi-percent',
        ];
        $data['data'] = $userObj;
        $data['paymentInfo'] = $userObj->paymentInfo ;
        return view('Tenancy.Profile.Views.taxInfo')->with('data', (object) $data);
    }

    public function postTaxInfo(){
        $input = \Request::all();
        $userObj = User::getOne(USER_ID);
        if($userObj->PaymentInfo){
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

        if(isset($input['company']) && !empty($input['company']) && $userObj->company != $input['company']){
            $userObj->company = $input['company'];
            $userObj->save();
        }

        if(isset($input['tax_id']) && !empty($input['tax_id']) && $paymentInfoObj->tax_id != $input['tax_id']){
            $paymentInfoObj->tax_id = $input['tax_id'];
            $paymentInfoObj->save();
        }

        WebActions::newType($type,'PaymentInfo');
        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function notifications(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.notifications'),
            'icon' => 'mdi mdi-alert-octagram-outline',
        ];
        $data['data'] = $userObj;
        return view('Tenancy.Profile.Views.notifications')->with('data', (object) $data);
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

    public function offers(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.offers'),
            'icon' => 'mdi mdi-offer',
        ];
        $data['data'] = $userObj;
        return view('Tenancy.Profile.Views.offers')->with('data', (object) $data);
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

    public function services(){
        $data['designElems']['mainData'] = [
            'title' => trans('main.service_tethering'),
            'icon' => 'mdi mdi-lan-connect',
        ];
        return view('Tenancy.Profile.Views.services')->with('data', (object) $data);
    }

    public function updateSalla(){
        $input = \Request::all();
        $rules = [
            'store_token' => 'required',
        ];

        $message = [
            'store_token.required' => trans('main.storeTokenValidation'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
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

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function updateZid(){
        $input = \Request::all();

        $rules = [
            'store_token' => 'required',
            'store_id' => 'required',
            'merchant_token' => 'required',
        ];

        $message = [
            'store_token.required' => trans('main.storeTokenValidation'),
            'store_id.required' => trans('main.storeIDValidation'),
            'merchant_token.required' => trans('main.merchantTokenValidation'),
        ];

        $validate = Validator::make($input, $rules, $message);
        if($validate->fails()){
            Session::flash('error', $validate->messages()->first());
            return back()->withInput();
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
            $sallaObj->var_value = $input['store_token'];
            $sallaObj->updated_at = DATE_TIME;
            $sallaObj->updated_by = USER_ID;
            $sallaObj->save();
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
            $sallaObj->var_value = $input['store_id'];
            $sallaObj->updated_at = DATE_TIME;
            $sallaObj->updated_by = USER_ID;
            $sallaObj->save();
        }

        $zidMerchantToken = Variable::NotDeleted()->where('var_key','ZidMerchantToken')->first();
        if($zidMerchantToken == null){
            $zidMerchantToken = new Variable;
            $zidMerchantToken->var_key = 'ZidMerchantToken';
            $zidMerchantToken->var_value = $input['merchant_token'];
            $zidMerchantToken->created_at = DATE_TIME;
            $zidMerchantToken->created_by = USER_ID;
            $ZidMerchantToken->save();
        }else{
            $sallaObj->var_value = $input['merchant_token'];
            $sallaObj->updated_at = DATE_TIME;
            $sallaObj->updated_by = USER_ID;
            $sallaObj->save();
        }

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function subscription(){
        $userObj = User::authenticatedUser();
        $data['designElems']['mainData'] = [
            'title' => trans('main.subscriptionManage'),
            'icon' => 'fa fa-cogs',
        ];
        $data['data'] = $userObj;
        return view('Tenancy.Profile.Views.subscription')->with('data', (object) $data);
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
