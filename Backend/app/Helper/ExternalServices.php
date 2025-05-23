<?php
use App\Models\Variable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ExternalServices {

    function __construct($data) {
        $this->data = $data;
    }

    function startFuncs(){
        // Fetch Data Array
        $dataObj = $this->getAllDataBaseOnURL($this->data['storeToken'],$this->data['dataURL'],$this->data['myHeaders'],$this->data['service'],$this->data['params']);
        $idType = 'increments';
        if(isset($dataObj[0]['id']) && !empty($dataObj[0]['id'])){
            $count = strlen($dataObj[0]['id']);
            if($count > 11){
                $idType = 'string';
            }
        }

        // Fetch Data Properties Array
        $dataProps = $this->getModelAttributes($dataObj);

        // Create Data Table
        $tableResult = $this->createDynTable($this->data['tableName'],$dataProps,$idType);
        if($tableResult == 1){
            // Insert Data Into Table
            $this->checkTableData($this->data['tableName'],$dataObj); 
        }
    }

    function getAllDataBaseOnURL($token,$url,$myHeaders=[],$service,$params=[]){
        $result = $this->callURL($token,$url,$myHeaders,$params);
        return $this->formatResponse($result,$service);
    }

    function callURL($token,$url,$myHeaders=[],$urlParameters=[]){
        if(count($myHeaders) > 0){
            $data = Http::withToken($token)->withHeaders($myHeaders)->get($url,$urlParameters);           
        }else{
            $data = Http::withToken($token)->get($url,$urlParameters);
        }
        $result = (object) $data->json();
        return $result;
    }

    function formatResponse($result,$service='salla'){
        if($service == 'salla'){
            $modelData = $this->formatSallaResponse($result);
        }elseif($service == 'zid'){
            $modelData = $this->formatZidResponse($result);
        }
        return self::reformatModelData($modelData);
    }

    function formatSallaResponse($result){
        $modelData = [];
        if($result && isset($result->status) && $result->status == 200){
            if(isset($result->pagination)){
                $pagesCount = $result->pagination['totalPages'];
                if($pagesCount > 1){
                    for ($i = 1; $i <= $pagesCount ; $i++) {
                        $params =  ['page' => $i];
                        $newResult = $this->callURL($this->data['storeToken'],$this->data['dataURL'],$this->data['myHeaders'],$params);
                        $modelData = array_merge($modelData,isset($newResult->data) ? $newResult->data : []);
                        // $modelData[] = $newResult->data;
                    }
                }elseif ($pagesCount == 1) {
                    $modelData = $result->data;
                }
            }else{
                $modelData = $result->data;
            }
        }
        return $modelData;
    }

    function formatZidResponse($result){
        $modelData = [];
        if( isset($result->results) && !empty($result->results) ){
            $modelData = $result->results;
        }
        if( isset($result->customers) && !empty($result->customers) ){
            $modelData = $result->customers;
        }
        if( isset($result->orders) && !empty($result->orders) ){
            $modelData = $result->orders;
        }
        if( isset($result->{'abandoned-carts'}) && !empty($result->{'abandoned-carts'}) ){
            $modelData = $result->{'abandoned-carts'};
            if(isset($result->pagination)){
                $pagesCount = $result->pagination['last_page'];
                if($pagesCount > 1){
                    for ($i = 2; $i <= $pagesCount ; $i++) {
                        $params =  ['page' => $i , 'page_size' => 100];
                        $newResult = $this->callURL($this->data['storeToken'],$this->data['dataURL'],$this->data['myHeaders'],$params);
                        $modelData = array_merge($modelData,$newResult->{'abandoned-carts'});
                    }
                }
            }
        }
        return $modelData;
    }

    static function reformatModelData($data){
        $editedData = [];
        foreach ($data as $value) {
            $newObj = $value;
            foreach ($value as $key => $dataObj) {
                if(in_array($key, ['hide_quantity','sort','tags','consisted_products','digital_download_limit','digital_download_expiry','hide_quantity','country_code','gender','birth_date','is_active','is_cod_enabled','purchase_restrictions','product_class','meta','calories','maximum_quantity_per_order','starting_price'])){
                    unset($newObj[$key]);
                }
                if(strpos($key, 'ed_at') !== false || $key == 'date'){
                    $newUpdate = $dataObj;
                    if(is_array($value[$key])){
                        $newUpdate = date('Y-m-d H:i:s',strtotime($value[$key]['date']));
                    }else{
                        $newUpdate = date('Y-m-d H:i:s',strtotime($dataObj));
                    }
                    $newObj[$key] = $newUpdate;
                }else{
                    if(is_array($dataObj)){
                        $newObj[$key] = serialize($dataObj);
                    }
                }
            }
            $editedData[] = $newObj;
        }
        return $editedData;
    }

    function getModelAttributes($data){
        $modelProps = [];
        if(count($data) > 0){
            foreach ($data[0] as $key => $value) {
                $modelProps[] = $key;
            }
        }
        return $modelProps;
    }

    function createDynTable($tableName, $modelProps = [] , $idType='increments'){
        if (!Schema::hasTable($tableName)) {
            if (count($modelProps) > 0) {
                Schema::create($tableName, function (Blueprint $table) use ($modelProps, $tableName,$idType) {
                    foreach ($modelProps as $field) {
                        if($field == 'id'){
                            $table->$idType('id');
                        }else if(strpos($field, 'ed_at') !== false || strpos($field, 'date') !== false){
                            $table->dateTime($field)->nullable();
                        }else{
                            $table->text($field)->collate('utf8_general_ci')->nullable();
                        }
                    }
                });
            }
        }
        return 1;
    }

    function checkTableData($tableName,$dataObj){
        if (Schema::hasTable($tableName)) {
            $tableDataCount = DB::table($tableName)->count();
            if(count($dataObj) < $tableDataCount){
                DB::table($tableName)->truncate();
            }
            if(count($dataObj) > 0){
                foreach ($dataObj as $value) {
                    if(isset($value['id'])){
                        $checkObj = DB::table($tableName)->where('id',$value['id'])->first();
                        if(!$checkObj){
                            if($tableName == 'salla_products'){
                                unset($value['consisted_products']);
                                unset($value['digital_download_limit']);
                                unset($value['digital_download_expiry']);
                                unset($value['tags']);
                                unset($value['hide_quantity']);
                                unset($value['starting_price']);
                                unset($value['main_image']);
                                unset($value['metadata']);
                            }
                            if($tableName == 'salla_customers'){
                                unset($value['country_code']);
                            }
                            if($tableName == 'zid_products'){
                                unset($value['purchase_restrictions']);
                            }
                            DB::table($tableName)->insert($value);
                        }   
                    }
                    
                }
            }
        }
    }
        
}
