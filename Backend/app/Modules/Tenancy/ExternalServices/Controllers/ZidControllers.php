<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\ModTemplate;
use App\Models\User;
use App\Models\Variable;
use App\Models\Template;
use App\Models\CentralVariable;
use App\Jobs\AbandonedCart;
use App\Models\UserAddon;
use App\Models\ModNotificationReport;
use DB;
use DataTables;

class ZidControllers extends Controller {

    use \TraitsFunc;
    public $service = 'zid';

    public function checkPerm(){
        $disabled = UserAddon::getDeactivated(User::first()->id);
        $dis = 0;
        if(in_array(4,$disabled)){
            $dis = 1;
        }
    }

    public function customers(Request $request){
        $input = \Request::all();
        $modelName = 'customers';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = CentralVariable::getVar('ZidMerchantToken');
        $managerToken = Variable::getVar('ZidStoreToken');
        
        $dataURL = $baseUrl.'/managers/store/'.$modelName.'/'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $managerToken,
            "STORE-ID" => $storeID,
            "ROLE" => 'Manager',
            'User-Agent' => 'whatsloop/1.00.00 (web)',
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $storeToken,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';
        $externalHelperObj = new \ExternalServices($dataArr);
        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            $externalHelperObj->startFuncs();
        }

        if($refresh == 'refresh'){
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }
        
        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }

    public function products(Request $request){
        $input = \Request::all();
        $modelName = 'products';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = CentralVariable::getVar('ZidMerchantToken');
        $managerToken = Variable::getVar('ZidStoreToken');
        
        $dataURL = $baseUrl.'/'.$modelName.'/'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $managerToken,
            "STORE-ID" => $storeID,
            "ROLE" => 'Manager',
            'User-Agent' => 'whatsloop/1.00.00 (web)',
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $storeToken,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';
        $externalHelperObj = new \ExternalServices($dataArr);
        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            $externalHelperObj->startFuncs();
        }

        if($refresh == 'refresh'){
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }

        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }

    public function orders(Request $request){
        $input = \Request::all();
        $modelName = 'orders';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = CentralVariable::getVar('ZidMerchantToken');
        $managerToken = Variable::getVar('ZidStoreToken');
        
        $dataURL = $baseUrl.'/managers/store/'.$modelName.'/'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $managerToken,
            "STORE-ID" => $storeID,
            "ROLE" => 'Manager',
            'User-Agent' => 'whatsloop/1.00.00 (web)',
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $storeToken,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';
        $externalHelperObj = new \ExternalServices($dataArr);
        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            $externalHelperObj->startFuncs();
        }

        if($refresh == 'refresh'){
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }

        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }

    public function sendAbandoned(){
        $input = \Request::all();
        if(!isset($input['message']) || empty($input['message'])){
            return  \TraitsFunc::ErrorMessage(trans('main.messageValidate'));
        }

        if(!isset($input['clientsData']) || empty($input['clientsData'])){
            return  \TraitsFunc::ErrorMessage(trans('main.clientsValidate'));
        }

        Template::where('name_en','abandonedCarts')->update([
            'description_ar' => $input['message'],
        ]);
        
        if($input['sendTime'] == 1){
            try {
                dispatch(new AbandonedCart(2,$input))->onConnection('cjobs');
            } catch (Exception $e) {
                
            }
        }else{
            $now = \Carbon\Carbon::now();
            $sendDate = \Carbon\Carbon::parse($input['sendTime']);
            $diff =  $sendDate->diffInSeconds($now);
            $on = \Carbon\Carbon::now()->addSeconds($diff);   
            try {
                dispatch(new AbandonedCart(2,$input))->onConnection('cjobs')->delay($on);
            } catch (Exception $e) {
                
            }
        }
        
        return \TraitsFunc::SuccessResponse(trans('main.inPrgo'));
    }

    public function abandonedCarts(Request $request){
        $tempObj = Template::where('name_en','abandonedCarts')->first();
        if(!$tempObj){
            Template::create([
                'channel' => \Session::get('channelCode'),
                'name_ar' => 'abandonedCarts',
                'name_en' => 'abandonedCarts',
                'description_ar' => 'يااهلا بـ {CUSTOMERNAME} 😍

    سلتك المتروكة رقم ( {ORDERID} ) والاجمالي ({ORDERTOTAL}) 😎.

    اذا ما عليك امر تتوجه الي صفحة مراجعة طلبك 😊 من خلال الرابط التالي :

    ( {ORDERURL} )

    مع تحيات فريق عمل واتس لوب ❤️',
                'description_en' => 'يااهلا بـ {CUSTOMERNAME} 😍

    سلتك المتروكة رقم ( {ORDERID} ) والاجمالي ({ORDERTOTAL}) 😎.

    اذا ما عليك امر تتوجه الي صفحة مراجعة طلبك 😊 من خلال الرابط التالي :

    ( {ORDERURL} )

    مع تحيات فريق عمل واتس لوب ❤️',
                'status' => 1,
            ]);
        } 
        

        $input = \Request::all();
        $modelName = 'abandonedCarts';
        $service = $this->service;

        $baseUrl = CentralVariable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = CentralVariable::getVar('ZidMerchantToken');
        $managerToken = Variable::getVar('ZidStoreToken');
        
        $dataURL = $baseUrl.'/managers/store/abandoned-carts'; 

        $tableName = $service.'_'.$modelName;

        $myHeaders = [
            "X-MANAGER-TOKEN" => $managerToken,
            "STORE-ID" => $storeID,
            "ROLE" => 'Manager',
            'User-Agent' => 'whatsloop/1.00.00 (web)',
        ];

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $storeToken,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
            'params' => [
                'page' => 1,
                'page_size' => 100,
            ],
        ];

        $refresh = isset($input['refresh']) && !empty($input['refresh']) ? $input['refresh'] : '';
        $externalHelperObj = new \ExternalServices($dataArr);
        if ((!Schema::hasTable($tableName) || $refresh == 'refresh') && !$this->checkPerm()) {
            $externalHelperObj->startFuncs();
        }

        if($refresh == 'refresh'){
            return redirect()->to('/services/'.$service.'/'.$modelName);
        }

        $ajaxCheck = $request->ajax();
        return $this->runModuleService($modelName,$tableName,$ajaxCheck);
    }


    public function runModuleService($model,$tableName,$ajaxCheck=0){
        $input = \Request::all();
        $service = $this->service;
        $paginationNo = isset($input['recordNumber']) && !empty($input['recordNumber']) ? $input['recordNumber'] : 15;

        if (Schema::hasTable($tableName)) {
            $source = DB::table($tableName);
        }else{
            $source = [];
        }
        
        if($model == 'customers'){
            // Begin Search
            if(isset($input['name']) && !empty($input['name'])){
                $source->where('first_name','LIKE','%'.$input['name'].'%')->orWhere('last_name','LIKE','%'.$input['name'].'%');
            }

            if(isset($input['email']) && !empty($input['email'])){
                $source->where('email',$input['email']);
            }

            if(isset($input['phone']) && !empty($input['phone'])){
                $source->where('mobile',$input['phone']);
            }

            if(isset($input['address']) && !empty($input['address'])){
                $source->where('city','LIKE','%'.$input['address'].'%');
            }

            if($ajaxCheck){
                if(isset($input['recordNumber']) && !empty($input['recordNumber'])){
                    $paginationNo = $input['recordNumber'];
                }
            }

            if(isset($input['keyword']) && !empty($input['keyword'])){
                $source->where('name','LIKE','%'.$input['keyword'].'%')->orWhere('email','LIKE','%'.$input['keyword'].'%')->orWhere('mobile','LIKE','%'.$input['keyword'].'%')->orWhere('city','LIKE','%'.$input['keyword'].'%');
            }

            $modelData = $source == [] ?  [] : ($paginationNo != 'all' ? $source->paginate($paginationNo) : $source->paginate($source->count()));
            $formattedData = $this->formatData($modelData,$model);
            
            $data['mainData'] = [
                'title' => trans('main.customers'),
                'url' => 'customers',
                'service' => $service,
                'icon' => ' fas fa-user-tie',
            ];

            $data['searchData'] = [
                'name' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.name_ar'),
                ],
                'phone' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.phone') .trans('main.noCountryCode'),
                ],
                'email' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.email'),
                ],
                'address' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.address'),
                ], 
            ];
        }elseif ($model == 'orders') {
            // Begin Search
            if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                $source->where('created_at','>=', $input['from'].' 00:00:00')->where('date','<=',$input['to']. ' 23:59:59');
            }
            if(isset($input['id']) && !empty($input['id'])){
                $source->where('id',$input['code']);
            }

            if(isset($input['status']) && !empty($input['status'])){
                $source->where('order_status','LIKE','%'.$input['status'].'%');
            }

            $modelData = $source == [] ?  [] : $source->orderBy('created_at','DESC')->paginate($paginationNo);
            $formattedData = $this->formatData($modelData,$model);
            
            $data['mainData'] = [
                'title' => trans('main.orders'),
                'url' => 'orders',
                'service' => $service,
                'icon' => 'mdi mdi-truck-delivery-outline',
            ];
            
            if (Schema::hasTable($service.'_order_status')) {
                $options = DB::table($service.'_order_status')->get();
            }else{
                $options = [
                    ['id'=>'جديد','name'=>'جديد'],
                    ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                    ['id'=>'جاهز','name'=>'جاهز'],
                    ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                    ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                    ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
                ];
            }

            $data['searchData'] = [
                'id' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.id'),
                ],
                'status' => [
                    'type' => 'select',
                    'class' => 'form-control',
                    'index' => '',
                    'options' => $options,
                    'label' => trans('main.status'),
                ],
                'from' => [
                    'type' => 'text',
                    'class' => 'form-control m-input datepicker',
                    'id' => 'datepicker1',
                    'label' => trans('main.dateFrom'),
                ],
                'to' => [
                    'type' => 'text',
                    'class' => 'form-control m-input datepicker',
                    'id' => 'datepicker2',
                    'label' => trans('main.dateTo'),
                ], 
            ];
        }elseif($model=='products'){

            if(isset($input['id']) && !empty($input['id'])){
                $source->where('id',$input['id']);
            }

            if(isset($input['name']) && !empty($input['name'])){
                $source->where('name','LIKE','%'.$input['name'].'%');
            }

            if(isset($input['price']) && !empty($input['price'])){
                if (strpos($input['price'], '||') !== false) {
                    $arr = explode('||', $input['price']);
                    $min = (int) $arr[0];
                    $max = (int) $arr[1];
                    $source->where('price','>=',$min)->where('price','<=',$max);
                }else{
                    $source->where('price',$input['price']);
                }
            }

            if(isset($input['status']) && !empty($input['status'])){
                $source->where('status',$input['status']);
            }

            $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
            $formattedData = $this->formatData($modelData,$model);
            
            $data['mainData'] = [
                'title' => trans('main.products'),
                'url' => 'products',
                'service' => $service,
                'icon' => ' fab fa-product-hunt',
            ];

            $options = [
                ['id'=>0,'name'=>trans('main.unAvail')],
                ['id'=>1,'name'=>trans('main.avail')]
            ];

            $data['searchData'] = [
                'id' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.id'),
                ],
                'name' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.name'),
                ],
                'price' => [
                    'type' => 'text',
                    'class' => 'form-control m-input',
                    'label' => trans('main.price'),
                ],
                'status' => [
                    'type' => 'select',
                    'class' => 'form-control',
                    'index' => '',
                    'options' => $options,
                    'label' => trans('main.status'),
                ],
            ];
        }elseif($model == 'abandonedCarts'){
            // Begin Search

            // if(isset($input['keyword']) && !empty($input['keyword'])){
            //     $source->where('first_name','LIKE','%'.$input['keyword'].'%')->orWhere('last_name','LIKE','%'.$input['keyword'].'%')->orWhere('email','LIKE','%'.$input['keyword'].'%')->orWhere('mobile','LIKE','%'.$input['keyword'].'%')->orWhere('country','LIKE','%'.$input['keyword'].'%')->orWhere('city','LIKE','%'.$input['keyword'].'%');
            // }
            $clients = [];
            $firstOrderObj = \DB::table('zid_orders')->first();
            if($firstOrderObj){
                $storeUrl = $firstOrderObj->store_url.'cart/view';
            }
            if(!empty($source)){
                foreach($source->orderBy('created_at','DESC')->get() as $oneItem){
                    $clients[] = [
                        'name' => $oneItem->customer_name,
                        'mobile' => $oneItem->customer_mobile,
                        'order_id' => $oneItem->cart_id,
                        'total' => $oneItem->cart_total_string,
                        'url' => $firstOrderObj ? $storeUrl : '',
                    ];
                }
            }

            $modelData = $source == [] ?  [] : $source->orderBy('created_at','DESC')->paginate($paginationNo);
            $formattedData = $this->formatData($modelData,$model);
            $data['mainData'] = [
                'title' => trans('main.abandonedCarts'),
                'url' => 'abandonedCarts',
                'service' => $service,
                'icon' => ' fas fa-user-tie',
            ];

            $data['searchData'] = [];
            $mainData['customers'] = $clients;
            $mainData['template'] = Template::where('name_en','abandonedCarts')->first();
        }

        $mainData['designElems'] = $data;
        $mainData['type'] = $model;
        $mainData['data'] = $formattedData;
        $mainData['dis'] = $this->checkPerm();

        if(!empty($formattedData)){
            $mainData['pagination'] = \Helper::GeneratePagination($modelData);
        }
        // dd($mainData);
        if($ajaxCheck){
            $returnHTML = view('Tenancy.ExternalServices.Views.V5.ajaxData')->with('data', (object) $mainData)->render();
            return response()->json( array('success' => true, 'html'=>$returnHTML) );
        }

        return view('Tenancy.ExternalServices.Views.V5.'.$model)->with('data', (object) $mainData);
    }

    public function formatData($data,$table,$extraData=null){
        $objs = [];
        foreach ($data as $key => $value) {
            $dataObj = new \stdClass();
            if($table == 'products'){
                $dataObj->id = $value->id;
                $name_ar = @unserialize($value->name)['ar'];
                $name_en = @unserialize($value->name)['en'];
                
                $price = $value->formatted_price;
                $images = @unserialize($value->images)[0]['image']['full_size'];
                $url = $value->html_url;
                $status = $value->is_published;
                $withTax = $value->is_taxable;
                $created_at = $value->created_at;
                $require_shipping = $value->requires_shipping;
                $categories = unserialize($value->categories);
                $categories_ar = [];
                $categories_en = [];
                foreach ($categories as $category) {
                    $categories_ar[]= isset($category['name']['ar']) && !empty($category['name']['ar']) ? $category['name']['ar'] : '' ;
                    $categories_en[]= isset($category['name']['en']) && !empty($category['name']['en']) ? $category['name']['en'] : '' ;
                }
                $dataObj->sku = $value->sku;
                $dataObj->quantity = $value->quantity;
                $dataObj->name_ar = $name_ar == null ? $name_en : $name_ar;
                $dataObj->name_en = $name_en == null ? $name_ar : $name_en;
                $dataObj->categories_ar = $categories_ar == [] ? $categories_en : $categories_ar;
                $dataObj->categories_en = $categories_en == [] ? $categories_ar : $categories_en;
                $dataObj->price = $price;
                $dataObj->images = $images;
                $dataObj->url = $url;
                $dataObj->status = $status;
                $dataObj->withTax = $withTax;
                $dataObj->require_shipping = $require_shipping;
                $dataObj->created_at = date('Y-m-d H:i:s',strtotime($created_at));
                $dataObj->updated_at = date('Y-m-d H:i:s',strtotime($value->updated_at));
            }elseif($table == 'customers'){
                $dataObj->id = $value->id;
                $dataObj->name = $value->name;
                $dataObj->phone = $value->mobile;
                $dataObj->email = $value->email;
                $dataObj->image = asset('images/not-available.jpg');
                $dataObj->city = isset($value->city) && !empty($value->city) ? unserialize($value->city)['name'] : '';
                $dataObj->country = isset($value->city) && !empty($value->city) ? unserialize($value->city)['country_name'] : '';
            }elseif($table == 'orders'){
                $status = unserialize($value->order_status);
                $dataObj->created_at = date('Y-m-d H:i:s', strtotime($value->created_at));
                $dataObj->id = $value->id;
                $dataObj->order_url = $value->order_url;
                $dataObj->total = $value->order_total_string;
                $dataObj->status = $status['name'];
                $dataObj->statusID = $status['code'];
                $dataObj->items = [['name'=>$value->store_name,'quantity'=>1]];
            }elseif($table == 'abandonedCarts'){
                $price = $value->cart_total;
                $customer = [
                    'name' => $value->customer_name,
                    'mobile' => $value->customer_mobile,
                    'email' => $value->customer_email,
                    'country' => '',
                ];

                $dataObj->id = $value->cart_id;
                $dataObj->reference_id = $value->id;
                $dataObj->store_id = $value->store_id;
                $dataObj->session_id = $value->session_id;
                $dataObj->order_id = $value->order_id;
                $dataObj->phase = $value->phase;
                $dataObj->customer = $customer;
                $dataObj->total = $value->cart_total_string;
                $dataObj->order_url = 'https://web.zid.sa/abandoned-cart/'.$value->store_id;
                $dataObj->reminders_count = $value->reminders_count;
                $dataObj->sent_count = $value->reminders_count;
                $dataObj->products_count = $value->products_count;
                $dataObj->items = trans('main.products_count') . $value->products_count;
                $dataObj->created_at = date('Y-m-d H:i:s',strtotime($value->created_at));
                $dataObj->updated_at = date('Y-m-d H:i:s',strtotime($value->updated_at));
            }
            $objs[] = $dataObj;
        }
        return $objs;
    }

    public function reports(Request $request){
        $service = $this->service;
       
        $data['designElems']['mainData'] = [
            'title' => trans('main.notReports'),
            'url' => 'services/'.$service.'/reports',
            'name' => 'reports',
            'service' => $service,
            'icon' => 'mdi mdi-file-account-outline',
        ];

        if (Schema::hasTable($service.'_order_status')) {
            $options = DB::table($service.'_order_status')->get();
        }else{
            $options = [
                ['id'=>'جديد','name'=>'جديد'],
                ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                ['id'=>'جاهز','name'=>'جاهز'],
                ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
                ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
            ];
        }

        $sendOptions =[
            ['id'=> 0 , 'name' => trans('main.notSent')],
            ['id'=> 1 , 'name' => trans('main.sentDone')],
        ];

        $extraTableData = [];
        $extraSearchData = [];
        
        $oldSearchData = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.id'),
                'index' => '0',
            ],
            'order_id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'index' => '1',
                'label' => trans('main.order'),
            ],
            'statusText' => [
                'type' => 'select',
                'class' => 'form-control',
                'id' => '',
                'index' => '',
                'options' => $options,
                'label' => trans('main.status'),
            ],
            'from' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'id' => '',
                'index' => '',
                'label' => trans('main.dateFrom'),
            ],
            'to' => [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'id' => '',
                'index' => '',
                'label' => trans('main.dateTo'),
            ],
        ];

        $oldTableData=  [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'order_id' => [
                'label' => trans('main.order'),
                'type' => '',
                'className' => '',
                'data-col' => 'order_id',
                'anchor-class' => '',
            ],  
            'statusText' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'statusText',
                'anchor-class' => '',
            ],   
            'created_at' => [
                'label' => trans('main.date'),
                'type' => '',
                'className' => '',
                'data-col' => 'created_at',
                'anchor-class' => '',
            ],   
        ];


        if($request->ajax()){
            $data = ModNotificationReport::dataList(2);
            return Datatables::of($data['data'])->make(true);
        }

        $data['designElems']['searchData'] = array_merge($oldSearchData,$extraSearchData); 
        $data['designElems']['tableData'] = array_merge($oldTableData,$extraTableData);
        return view('Tenancy.ExternalServices.Views.V5.reports')->with('data', (object) $data);
    }

    public function templates(Request $request){
        $service = $this->service;

        $userObj = User::find(USER_ID);
        $channels = [];
        $channelObj = new \stdClass();
        $channelObj->id = Session::get('channelCode');
        $channelObj->name = unserialize($userObj->channels)[0];
        $channels[] = $channelObj;

        $data['designElems']['mainData'] = [
            'title' => trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fas fa-envelope-open-text',
            'addOne' => trans('main.newTemplate'),
        ];

        $actives = [
            ['id'=>0,'name'=>trans('main.notActive')],
            ['id'=>1,'name'=>trans('main.active')],
        ];

        $options = [
            ['id'=>'جديد','name'=>'جديد'],
            ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
            ['id'=>'جاهز','name'=>'جاهز'],
            ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
            ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
            ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
            ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
        ];

        $searchData = [
            'id' => [
                'type' => 'text',
                'class' => 'form-control m-input',
                'label' => trans('main.id'),
                'index' => '0',
            ],
            'channel' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.channel'),
                'index' => '',
                'options' => $channels,
            ],
            'statusText' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.type'),
                'index' => '',
                'options' => $options,
            ],
            'status' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.status'),
                'index' => '',
                'options' => $actives,
            ],
            
        ];

        $tableData=  [
            'id' => [
                'label' => trans('main.id'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ],
            'channel' => [
                'label' => trans('main.channel'),
                'type' => '',
                'className' => '',
                'data-col' => 'channel',
                'anchor-class' => 'badge badge-dark',
            ],  
            'content_'.LANGUAGE_PREF => [
                'label' => trans('main.content_'.LANGUAGE_PREF),
                'type' => '',
                'className' => 'text-center pre-space',
                'data-col' => 'content_'.LANGUAGE_PREF,
                'anchor-class' => 'pre-space',
            ],   
            'statusText' => [
                'label' => trans('main.status'),
                'type' => '',
                'className' => '',
                'data-col' => 'statusText',
                'anchor-class' => '',
            ],  
            'statusIDText' => [
                'label' => trans('main.type'),
                'type' => '',
                'className' => '',
                'data-col' => 'statusIDText',
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

        if($request->ajax()){
            $data = ModTemplate::dataList(null,2);
            return Datatables::of($data['data'])->make(true);
        }

        $data['designElems']['searchData'] = $searchData; 
        $data['designElems']['tableData'] = $tableData;
        return view('Tenancy.ExternalServices.Views.V5.templates')->with('data', (object) $data);
    }

    public function templatesEdit($id) {
        $id = (int) $id;
        $service = $this->service;
        

        $dataObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('id',$id)->first();
        if($dataObj == null) {
            return Redirect('404');
        }

        $userObj = User::getData(User::getOne(USER_ID));

        $data['designElems']['mainData'] = [
            'title' => trans('main.edit') . ' '.trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fa fa-pencil-alt',
        ];

        $data['data'] = ModTemplate::getData($dataObj);
        return view('Tenancy.ExternalServices.Views.V5.edit')->with('data', (object) $data);      
    }

    public function templatesUpdate($id) {
        $id = (int) $id;
        $service = $this->service;
       
        $input = \Request::all();
        $dataObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('id',$id)->first();
        if($dataObj == null) {
            return Redirect('404');
        }

        $dataObj->content_ar = $input['content_ar'];
        $dataObj->content_en = $input['content_en'];
        $dataObj->status = $input['status'];
        $dataObj->updated_at = DATE_TIME;
        $dataObj->updated_by = USER_ID;
        $dataObj->save();

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function templatesAdd() {
        $service = $this->service;
        $data['designElems']['mainData'] = [
            'title' => trans('main.add') . ' '.trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fa fa-plus',
        ];
        $userObj = User::getData(User::getOne(USER_ID));
        $data['channel'] = $userObj->channels[0];
        $data['statuses'] = [
                ['id'=>'جديد','name'=>'جديد'],
                ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                ['id'=>'جاهز','name'=>'جاهز'],
                ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
                ['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل'],
        ];
        return view('Tenancy.ExternalServices.Views.V5.add')->with('data', (object) $data);      
    }

    public function templatesCreate() {
        $service = $this->service;
        $input = \Request::all();
        
        $dataObj = ModTemplate::NotDeleted()->where('mod_id',2)->where('statusText',$input['statusText'])->where('status',1)->first();
        if($dataObj && $input['status'] == 1){
            Session::flash('error', trans('main.statusFound'));
            return \Redirect::back()->withInput();
        }

        $dataObj = new ModTemplate;
        $dataObj->channel = Session::get('channelCode');
        $dataObj->content_ar = $input['content_ar'];
        $dataObj->content_en = $input['content_en'];
        $dataObj->statusText = $input['statusText'];
        $dataObj->status = $input['status'];
        $dataObj->mod_id = 2;
        $dataObj->updated_at = DATE_TIME;
        $dataObj->updated_by = USER_ID;
        $dataObj->save();

        Session::flash('success', trans('main.editSuccess'));
        return \Redirect::back()->withInput();
    }

    public function templatesDelete($id) {
        $id = (int) $id;
        $dataObj = ModTemplate::getOne($id);
        return \Helper::globalDelete($dataObj);
    }
}
