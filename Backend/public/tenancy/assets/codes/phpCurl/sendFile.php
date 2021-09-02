<?php 

    $curl = curl_init();
    // رابط التوجيه لارسال الرسائل
    $url = 'https://wloop.net/engine/messages/sendFile';

    $headers = array(
        'CHANNELID: xxxxxx',  // رقم القناة
        'CHANNELTOKEN: xxxxxxxxxxxxxxxxxxxxxxxxxx',  // رمز المصادقة ( Token )
    );

    $data = [
        'phone' => '966xxxxxxxxx', // هاتف المستقبل
        'body' => 'https://image.freepik.com/free-vector/shining-circle-purple-lighting-isolated-dark-background_1441-2396.jpg', // رابط الملف
        'filename' => 'shining-circle-purple-lighting-isolated-dark-background_1441-2396.jpg', // اسم الملف
        'caption' => 'file caption', // النص مع الملف ( مع الصورة فقط)
    ];

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
    ));

    // تقديم طلب POST
    $response = curl_exec($curl);
    curl_close($curl);