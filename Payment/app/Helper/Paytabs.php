<?php 

class Paytabs{

	
	public static function hostedPayment($data){
		$mainURL = "https://secure.paytabs.sa";
	
		$curl = curl_init();

		$headers = array(
			"authorization: " . SERVER_KEY . "",
        );    

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt_array($curl, array(
  			CURLOPT_URL => $mainURL.'/payment/request',
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

	public static function queryTransaction($data,$serverKey){
		$mainURL = "https://secure.paytabs.sa";
	
		$curl = curl_init();

		$headers = array(
			"authorization: " . $serverKey . "",
        );    

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt_array($curl, array(
  			CURLOPT_URL => $mainURL.'/payment/query',
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

	
}