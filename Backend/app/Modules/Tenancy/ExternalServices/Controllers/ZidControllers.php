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
use DB;
use DataTables;

class ZidControllers extends Controller {

    use \TraitsFunc;
    public $service = 'zid';

    public function checkPerm(){
        $disabled = Session::get('deactivatedAddons');
        $dis = 0;
        if(in_array(4,$disabled)){
            $dis = 1;
        }
        return $dis;
    }

    public function customers(Request $request){
        $input = \Request::all();
        $modelName = 'customers';
        $service = $this->service;

        $baseUrl = Variable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = Variable::getVar('ZidMerchantToken');
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

        $baseUrl = Variable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = Variable::getVar('ZidStoreToken');
        $managerToken = Variable::getVar('ZidMerchantToken');
        
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

        $baseUrl = Variable::getVar('ZidURL');
        $storeID = Variable::getVar('ZidStoreID');
        $storeToken = Variable::getVar('ZidMerchantToken');
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
        $paginationNo = 15;

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

            $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
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
        }

        $mainData['designElems'] = $data;
        $mainData['type'] = $model;
        $mainData['data'] = $formattedData;
        $mainData['dis'] = $this->checkPerm();

        if(!empty($formattedData)){
            $mainData['pagination'] = \Helper::GeneratePagination($modelData);
        }

        if($ajaxCheck){
            $returnHTML = view('Tenancy.ExternalServices.Views.ajaxData')->with('data', (object) $mainData)->render();
            return response()->json( array('success' => true, 'html'=>$returnHTML) );
        }

        return view('Tenancy.ExternalServices.Views.'.$model)->with('data', (object) $mainData);
    }

    public function formatData($data,$table){
        $objs = [];
        foreach ($data as $key => $value) {
            $dataObj = new \stdClass();
            $dataObj->id = $value->id;
            if($table == 'products'){
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
                    $categories_ar[]= isset($category['name']['ar']) && !empty($category['name']['ar']) ? $category['name']['ar'] : $category['name']['en'] ;
                    $categories_en[]= isset($category['name']['en']) && !empty($category['name']['en']) ? $category['name']['en'] : $category['name']['ar'] ;
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
                $dataObj->name = $value->name;
                $dataObj->phone = $value->mobile;
                $dataObj->email = $value->email;
                $dataObj->image = asset('images/not-available.jpg');
                $dataObj->city = unserialize($value->city)['name'];
                $dataObj->country = unserialize($value->city)['country_name'];
            }elseif($table == 'orders'){
                $status = unserialize($value->order_status);
                $dataObj->created_at = date('Y-m-d H:i:s', strtotime($value->created_at));
                $dataObj->id = $value->id;
                $dataObj->order_url = $value->order_url;
                $dataObj->total = $value->order_total_string;
                $dataObj->status = $status['name'];
                $dataObj->statusID = $status['code'];
                $dataObj->items = [['name'=>$value->store_name,'quantity'=>1]];
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
        foreach ($options as $key => $option) {
            $option = (object) $option;
            $extraTableData[$option->name] = [
                'label' => $option->name,
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ];
            $extraTableData['date_'.$key] = [
                'label' => trans('main.date'),
                'type' => '',
                'className' => '',
                'data-col' => '',
                'anchor-class' => '',
            ];

            $extraSearchData[$option->name] = [
                'type' => 'select',
                'class' => 'form-control',
                'id' => '',
                'index' => '',
                'options' => $sendOptions,
                'label' => $option->name,
            ];
            $extraSearchData['from_'.$key] = [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'id' => '',
                'index' => '',
                'label' => $option->name.' '.trans('main.dateFrom'),
            ];
            $extraSearchData['to_'.$key] = [
                'type' => 'text',
                'class' => 'form-control m-input datepicker',
                'id' => '',
                'index' => '',
                'label' => $option->name.' '.trans('main.dateTo'),
            ];
        }

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
        ];

        if($request->ajax()){
            // $data = User::dataList();
            return Datatables::of([])->make(true);
        }

        $data['designElems']['searchData'] = array_merge($oldSearchData,$extraSearchData); 
        $data['designElems']['tableData'] = array_merge($oldTableData,$extraTableData);
        return view('Tenancy.ExternalServices.Views.reports')->with('data', (object) $data);
    }

    public function templates(Request $request){
        $service = $this->service;

        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value->id;
            $channelObj->name = $value->name;
            $channels[] = $channelObj;
        }

        $data['designElems']['mainData'] = [
            'title' => trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => $service.'-template',
            'service' => $service,
            'icon' => 'fas fa-envelope-open-text',
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
                'label' => trans('main.status'),
                'index' => '',
                'options' => $options,
            ],
            'status' => [
                'type' => 'select',
                'class' => 'form-control ',
                'label' => trans('main.type'),
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
        return view('Tenancy.ExternalServices.Views.templates')->with('data', (object) $data);
    }

    public function templatesEdit($id) {
        $id = (int) $id;
        $service = $this->service;
        

        $dataObj = ModTemplate::NotDeleted()->find($id);
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
        return view('Tenancy.ExternalServices.Views.edit')->with('data', (object) $data);      
    }

    public function templatesUpdate($id) {
        $id = (int) $id;
        $service = $this->service;
       
        $input = \Request::all();
        $dataObj = ModTemplate::NotDeleted()->find($id);
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
        return view('Tenancy.ExternalServices.Views.add')->with('data', (object) $data);      
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
        $dataObj->channel = $input['channel'];
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
