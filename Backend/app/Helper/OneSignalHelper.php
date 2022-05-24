<?php
use App\Models\CentralVariable;

class OneSignalHelper {

    public static function sendnotification($data){
        $appId = 'c3f0f166-2cc7-4f29-bd07-a735b4559481';//CentralVariable::getVar('ONESIGNALAPPID');
        $appKey = 'ODU0MDQ5NjEtYTZlOS00MThlLTk5YWEtN2Q5NWQ2MjQ3Zjk0';//CentralVariable::getVar('ONESIGNALAPPKEY');
        
        // $appId = 'df87a757-56ff-4fbe-8872-0a79bec2a8f2';
        // $appKey = 'NGNhYTI2OTAtM2Q2MS00NjA2LWIxMTEtZjFhZTQ1MzFlZmVl';

        $content = array(
            "en" => $data['message'],
        );
        
        $headings = array(
            "en" => $data['title'],
        );

        $ios_img = array(
            "id1" => $data['image']
        );
        $fields = array(
            'app_id' => $appId,
            "headings" => $headings,
            'include_player_ids' => $data['to'],
            'contents' => $content,
            "big_picture" => $data['image'],
            'large_icon' => "https://www.google.co.in/images/branding/googleg/1x/googleg_standard_color_128dp.png",
            'content_available' => true,
            "ios_attachments" => $ios_img
        );
        
        $fields = json_encode($fields);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                                                   'Authorization: Basic ODU0MDQ5NjEtYTZlOS00MThlLTk5YWEtN2Q5NWQ2MjQ3Zjk0'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;

        // $content = array(
        //     "en" => $data['message'],
        // );
        // $headings = array(
        //     "en" => $data['title'],
        // );
        // if ($data['type'] == 'android') {
        //     $fields = array(
        //         'app_id' => $appId,
        //         "headings" => $headings,
        //         'include_player_ids' => array($data['to']),
        //         'large_icon' => "https://www.google.co.in/images/branding/googleg/1x/googleg_standard_color_128dp.png",
        //         'content_available' => true,
        //         'contents' => $content
        //     );
        // } else {
        //     $ios_img = array(
        //         "id1" => $data['image']
        //     );
        //     $fields = array(
        //         'app_id' => $appId,
        //         "headings" => $headings,
        //         'include_player_ids' => array($data['to']),
        //         'contents' => $content,
        //         "big_picture" => $data['image'],
        //         'large_icon' => "https://www.google.co.in/images/branding/googleg/1x/googleg_standard_color_128dp.png",
        //         'content_available' => true,
        //         "ios_attachments" => $ios_img
        //     );

        // }
        // $headers = array(
        //     'Authorization: Basic '.$appKey,
        //     'Content-Type: application/json; charset=utf-8'
        // );
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // $result = curl_exec($ch);
        // curl_close($ch);
        // Logger(json_encode($fields));
        // Logger($result);
        // return $result;
    }

}

