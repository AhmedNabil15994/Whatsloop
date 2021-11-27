<?php 

    include 'MainWhatsLoop.php';
    $instanceId = "xxxxxx";
    $instanceToken = "xxxxxxxxxxxxxxxxxxxxxxxxxx";
    $whatsLoopObj = new MainWhatsLoop($instanceId, $instanceToken);
    $data = [
        'phone' => '966xxxxxxxxx', // هاتف المستقبل
        'body' => 'https://wloop.net', // الرابط المرسل
        'title' => 'title', // عنوان الرابط المرسل
        'description' => 'Link Description', // وصف الرابط المرسل
        'previewBase64' => '', // Link Image as base64
    ];
    $whatsLoopObj->sendLink($data);