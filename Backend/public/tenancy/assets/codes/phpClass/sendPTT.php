<?php 

    include 'MainWhatsLoop.php';
    $instanceId = "xxxxxx";
    $instanceToken = "xxxxxxxxxxxxxxxxxxxxxxxxxx";
    $whatsLoopObj = new MainWhatsLoop($instanceId, $instanceToken);
    $data = [
        'phone' => '966xxxxxxxxx', // هاتف المستقبل
        'audio' => 'https://url/file.ogg', // رابط الملف
    ];
    $whatsLoopObj->sendPTT($data);