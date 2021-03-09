<?php

use App\Models\Variable;
use App\Models\UserChannels;
/**
 * This Class For Whatsloop Api To Send ( Message - File - Photo - Location - Voice - Contact )
 *
 * @author WhatsLoop.net
 */
class WhatsLoop {
    protected $InstanceId = "", $Token = "";
    /**
     * WhatsLoop constructor.
     * @param int $InstanceId
     * @param string $Token
     */
    public function __construct() {
        $channelObj =  UserChannels::NotDeleted()->where('start_date','<=',date('Y-m-d'))->where('end_date','>=',date('Y-m-d'))->first();
        $this->InstanceId = $channelObj->id;
        $this->Token = $channelObj->token;
    }

    /**
     * Send Message To Mobile Number
     * @param string $Message
     * @param int $Number
     */
    public function sendMessage($Message, $Number) {
        $Data = array();
        $Data['InstanceId'] = $this->InstanceId;
        $Data['Token'] = $this->Token;
        $Data['Number'] = $Number;
        $Data['Message'] = $Message;
        $Data['Type'] = 0;
        return $this->OpenURLWithPost("https://whatsloop.net/API/Send.php", $Data);
    }

    /**
     * Send File ( Photo " jpg or png " - File " xlsx ") To Mobile Number Caption Working With Photo Only
     * @param string $PhotoCaption
     * @param int $Number
     * @param string $URL
     */
    public function sendFile($Number, $URL, $PhotoCaption = "") {
        $Data = array();
        $Data['InstanceId'] = $this->InstanceId;
        $Data['Token'] = $this->Token;
        $Data['Number'] = $Number;
        $Data['MessageFileCaption'] = $PhotoCaption;
        $Data['URL'] = $URL;
        $Data['Type'] = 1;
        return $this->OpenURLWithPost("https://whatsloop.net/API/Send.php", $Data);
    }

    /**
     * Send Voice Support Ogg only
     * @param int $Number
     * @param string $URL
     */
    public function sendVoice($Number, $URL) {
        $Data = array();
        $Data['InstanceId'] = $this->InstanceId;
        $Data['Token'] = $this->Token;
        $Data['Number'] = $Number;
        $Data['URL'] = $URL;
        $Data['Type'] = 3;
        return $this->OpenURLWithPost("https://whatsloop.net/API/Send.php", $Data);
    }

    /**
     * Send Location 
     * @param int $Number
     * @param string $Location lat,lng example: 21.59822,39.160861
     * @param string $Address Your Address example : 6081 Quraysh, Al Bawadi, Jeddah 23531, Saudi Arabia
     */
    public function sendLocation($Number, $Location, $Address) {
        $Data = array();
        $Data['InstanceId'] = $this->InstanceId;
        $Data['Token'] = $this->Token;
        $Data['Number'] = $Number;
        $Data['Location'] = $Location;
        $Data['Message'] = $Address;
        $Data['Type'] = 6;
        return $this->OpenURLWithPost("https://whatsloop.net/API/Send.php", $Data);
    }

    /**
     * Send Contact 
     * @param int $Number
     * @param int $Contact
     */
    public function sendContact($Number, $Contact) {
        $Data = array();
        $Data['InstanceId'] = $this->InstanceId;
        $Data['Token'] = $this->Token;
        $Data['Number'] = $Number;
        $Data['Contact'] = $Contact;
        $Data['Type'] = 5;
        return $this->OpenURLWithPost("https://whatsloop.net/API/Send.php", $Data);
    }

    /**
     * Get Status Of Instance 
     * @param int $Number
     * @param int $Contact
     */
    public function getStatus($Number, $Contact) {
        $Data = array();
        $Data['InstanceId'] = $this->InstanceId;
        $Data['Token'] = $this->Token;
        return $this->OpenURLWithPost("https://whatsloop.net/API/Get.php", $Data);
    }

    private function OpenURLWithPost($url, $data) {
        $url = $url;
        $params = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        $ctx = @stream_context_create($params);
        $fp = @fopen($url, 'rb', false, $ctx);
        if ($fp) {
            return @stream_get_contents($fp);
        } else {
            throw new Exception("Error loading '$url', $php_errormsg");
        }
    }

    function Webhook($url, array $data, array $headers = null) {
        $params = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query($data)
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

}