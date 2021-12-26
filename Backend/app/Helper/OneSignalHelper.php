<?php
use App\Models\CentralVariable;

class OneSignalHelper {

    public static function sendnotification($data){
        $appId = CentralVariable::getVar('ONESIGNALAPPID');
        $appKey = CentralVariable::getVar('ONESIGNALAPPKEY');

        $content = array(
            "ar" => $data['message'],
        );
        $headings = array(
            "ar" => $data['title'],
        );
        if ($data['type'] == 'android') {
            $fields = array(
                'app_id' => $appId,
                "headings" => $headings,
                'include_player_ids' => array($data['to']),
                'large_icon' => "https://www.google.co.in/images/branding/googleg/1x/googleg_standard_color_128dp.png",
                'content_available' => true,
                'contents' => $content
            );
        } else {
            $ios_img = array(
                "id1" => $data['image']
            );
            $fields = array(
                'app_id' => $this->,
                "headings" => $headings,
                'include_player_ids' => array($to),
                'contents' => $content,
                "big_picture" => $data['image'],
                'large_icon' => "https://www.google.co.in/images/branding/googleg/1x/googleg_standard_color_128dp.png",
                'content_available' => true,
                "ios_attachments" => $ios_img
            );

        }
        $headers = array(
            'Authorization: key='.$appKey,
            'Content-Type: application/json; charset=utf-8'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}

