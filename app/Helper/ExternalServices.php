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
        $dataObj = $this->getAllDataBaseOnURL($this->data['storeToken'],$this->data['dataURL'],$this->data['myHeaders'],$this->data['service']);
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

    function getAllDataBaseOnURL($token,$url,$myHeaders=[],$service){
        $params = [];
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
        $modelData = [];
        if($service == 'salla' && $result->status == 200){
            if(isset($result->pagination)){
                $pagesCount = $result->pagination['totalPages'];
                if($pagesCount > 1){
                    for ($i = 1; $i <= $pagesCount ; $i++) {
                        $params =  ['page' => $i];
                        $newResult = $this->reformatURL($token,$url,$myHeaders,$params);
                        $modelData[] = $newResult['data'];
                    }
                }elseif ($pagesCount == 1) {
                    $modelData = $result->data;
                }
            }else{
                $modelData = $result->data;
            }
        }elseif($service == 'zid' && isset($result->results) && !empty($result->results)){
            $modelData = $result->results;
        }
        return $this->reformatModelData($modelData);
    }

    function reformatModelData($data){
        $editedData = [];
        foreach ($data as $value) {
            $newObj = $value;
            foreach ($value as $key => $dataObj) {
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
            if(count($dataObj) != $tableDataCount){
                if($tableDataCount > 0){
                    DB::table($tableName)->truncate();
                }
                DB::table($tableName)->insert($dataObj);
            }
        }
    }
        
}
