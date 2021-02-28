<?php
use App\Models\Variable;
class ExrernalService {
    protected $fcmKey;

    function __construct($storeToken,$storeID=null) {
        $this->storeToken = $storeToken;
        $this->storeID = $storeID;
    }


    function getServiceData($url) {  
        $header = array(
            "content-type: application/json",
            "Authorization: Bearer $this->storeToken",
            "STORE-ID: $this->storeID",
            "ROLE: Manager",
        );    

        $fields = [
            
        ];

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);    
        // close handle to release resources
        curl_close($ch);
        return $result;
    }

    
}
