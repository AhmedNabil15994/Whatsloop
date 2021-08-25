<?php namespace App\Http\Controllers;

use Request;
use Response;
use URL;
use Session;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class PayTabsControllers extends Controller {

    use \TraitsFunc;

    public function validateInputByMethod($input,$method='main'){
        $extraRules = [];
        $extraMessages = [];

        $mainRules = [
            'returnURL' => 'required',
            'cart_id' => 'required',
            'cart_amount' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            // 'street' => 'required',
            // 'state' => 'required',
            // 'city' => 'required',
            // 'country' => 'required',
            // 'postal_code' => 'required',
        ];

        $mainMessages = [
            'returnURL.required' => "Sorry Return URL is Required",
            'cart_id.required' => "Sorry Cart ID is Required",
            'cart_amount.required' => "Sorry Pay Amount is Required",
            'name.required' => "Sorry Name is Required",
            'email.required' => "Sorry Email is Required",
            'phone.required' => "Sorry Phone is Required",
            // 'street.required' => "Sorry Street is Required",
            // 'state.required' => "Sorry State is Required",
            // 'city.required' => "Sorry City is Required",
            // 'country.required' => "Sorry Country is Required",
            // 'postal_code.required' => "Sorry Postal Code is Required",
        ];

        // if($method == 'main'){

        // }elseif($method == ''){

        // }

        $rules = array_merge($mainRules,$extraRules);
        $message = array_merge($mainMessages,$extraMessages);

        return \Validator::make($input, $rules, $message);
    }

    public function index(){
        $input = \Request::all();

        $input['paypage_lang'] = isset($input['paypage_lang']) && !empty($input['paypage_lang']) ? $input['paypage_lang'] : 'ar'; 
        $input['street'] = isset($input['street']) && !empty($input['street']) ? $input['street'] : 'street2'; 
        $input['city'] = isset($input['city']) && !empty($input['city']) ? $input['city'] : 'jeddah'; 
        $input['state'] = isset($input['state']) && !empty($input['state']) ? $input['state'] : 'jeddah'; 
        $input['country'] = isset($input['country']) && !empty($input['country']) ? $input['country'] : 'KSA'; 
        $input['postal_code'] = isset($input['postal_code']) && !empty($input['postal_code']) ? $input['postal_code'] : '23324'; 
        $input['cart_description'] = isset($input['cart_description']) && !empty($input['cart_description']) ? $input['cart_description'] : 'Description of the items/services'; 
        
        $validate = $this->validateInputByMethod($input);
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $date = date('Y-m-d H:i:s');

        $data = [
            "cart_id" => $input['cart_id'],
            "cart_amount" => $input['cart_amount'],
            "cart_description" => $input['cart_description'],
            "paypage_lang" => $input['paypage_lang'],
            "customer_details" => [
                "name" => $input['name'],
                "email" => $input['email'],
                "phone" => $input['phone'],
                "street1" => $input['street'],
                "city" => $input['city'],
                "state" => $input['state'],
                "country" => $input['country'],
                "zip" => $input['postal_code']
            ],
            "shipping_details" => [
                "name" => $input['name'],
                "email" => $input['email'],
                "phone" => $input['phone'],
                "street1" => $input['street'],
                "city" => $input['city'],
                "state" => $input['state'],
                "country" => $input['country'],
                "zip" => $input['postal_code']
            ],


            'profile_id' => PROFILE_ID,
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            "cart_currency" => "SAR",
            "return" => URL::to('/paytabs/testResult?date='.$date),
            "callback" => "",
            "user_defined" => [
                "udf3" => "UDF3 Test3",
                "udf9" => "UDF9 Test9"
            ]
        ];
        $paytabs = \Paytabs::hostedPayment($data);
        $paytabs = json_decode($paytabs);
        $paytabs = (array) $paytabs;

        if(isset($paytabs['tran_ref']) && !empty($paytabs['tran_ref'])){

            Transaction::insert([
                'tran_ref' => $paytabs['tran_ref'],
                'ip_address' => \Request::ip(),
                'type' => 'PayTabs',
                'profile_id' => PROFILE_ID,
                'server_key' => SERVER_KEY,
                'returnURL' => $input['returnURL'],
                'created_at' => $date,
            ]);

            $dataList['data'] = $paytabs;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($paytabs['message']);
        }
    }

    public function testResult(){
        $input = \Request::all();
        $transactionObj = Transaction::where('type','PayTabs')->where('created_at',$input['date'])->orderBy('created_at','DESC')->first();

        $data = [
            'profile_id' => $transactionObj->profile_id,
            'tran_ref' => $transactionObj->tran_ref,
        ];

        $paytabs = \Paytabs::queryTransaction($data,$transactionObj->server_key);
        $paytabs = json_decode($paytabs);
        if(isset($paytabs->payment_result)){
            $dataList['data'] = $paytabs;
            $dataList['data']->paymentGateaway = 'paytabs';
            if($paytabs->payment_result->response_status == 'A'){
                $dataList['status'] = \TraitsFunc::SuccessMessage();
            }else{                
                $dataList['status'] = \TraitsFunc::ErrorMessage($paytabs->payment_result->response_message)->original->status;
            }
            $transactionObj->response_status = $paytabs->payment_result->response_status;
            $transactionObj->save();

            return \Helper::RedirectWithPostForm((array)$dataList,$transactionObj->returnURL);
        }        
    }

    public function success()
    {
        $data = Request::all();
        $data['data'] = json_decode($data['data']);
        $data['status'] = json_decode($data['status']);
        dd($data);        
    }

}
