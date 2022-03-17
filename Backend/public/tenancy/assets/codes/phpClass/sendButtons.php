<?php 

    include 'MainWhatsLoop.php';
    $instanceId = "xxxxxx";
    $instanceToken = "xxxxxxxxxxxxxxxxxxxxxxxxxx";
    $whatsLoopObj = new MainWhatsLoop($instanceId, $instanceToken);
    $data = [
        'phone' => "966xxxxxxxxx",
        'title' => 'hello',
        'body' => "اهلا بك فى واتس لوب تجربة ارسال ازرار تفاعلية",
        'footer' => "bye bye",
        'buttons' => 'Option 1,Option 2,Option 3', 
    ];
    $whatsLoopObj->sendButtons($data);