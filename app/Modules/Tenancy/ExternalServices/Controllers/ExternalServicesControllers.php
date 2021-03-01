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

class ExternalServicesControllers extends Controller {

    use \TraitsFunc;

    public function getServiceData($service,$model,$refresh=null){    
        if(!in_array($service, ['salla','zid']) || !in_array($model, ['customers','products','orders']) || !in_array($refresh, ['refresh',null])){
            return redirect('404');
        }

        if($service == 'salla'){
            $baseUrl = Variable::getVar('SallaURL');
            $storeToken = Variable::getVar('SallaStoreToken'); 
            $dataURL = $baseUrl.'/'.$model;
            $tableName = $service.'_'.$model;
            $myHeaders =[];
        }else if($service == 'zid'){
            $baseUrl = Variable::getVar('ZidURL');
            $storeID = Variable::getVar('ZidStoreID');
            $storeToken = Variable::getVar('ZidStoreToken');
            $managerToken = Variable::getVar('ZidMerchantToken');
            
            if($model == 'products'){
                $dataURL = $baseUrl.'/'.$model.'/';
            }elseif($model == 'orders' || $model == 'customers'){
                $dataURL = $baseUrl.'/managers/store/'.$model.'/';   
                $oldStoreToken = $storeToken;
                $storeToken = $managerToken;
                $managerToken = $oldStoreToken;
            }
            $tableName = $service.'_'.$model;

            $myHeaders = [
                "X-MANAGER-TOKEN" => $managerToken,
                "STORE-ID" => $storeID,
                "ROLE" => 'Manager',
                'User-Agent' => 'whatsloop/1.00.00 (web)',
            ];
        }

        $dataArr = [
            'baseUrl' => $baseUrl,
            'storeToken' => $storeToken,
            'dataURL' => $dataURL,
            'tableName' => $tableName,
            'myHeaders' => $myHeaders,
            'service' => $service,
        ];

        if($service == 'salla' && $model == 'orders'){
            $newDataArr = $dataArr;
            $newDataArr['dataURL'] = $dataArr['dataURL'].'/statuses';
            $newDataArr['tableName'] = $service.'_order_status';  
            $externalHelperObj = new \ExternalServices($newDataArr);
            if (!Schema::hasTable($newDataArr['tableName']) || $refresh == 'refresh') {
                $externalHelperObj->startFuncs();
            }
        }
        $externalHelperObj = new \ExternalServices($dataArr);
        if (!Schema::hasTable($tableName) || $refresh == 'refresh') {
            $externalHelperObj->startFuncs();
        }

        if($refresh == 'refresh'){
            return redirect()->to('/services/'.$service.'/'.$model);
        }

        return $this->runModuleService($service,$model,$tableName);
    }

    public function runModuleService($service,$model,$tableName){
        $input = \Request::all();
        $paginationNo = 12;

        if (Schema::hasTable($tableName)) {
            $source = DB::table($tableName);
        }else{
            $source = [];
        }
        
        if($service == 'salla'){
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
                    $source->where('country','LIKE','%'.$input['address'].'%')->orWhere('city','LIKE','%'.$input['address'].'%');
                }

                $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
                $formattedData = $this->formatData($modelData,$model,$service);
                
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
                    $source->where('date','>=', $input['from'].' 00:00:00')->where('date','<=',$input['to']. ' 23:59:59');
                }
                if(isset($input['id']) && !empty($input['id'])){
                    $source->where('id',$input['id']);
                }

                if(isset($input['status']) && !empty($input['status'])){
                    $source->where('status','LIKE','%'.$input['status'].'%');
                }

                $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
                $formattedData = $this->formatData($modelData,$model,$service);
                
                $data['mainData'] = [
                    'title' => trans('main.orders'),
                    'url' => 'orders',
                    'service' => $service,
                    'icon' => 'mdi mdi-truck-delivery-outline',
                ];

