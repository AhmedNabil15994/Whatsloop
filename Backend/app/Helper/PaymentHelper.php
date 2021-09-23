<?php
use App\Models\CentralVariable;

class PaymentHelper {
    protected $secret_key;

    function __construct() {
        $this->secret_key = CentralVariable::getVar('SECRET_KEY');
    }

    public function moyasar($url,$data) {  
        $url = "https://api.moyasar.com/v1/".$url;         
        return Http::withBasicAuth($this->secret_key,'')->post($url,$data);

    }

    public function RedirectWithPostForm(array $data) {
        $url = "https://payment.servers.com.sa/API/";
        $fullData = array_merge($data,['Token'=>$this->secret_key]);
        ?>
       <html xmlns="http://www.w3.org/1999/xhtml">
           <head>
               <script type="text/javascript">
                   function closethisasap() {
                       document.forms["redirectpost"].submit();
                   }
               </script>
           </head>
           <body onload="closethisasap();">
               <form name="redirectpost" method="post" action="<?PHP echo $url; ?>">
                   <?php
                   if (!is_null($fullData)) {
                       foreach ($fullData as $k => $v) {
                           echo '<input type="hidden" name="' . $k . '" value="' . $v . '"> ';
                       }
                   }
                   ?>
               </form>
           </body>
       </html>
       <?php
       exit;
    }

    public function OpenURLWithPost(array $data, array $headers = null) {
        $url = "https://payment.servers.com.sa/API/CheckPayment.php";
        $fullData = array_merge($data,['Token'=>$this->secret_key]);
        $params = array(
           'http' => array(
               'method'  => 'POST',
               'content' => http_build_query($fullData)
           )
       );
       if (!is_null($headers)) {
           $params['http']['header'] = '';
           foreach ($headers as $k => $v) {
               $params['http']['header'] .= "$k: $v\n";
           }
       }
       $ctx = stream_context_create($params);
       $fp = @fopen($url, 'rb', false, $ctx);
       if ($fp) {
           return @stream_get_contents($fp);
           die();
       } else {
           // Error
           throw new Exception("Error loading '$url', $php_errormsg");
       }
    }

    public function payTabs($data){
        $url = "https://payment.servers.com.sa/API/";
        $fullData = array_merge($data,['Token'=>$this->secret_key]);
        return Http::asForm()->post($url,$fullData);
    }

    public function formatResponse($result){
        if(isset($result['errors']) && !empty($result['errors'])){
            $extraResult = array_values($result['errors']);
            if(is_array($extraResult[0])){
                $msg = implode(',', $extraResult[0]);
            }else{
                $msg = $extraResult[0];
            }
            return [0,$msg];
        }
        return [1,''];
    }


    public function hostedPayment($data,$urlSegment,$extraHeaders){
        $curl = curl_init();
        $mainURL = "http://payment.whatsloop.loc";

        $headers = array(
            'Content-Type: application/json',
        );

        $allHeaders = array_merge($headers,$extraHeaders);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $allHeaders);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $mainURL.$urlSegment,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }   

    public function initNoon($data){
        $businessId = 'digital_servers';
        $appName = 'whatsloop';
        $appKey = '9ccc2c4b3f3345d4900d916d2a8c2abf'; //For Test
        $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDo5Y2NjMmM0YjNmMzM0NWQ0OTAwZDkxNmQyYThjMmFiZg=='; // For Test
        // $appKey = 'c55603b594b1495ea260a96bdccef35c';
        // $authKey = 'ZGlnaXRhbF9zZXJ2ZXJzLndoYXRzbG9vcDpjNTU2MDNiNTk0YjE0OTVlYTI2MGE5NmJkY2NlZjM1Yw==';
        $dataArr = [
            'returnURL' => $data['returnURL'],
            'cart_id' => $data['cart_id'],
            'cart_amount' => $data['cart_amount'],
            'cart_description' => $data['cart_description'],
            'paypage_lang' => $data['paypage_lang'],
            'description' => $data['description'],
        ];

        $extraHeaders = [
            'BUSINESSID: '.$businessId,
            'APPNAME: '.$appName,
            'APPKEY: '.$appKey,
            'AUTHKEY: '.$authKey,
        ];
        return [
            'dataArr' => $dataArr,
            'extraHeaders' => $extraHeaders,
        ];
    }

}

