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

}

