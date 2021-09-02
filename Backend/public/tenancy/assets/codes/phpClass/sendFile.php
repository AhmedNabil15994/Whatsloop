<?php 

    include 'MainWhatsLoop.php';
    $instanceId = "xxxxxx";
    $instanceToken = "xxxxxxxxxxxxxxxxxxxxxxxxxx";
    $whatsLoopObj = new MainWhatsLoop($instanceId, $instanceToken);
    $data = [
        'phone' => '966xxxxxxxxx', // هاتف المستقبل
        'body' => 'https://image.freepik.com/free-vector/shining-circle-purple-lighting-isolated-dark-background_1441-2396.jpg', // رابط الملف
        'filename' => 'shining-circle-purple-lighting-isolated-dark-background_1441-2396.jpg', // اسم الملف
        'caption' => 'file caption', // النص مع الملف ( مع الصورة فقط)
    ];
    $whatsLoopObj->sendFile($data);