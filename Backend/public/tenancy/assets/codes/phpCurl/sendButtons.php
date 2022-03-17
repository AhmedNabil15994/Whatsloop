<?php 

    $curl = curl_init();
    // رابط التوجيه لارسال ازرار تفاعلية
    $url = 'https://wloop.net/engine/messages/sendButtons';

    $headers = array(
        'CHANNELID: xxxxx',  // رقم القناة
        'CHANNELTOKEN: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',  // رمز المصادقة ( Token )
    );

    $data = array(
        'chatId' => '966xxxxxxxxx',
        'title' => 'hello',
        'body' => 'اهلا بك فى واتس لوب تجربة ارسال ازرار تفاعلية',
        'footer' => 'bye bye',
        'buttons' => 'Option 1,Option 2,Option 3',
    );

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => $headers,
    ));

    // تقديم طلب POST
    $response = curl_exec($curl);

    curl_close($curl);