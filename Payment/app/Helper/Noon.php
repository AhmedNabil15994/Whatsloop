<?php 

class Noon{

    private static $mainURL = "https://api-test.noonpayments.com/payment/v1/";
    private static $mode = 'Test';
    // private static $mainURL = "https://api.noonpayments.com/payment/v1/";
    // private static $mode = 'Live';


	public static function hostedPayment($data){
		$businessId = BUSINESS_ID;
		$appName = APP_NAME;
		$appKey = APP_KEY;
		$authKey = AUTH_KEY;

		$curl = curl_init();

		$headers = array(
			'Content-Type: application/json',
			"Authorization: Key_" . self::$mode . " " . $authKey . "",
        );    

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt_array($curl, array(
  			CURLOPT_URL => self::$mainURL.'/order',
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

	public static function queryTransaction($data){
		$curl = curl_init();

		$headers = array(
			'Content-Type: application/json',
			"Authorization: Key_" . self::$mode . " " . $data['auth_key'] . "",
        );    

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt_array($curl, array(
  			CURLOPT_URL => self::$mainURL.'/order/'.$data['orderId'],
  			CURLOPT_RETURNTRANSFER => true,
  			CURLOPT_ENCODING => '',
  			CURLOPT_MAXREDIRS => 10,
  			CURLOPT_TIMEOUT => 0,
  			CURLOPT_FOLLOWLOCATION => true,
  			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  			CURLOPT_CUSTOMREQUEST => 'GET',
		));

		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}	

	
}