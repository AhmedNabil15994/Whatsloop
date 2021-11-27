<?php

class SendButtons {

    public static function send($data) {  
        $header = array(
            "content-type: application/json"
        );    

        $fields = [
            "chatId" => $data['chatId'],//"966557722074@c.us",
            "title" => $data['title'],//"title",
            "body" => $data['body'],//"Please choose option",
            "footer" => $data['footer'],//"Thank you",
            "buttons" => explode(',',$data['buttons']),//["Option A", "Option B","Option C",]
        ];

        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $data['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields ));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);    
        // close handle to release resources
        curl_close($ch);
        return json_decode( $result);
    }

    
}