                if (Schema::hasTable($service.'_order_status')) {
                    $options = DB::table($service.'_order_status')->get();
                }else{
                    $options = [];
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
            }elseif ($model == 'products') {
                // Begin Search
                if(isset($input['id']) && !empty($input['id'])){
                    $source->where('id',$input['id']);
                }

                if(isset($input['name']) && !empty($input['name'])){
                    $source->where('name','LIKE','%'.$input['name'].'%');
                }

                if(isset($input['price']) && !empty($input['price'])){
                    $source->where('price','LIKE','%'.$input['price'].'%');
                }

                if(isset($input['status'])){
                    $source->where('is_available',$input['status']);
                }

                $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
                $formattedData = $this->formatData($modelData,$model,$service);
                
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
        }elseif($service == 'zid'){
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


                $modelData = $source == [] ?  [] : $source->paginate($paginationNo);
                $formattedData = $this->formatData($modelData,$model,$service);
                
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
                $formattedData = $this->formatData($modelData,$model,$service);
                
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
                $formattedData = $this->formatData($modelData,$model,$service);
                
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
        }

        $mainData['designElems'] = $data;
        $mainData['data'] = $formattedData;
        if(!empty($formattedData)){
            $mainData['pagination'] = \Helper::GeneratePagination($modelData);
        }
        return view('Tenancy.ExternalServices.Views.'.$model)->with('data', (object) $mainData);
    }

    public function formatData($data,$table,$service){
        $objs = [];
        foreach ($data as $key => $value) {
            $dataObj = new \stdClass();
            $dataObj->id = $value->id;
            if($table == 'products'){
                if($service == 'salla'){
                    $name_ar = $value->name;
                    $name_en = $value->name;

                    $dbPrice = unserialize($value->price);
                    $price = $dbPrice['amount'] .' '.$dbPrice['currency'];
                    $images = @unserialize($value->images)[0]['url'];
                    $url = $value->url;
                    $status = $value->is_available;
                    $withTax = $value->with_tax;
                    $created_at = $value->pinned_date;
                    $require_shipping = $value->require_shipping;
                }elseif($service == 'zid'){
                    $name_ar = @unserialize($value->name)['ar'];
                    $name_en = @unserialize($value->name)['en'];
                    
                    $price = $value->formatted_price;
                    $images = @unserialize($value->images)[0]['image']['full_size'];
                    $url = $value->html_url;
                    $status = $value->is_published;
                    $withTax = $value->is_taxable;
                    $created_at = $value->created_at;
                    $require_shipping = $value->requires_shipping;
                }

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
                if($service == 'salla'){
                    $dataObj->name = $value->first_name .' '.$value->last_name;
                    $dataObj->phone = $value->mobile_code . '' . $value->mobile;
                    $dataObj->email = $value->email;
                    $dataObj->gender = $value->gender;
                    $dataObj->image = $value->avatar;
                    $dataObj->city = $value->city;
                    $dataObj->country = $value->country;
                    $dataObj->currency = $value->currency;
                    $dataObj->location = $value->location;
                    $dataObj->updated_at = date('Y-m-d H:i:s' , strtotime($value->updated_at));
                }elseif($service == 'zid'){
                    $dataObj->name = $value->name;
                    $dataObj->phone = $value->mobile;
                    $dataObj->email = $value->email;
                    $dataObj->image = asset('images/not-available.jpg');
                    $dataObj->city = unserialize($value->city)['name'];
                    $dataObj->country = unserialize($value->city)['country_name'];
                }
            }elseif($table == 'orders'){
                if($service == 'salla'){
                    $price = unserialize($value->total);
                    $status = unserialize($value->status);
                    $dataObj->reference_id = $value->reference_id;
                    $dataObj->created_at = date('Y-m-d H:i:s', strtotime($value->date));
                    $dataObj->total = $price['amount'] . ' '.$price['currency'];
                    $dataObj->can_cancel = $value->can_cancel;
                    $dataObj->status = $status['name'];
                    $dataObj->statusID = $status['id'];
                    $dataObj->items = unserialize($value->items);
                }elseif($service == 'zid'){
                    $status = unserialize($value->order_status);
                    $dataObj->created_at = date('Y-m-d H:i:s', strtotime($value->created_at));
                    $dataObj->id = $value->code;
                    $dataObj->total = $value->order_total_string;
                    $dataObj->status = $status['name'];
                    $dataObj->statusID = $status['code'];
                    $dataObj->items = [['name'=>$value->store_name,'quantity'=>1]];
                }
            }
            $objs[] = $dataObj;
        }
        return $objs;
    }

    public function reports($service,Request $request){
        if(!in_array($service, ['salla','zid'])){
            return redirect('404');
        }
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
            $options = [];
        }

        $sendOptions =[
            ['id'=> 0 , 'name' => trans('main.notSent')],
            ['id'=> 1 , 'name' => trans('main.sentDone')],
        ];

        $extraTableData = [];
        $extraSearchData = [];
        foreach ($options as $key => $option) {
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

    public function templates($service,Request $request){
        if(!in_array($service, ['salla','zid'])){
            return redirect('404');
        }

        $userObj = User::getData(User::getOne(USER_ID));
        $channels = [];
        foreach ($userObj->channels as $key => $value) {
            $channelObj = new \stdClass();
            $channelObj->id = $value;
            $channelObj->name = $value;
            $channels[] = $channelObj;
        }

        $data['designElems']['mainData'] = [
            'title' => trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => 'templates',
            'service' => $service,
            'icon' => 'fas fa-envelope-open-text',
        ];

        $actives = [
            ['id'=>0,'name'=>trans('main.notActive')],
            ['id'=>1,'name'=>trans('main.active')],
        ];

        $options = [['id'=>'ترحيب بالعميل','name'=>'ترحيب بالعميل']];
        if($service == 'salla'){
            if (Schema::hasTable($service.'_order_status')) {
                $statuses = DB::table($service.'_order_status')->get();
                foreach ($statuses as $value) {
                    $options[] = ['id'=>$value->name,'name'=>$value->name];
                }
            }
        }elseif($service == 'zid'){
            $zidOptions = [
                ['id'=>'جديد','name'=>'جديد'],
                ['id'=>'جاري التجهيز','name'=>'جاري التجهيز'],
                ['id'=>'جاهز','name'=>'جاهز'],
                ['id'=>'جارى التوصيل','name'=>'جارى التوصيل'],
                ['id'=>'تم التوصيل','name'=>'تم التوصيل'],
                ['id'=>'تم الالغاء','name'=>'تم الالغاء'],
            ];
            $options = array_merge($options,$zidOptions);
        }

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
                'anchor-class' => '',
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
            $mod_id = $service == 'salla' ? 1 : 2;
            $data = ModTemplate::dataList(null,$mod_id);
            return Datatables::of($data['data'])->make(true);
        }

        $data['designElems']['searchData'] = $searchData; 
        $data['designElems']['tableData'] = $tableData;
        return view('Tenancy.ExternalServices.Views.templates')->with('data', (object) $data);
    }

    public function templatesEdit($service,$id) {
        $id = (int) $id;
        if(!in_array($service, ['salla','zid'])){
            return redirect('404');
        }

        $dataObj = ModTemplate::NotDeleted()->find($id);
        if($dataObj == null) {
            return Redirect('404');
        }

        $userObj = User::getData(User::getOne(USER_ID));

        $data['designElems']['mainData'] = [
            'title' => trans('main.edit') . ' '.trans('main.templates'),
            'url' => 'services/'.$service.'/templates',
            'name' => 'templates',
            'nameOne' => 'templates',
            'service' => $service,
            'icon' => 'fa fa-pencil-alt',
        ];

        $data['data'] = ModTemplate::getData($dataObj);
        return view('Tenancy.ExternalServices.Views.edit')->with('data', (object) $data);      
    }

    public function templatesUpdate($service,$id) {
        $id = (int) $id;
        if(!in_array($service, ['salla','zid'])){
            return redirect('404');
        }

        $mod_id = $service == 'salla' ? 1 : 2;
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

}
