<?php

class CheckSendButtons {

    public static function sendButtons() {  
        $url = "https://api.chat-api.com/instance139624/sendButtons?token=72pb2371l07ur177";            
        $header = array(
            "content-type: application/json"
        );    

        $fields = [
            // "chatId" => "201016690106@c.us",
            // "chatId" => "201069273925@c.us",
            "chatId" => "966557722074@c.us",
            "title" => "title",
            "body" => "Please choose option",
            "footer" => "Thank you",
            "buttons" => ["Option A", "Option B","Option C",]
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
