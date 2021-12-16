<?php namespace App\Http\Controllers;

use Request;
use Response;
use URL;
use Session;
use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class NoonControllers extends Controller {

    use \TraitsFunc;

    public function validateInputByMethod($input,$method='main'){
        $extraRules = [];
        $extraMessages = [];

        $mainRules = [
            'returnURL' => 'required',
            'cart_id' => 'required',
            'cart_amount' => 'required',
        ];

        $mainMessages = [
            'returnURL.required' => "Sorry Return URL is Required",
            'cart_id.required' => "Sorry Cart ID is Required",
            'cart_amount.required' => "Sorry Pay Amount is Required",
        ];

        if($method == 'newSubscription'){
            $extraRules = [
                'subs_name' => 'required',
                'subs_valid_till' => 'required',
                'subs_type' => 'required',
            ];
            $extraMessages = [
                'subs_name.required' => "Sorry Subscription Name is Required",
                'subs_valid_till.required' => "Sorry Subscription Valid Till is Required",
                'subs_type.required' => "Sorry Subscription Type is Required",
            ];
        }elseif($method == 'recurSubscription'){
            $extraRules = [
                'name' => 'required',
                'description' => 'required',
                'subscriptionId' => 'required',
            ];


            $extraMessages = [
                'name.required' => "Sorry Order Name is Required",
                'description.required' => "Sorry Order Description is Required",
                'subscriptionId.required' => "Sorry Subscription Identifier is Required",
            ];
        }elseif($method == 'retrieveSubscription' || $method == 'cancelSubscription'){
            $mainRules = [
                'subscriptionId' => 'required',
            ];

            $extraMessages = [
                'subscriptionId.required' => "Sorry Subscription Identifier is Required",
            ];
        }

        $rules = array_merge($mainRules,$extraRules);
        $message = array_merge($mainMessages,$extraMessages);

        return \Validator::make($input, $rules, $message);
    }

    public function index(){
        $input = \Request::all();
        $payType = isset($input['payType']) && !empty($input['payType']) ? $input['payType'] : 'pay';

        $input['paypage_lang'] = isset($input['paypage_lang']) && !empty($input['paypage_lang']) ? $input['paypage_lang'] : 'ar'; 
        $input['street'] = isset($input['street']) && !empty($input['street']) ? $input['street'] : 'street2'; 
        $input['city'] = isset($input['city']) && !empty($input['city']) ? $input['city'] : 'jeddah'; 
        $input['state'] = isset($input['state']) && !empty($input['state']) ? $input['state'] : 'jeddah'; 
        $input['country'] = isset($input['country']) && !empty($input['country']) ? $input['country'] : 'SA'; 
        $input['postal_code'] = isset($input['postal_code']) && !empty($input['postal_code']) ? $input['postal_code'] : '23324'; 
        $input['description'] = isset($input['description']) && !empty($input['description']) ? $input['description'] : 'Sample order name'; 
       
        $validate = $this->validateInputByMethod($input);
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }
        $date = date('Y-m-d H:i:s');
        $data = [
            "apiOperation" => "INITIATE",
            "order" => [
                "reference" => $input['cart_id'],
                "amount" => $input['cart_amount'],
                "currency" => "SAR",
                "name" => $input['description'],
                "channel" => "web",
                "category" => $payType
            ],
            "configuration" => [
                "tokenizeCc" => "true",
                "returnUrl" => URL::to('/noon/pushResult?date='.strtotime($date)),
                "locale" => $input['paypage_lang'],
                "paymentAction" => "Sale"
            ],
            "billing" => [
                'address' => [
                    "street" => "7447 Al Ilham",
                    "city" => "Jeddah",
                    "stateProvince" => "Jeddah",
                    "country" => "SA",
                    "postalCode" => '23324',
                ],
                'contact' => [
                    "firstName" => "Ahmed",
                    "lastName" => "Nabil",
                    "phone" => "201069273925",
                    "mobilePhone" => "201069273925",
                    "email" => "ahmednabil15994@gmail.com"
                ],
            ],
        ];
        $noon = \Noon::hostedPayment($data);
        $noon = json_decode($noon);

        if(isset($noon->resultCode) && $noon->resultCode == 0 ){
            Transaction::insert([
                'tran_ref' => $noon->result->order->id,
                'ip_address' => \Request::ip(),
                'type' => 'Noon',
                'business_id' => BUSINESS_ID,
                'app_name' => APP_NAME,
                'app_key' => APP_KEY,
                'auth_key' => AUTH_KEY,
                'returnURL' => $input['returnURL'],
                'created_at' => $date,
            ]);

            $fullData = (array) $noon;
            $result = $fullData['result'];            

            $fullData['result']  = [ 
                'order' => $result->order,
                'redirect_url' => $result->checkoutData->postUrl
            ];

            $dataList['data'] = $fullData;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }
    }

    public function testResult(){
        $input = \Request::all();
        $transactionObj = Transaction::where('type','Noon')->where('created_at',date('Y-m-d H:i:s',$input['date']))->orderBy('created_at','DESC')->first();

        $data = [
            'auth_key' => $transactionObj->auth_key,
            'orderId' => $transactionObj->tran_ref,
        ];
        
        $noon = \Noon::queryTransaction($data);
        $noon = json_decode($noon);

        $fullData = (array) $noon;
        $order = $fullData['result']->order;     
        if(isset($fullData['result']->subscription)){
            $fullData['subscriptionId']  = $fullData['result']->subscription->identifier;
        }       
        unset($fullData['result']);

        $fullData['order']  = $order;

        if(isset($noon->resultCode) && $noon->resultCode == 0 ){
            if($noon->result->order->status == 'CAPTURED'){
                $dataList['status'] = \TraitsFunc::SuccessMessage();
            }else{
                $fullData['message'] = isset($noon->result->order->errorMessage) ? $noon->result->order->errorMessage : $noon->result->order->status;
                $dataList['status'] = \TraitsFunc::ErrorMessage(isset($noon->result->order->errorMessage) ? $noon->result->order->errorMessage : $noon->result->order->status)->original->status;
            }
            $dataList['data'] = $fullData;
            $dataList['data']['paymentGateaway'] = 'Noon';
            $dataList['data']['transaction_id'] = $transactionObj->tran_ref;

            $transactionObj->response_status = $noon->result->order->status;
            $transactionObj->save();
            return \Helper::RedirectWithPostForm((array)$dataList,$transactionObj->returnURL);
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }    
    }

    public function newSubscription(){
        $input = \Request::all();

        $input['paypage_lang'] = isset($input['paypage_lang']) && !empty($input['paypage_lang']) ? $input['paypage_lang'] : 'ar'; 
        $input['description'] = isset($input['description']) && !empty($input['description']) ? $input['description'] : 'Sample order name'; 
       
        $validate = $this->validateInputByMethod($input,'newSubscription');
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }


        $datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $input['subs_valid_till']);
        $validTill = $datetime->format(\DateTime::RFC3339); 
        $input['subs_valid_till'] = $validTill;

        $data = [
            "apiOperation" => "INITIATE",
            "order" => [
                "reference" => $input['cart_id'],
                "amount" => $input['cart_amount'],
                "currency" => "SAR",
                "name" => $input['description'],
                "channel" => "web",
                "category" => "pay"
            ],
            "configuration" => [
                "tokenizeCc" => "true",
                "returnUrl" => URL::to('/noon/pushResult'),
                "locale" => $input['paypage_lang'],
                "paymentAction" => "Sale"
            ],
            "subscription" => [
                "type" => $input['subs_type'] == 1 ? 'Unscheduled' : 'Recurring' ,
                "maxAmount" => isset($input['subs_max_amount']) && !empty($input['subs_max_amount']) ? $input['subs_max_amount'] : 1,
                "name" => $input['subs_name'],
                "validTill" => $input['subs_valid_till']
            ]
        ];
        $noon = \Noon::hostedPayment($data);
        $noon = json_decode($noon);
        if(isset($noon->resultCode) && $noon->resultCode == 0 ){
            Transaction::insert([
                'tran_ref' => $noon->result->order->id,
                'ip_address' => \Request::ip(),
                'type' => 'Noon',
                'business_id' => BUSINESS_ID,
                'app_name' => APP_NAME,
                'app_key' => APP_KEY,
                'auth_key' => AUTH_KEY,
                'returnURL' => $input['returnURL'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $fullData = (array) $noon;
            $result = $fullData['result'];            

            $fullData['result']  = [ 
                'order' => $result->order,
                'redirect_url' => $result->checkoutData->postUrl
            ];

            $dataList['data'] = $fullData;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }
    }

    public function recurSubscription(){
        $input = \Request::all();

        $input['paypage_lang'] = isset($input['paypage_lang']) && !empty($input['paypage_lang']) ? $input['paypage_lang'] : 'ar'; 
        $input['description'] = isset($input['description']) && !empty($input['description']) ? $input['description'] : ''; 
       
        $validate = $this->validateInputByMethod($input,'recurSubscription');
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $data = [
            "apiOperation" => "INITIATE",
            "order" => [
                "reference" => $input['cart_id'],
                "amount" => $input['cart_amount'],
                "name" => $input['name'],
                "description" => $input['description'],
                "channel" => "web",
            ],
            "configuration" => [
                "returnUrl" => URL::to('/noon/pushResult'),
                "locale" => $input['paypage_lang'],
                "paymentAction" => "Sale"
            ],
            "paymentData" => [
                "type" => "Subscription",
                "data" => [
                    "subscriptionIdentifier" => $input['subscriptionId'],
                ],
            ]
        ];
        $noon = \Noon::hostedPayment($data);
        $noon = json_decode($noon);

        if(isset($noon->resultCode) && $noon->resultCode == 0 ){
            Transaction::insert([
                'tran_ref' => $noon->result->order->id,
                'ip_address' => \Request::ip(),
                'type' => 'Noon',
                'business_id' => BUSINESS_ID,
                'app_name' => APP_NAME,
                'app_key' => APP_KEY,
                'auth_key' => AUTH_KEY,
                'returnURL' => $input['returnURL'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $fullData = (array) $noon;
            $result = $fullData['result'];            

            $fullData['result']  = [ 
                'order' => $result->order,
            ];

            $dataList['data'] = $fullData;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }
    }

    public function mitUnschedSubscription(){
        $input = \Request::all();

        $input['paypage_lang'] = isset($input['paypage_lang']) && !empty($input['paypage_lang']) ? $input['paypage_lang'] : 'ar'; 
        $input['description'] = isset($input['description']) && !empty($input['description']) ? $input['description'] : ''; 
       
        $validate = $this->validateInputByMethod($input,'recurSubscription');
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $data = [
            "apiOperation" => "INITIATE",
            "order" => [
                "reference" => $input['cart_id'],
                "amount" => $input['cart_amount'],
                "name" => $input['name'],
                "description" => $input['description'],
                "channel" => "web",
            ],
            "configuration" => [
                "returnUrl" => URL::to('/noon/pushResult'),
                "locale" => $input['paypage_lang'],
                "paymentAction" => "Sale"
            ],
            "paymentData" => [
                "type" => "Subscription",
                "data" => [
                    "subscriptionIdentifier" => $input['subscriptionId'],
                ],
            ]
        ];
        $noon = \Noon::hostedPayment($data);
        $noon = json_decode($noon);

        if(isset($noon->resultCode) && $noon->resultCode == 0 ){
            Transaction::insert([
                'tran_ref' => $noon->result->order->id,
                'ip_address' => \Request::ip(),
                'type' => 'Noon',
                'business_id' => BUSINESS_ID,
                'app_name' => APP_NAME,
                'app_key' => APP_KEY,
                'auth_key' => AUTH_KEY,
                'returnURL' => $input['returnURL'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $fullData = (array) $noon;
            $result = $fullData['result'];            

            $fullData['result']  = [ 
                'order' => $result->order,
            ];

            $dataList['data'] = $fullData;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }
    }

    public function retrieveSubscription(){
        $input = \Request::all();

       
        $validate = $this->validateInputByMethod($input,'retrieveSubscription');
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $data = [
            "apiOperation" => "RETRIEVE_SUBSCRIPTION",
            "subscription" => [
                "identifier" => $input['subscriptionId'],
            ],
        ];
        $noon = \Noon::hostedPayment($data);
        $noon = json_decode($noon);
        if(isset($noon->resultCode) && $noon->resultCode == 0 ){

            $fullData = (array) $noon;
            $result = $fullData['result'];            

            $fullData['result']  = [ 
                'subscription' => $result->subscription,
            ];

            $dataList['data'] = $fullData;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }
    }

    public function cancelSubscription(){
        $input = \Request::all();

       
        $validate = $this->validateInputByMethod($input,'cancelSubscription');
        if($validate->fails()){
            return \TraitsFunc::ErrorMessage($validate->messages()->first());
        }

        $data = [
            "apiOperation" => "CANCEL_SUBSCRIPTION",
            "subscription" => [
                "identifier" => $input['subscriptionId'],
            ],
        ];
        $noon = \Noon::hostedPayment($data);
        $noon = json_decode($noon);
        if(isset($noon->resultCode) && $noon->resultCode == 0 ){

            $fullData = (array) $noon;
            $result = $fullData['result'];            

            $fullData['result']  = [ 
                'subscription' => $result->subscription,
            ];

            $dataList['data'] = $fullData;
            $dataList['status'] = \TraitsFunc::SuccessResponse();
            return \Response::json((object) $dataList);   
        }else{
            return \TraitsFunc::ErrorMessage($noon->message);
        }
    }

    public function success()
    {
        $data = Request::all();
        $newData = [];
        $newData['data'] = json_decode($data['data']);
        $newData['status'] = json_decode($data['status']);
        dd($newData);        
    }

}
