<?php
use App\Models\CentralVariable;

class WhmcsHelper {

    public static function pullData($data){
        $whmcsUrl = "https://ca.whatsloop.net/OLD2022/";
        $api_identifier = "zOTTJdatQNMbOXXzee0L73SNJ1aVGK0i";
        $api_secret = "KxcHg7NlzIwspeYqswFIQeqDPvf4rrrX";

        $postfields = array(
            'identifier' => $api_identifier,
            'secret' => $api_secret,
            'responsetype' => 'json',
        );

        $postfields = array_merge($postfields,$data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $whmcsUrl . 'includes/api.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
        $response = curl_exec($ch);
        if (curl_error($ch)) {
            die('Unable to connect: ' . curl_errno($ch) . ' - ' . curl_error($ch));
        }
        curl_close($ch);
        $jsonData = json_decode($response, true);

        return $jsonData;
    }

}

