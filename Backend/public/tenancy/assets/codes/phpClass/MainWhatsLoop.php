<?php
class MainWhatsLoop {
    protected $instanceId = "", $token = "",$baseUrl = "";
    public function __construct($instanceId=null,$token=null) {

        $this->instanceId = $instanceId;
        $this->token = $token;
        $this->baseUrl = 'http://wloop.net/engine/';
    }

    /*----------------------------------------------------------
    Messages
    ----------------------------------------------------------*/

    public function sendMessage($data){
        $mainURL = $this->baseUrl.'messages/sendMessage';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function sendFile($data){
        $mainURL = $this->baseUrl.'messages/sendFile';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function sendPTT($data){
        $mainURL = $this->baseUrl.'messages/sendPTT';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function sendContact($data){
        $mainURL = $this->baseUrl.'messages/sendContact';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function sendLocation($data){
        $mainURL = $this->baseUrl.'messages/sendLocation';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }

    public function sendLink($data){
        $mainURL = $this->baseUrl.'messages/sendLink';
        return Http::withHeaders([
            'CHANNELID' => $this->instanceId,
            'CHANNELTOKEN' => $this->token,
        ])->post($mainURL,$data);
    }
}