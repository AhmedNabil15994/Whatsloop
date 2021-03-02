{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css">
    i{
        border: 0 !important;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ URL::to('/profile') }}">{{ trans('main.myAccount') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>
    </div>     

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ trans('main.intro') }}</h4>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="bg-success text-white p-4" role="alert">
                                <h4 class="alert-heading">{{ trans('main.instructions') }}</h4>
                                <ul>
                                    <li>{{ trans('main.instructions_p1') }}</li>
                                    <li>{{ trans('main.instructions_p2') }}</li>
                                    <li>{{ trans('main.instructions_p3') }}</li>
                                    <li>{{ trans('main.instructions_p4') }}</li>
                                </ul>
                                <div class="border-bottom border-white opacity-20 mb-2"></div>
                                <div class="text-right">
                                    <a href="{{ URL::to('/').'/uploads/Whatsloop.class.zip' }}" class="btn btn-success mr-2">{{ trans('main.downloadLibrary') }}</a>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""><i class="mdi mdi-minus"></i></a>
                    </div>
                    <h5 class="card-title mb-0 text-white">{{ trans('main.send_text') }}</h5>
                </div>
                <div id="cardCollpase1" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_text_p1') }} .</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item"><a href="#php" data-toggle="tab" aria-expanded="false" class="nav-link active">PHP</a></li>
                                <li class="nav-item"><a href="#phpclass" data-toggle="tab" aria-expanded="true" class="nav-link">PHP Class</a></li>
                                <li class="nav-item"><a href="#node" data-toggle="tab" aria-expanded="false" class="nav-link">Node.js</a></li>
                                <li class="nav-item"><a href="#jquery" data-toggle="tab" aria-expanded="false" class="nav-link">JQuery</a></li>
                                <li class="nav-item"><a href="#curl" data-toggle="tab" aria-expanded="false" class="nav-link">Curl (Bash)</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="php">
                                    $data = [
                                        'InstanceId' => 'xxxxx', // رقم القناة
                                        'Token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        'Number' => '966501484701', // هاتف المستقبل
                                        'Message' => 'اهلا بك فى واتس لوب تجربة ارسال رسالة',
                                        'Type' => 0, // نوع الرساله (نص)
                                    ];
                                    // رابط التوجيه لارسال الرسائل
                                    $url = 'https://whatsloop.net/API/Send.php';
                                    // تقديم طلب POST
                                    $options = stream_context_create(['http' => [
                                            'method'  => 'POST',
                                            'content' => http_build_query($data)
                                        ]
                                    ]);
                                    // ارسل طلب
                                    $result = file_get_contents($url, false, $options);
                                </div>
                                <div class="tab-pane" id="phpclass">
                                    include 'Whatsloop.class.php';
                                    $InstanceId = "xxxx";
                                    $WhatsLoopToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
                                    $WhatsLoop = new Whatsloop($InstanceId, $WhatsLoopToken);
                                    $WhatsLoop->sendMessage("اهلا بك فى واتس لوب تجربة ارسال رسالة", "966501484701");
                                </div>
                                <div class="tab-pane" id="node">
                                    var request = require('request'); //bash: طلب تثبيت npm
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        Message: 'اهلا بك فى واتس لوب تجربة ارسال رسالة',
                                        Type: 0, // نوع الرساله (نص)
                                    };
                                    // ارسل طلب
                                    request.post({
                                        url: url,
                                        method: "POST",
                                        body: data
                                    });
                                </div>
                                <div class="tab-pane" id="jquery">
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        Message: 'اهلا بك فى واتس لوب تجربة ارسال رسالة',
                                        Type: 0, // نوع الرساله (نص)
                                    };
                                    // Send a request
                                    $.ajax(url, {
                                        type : 'POST'
                                        dataType: "json",
                                        data : data,
                                    });
                                </div>
                                <div class="tab-pane" id="curl">
                                    curl \
                                    -d '{"InstanceId": "xxxxx","Token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","Number": "966501484701","Message": "اهلا بك فى واتس لوب تجربة ارسال رسالة","Type": "0"}' \ # البيانات المرسلة
                                    -X POST \ # Type = POST
                                    "https://whatsloop.net/API/Send.php" # رابط التوجيه لارسال الرسائل
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase2" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""><i class="mdi mdi-minus"></i></a>
                    </div>
                    <h5 class="card-title mb-0 text-white">{{ trans('main.send_file') }}</h5>
                </div>
                <div id="cardCollpase2" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_file_p1') }}</p>
                        <p>{{ trans('main.send_file_p2') }}</p>
                        <p>{{ trans('main.send_file_p3') }}</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item"><a href="#php" data-toggle="tab" aria-expanded="false" class="nav-link active">PHP</a></li>
                                <li class="nav-item"><a href="#phpclass" data-toggle="tab" aria-expanded="true" class="nav-link">PHP Class</a></li>
                                <li class="nav-item"><a href="#node" data-toggle="tab" aria-expanded="false" class="nav-link">Node.js</a></li>
                                <li class="nav-item"><a href="#jquery" data-toggle="tab" aria-expanded="false" class="nav-link">JQuery</a></li>
                                <li class="nav-item"><a href="#curl" data-toggle="tab" aria-expanded="false" class="nav-link">Curl (Bash)</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="php">
                                    $data = [
                                        'InstanceId' => 'xxxxx', // رقم القناة
                                        'Token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        'Number' => '966501484701', // هاتف المستقبل
                                        'MessageFileCaption' => 'النص مع الرسالة', // النص مع الملف ( مع الصورة فقط)
                                        'URL' => 'https://whatsloop.net/resources/images/Pic.jpg', // رابط الملف او الصورة
                                        'Type' => 1, // نوع الرساله (ملف)
                                    ];
                                    // رابط التوجيه لارسال الرسائل
                                    $url = 'https://whatsloop.net/API/Send.php';
                                    // تقديم طلب POST
                                    $options = stream_context_create(['http' => [
                                            'method'  => 'POST',
                                            'content' => http_build_query($data)
                                        ]
                                    ]);
                                    // ارسل طلب
                                    $result = file_get_contents($url, false, $options);
                                </div>
                                <div class="tab-pane" id="phpclass">
                                    include 'Whatsloop.class.php';
                                    $InstanceId = "xxxx";
                                    $WhatsLoopToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
                                    $WhatsLoop = new Whatsloop($InstanceId, $WhatsLoopToken);
                                    $WhatsLoop->sendFile("966501484701","https://whatsloop.net/resources/images/Pic.jpg","النص مع الرسالة" );
                                </div>
                                <div class="tab-pane" id="node">
                                    var request = require('request'); //bash: طلب تثبيت npm
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        MessageFileCaption: 'النص مع الرسالة',
                                        URL: 'https://whatsloop.net/resources/images/Pic.jpg', // رابط الملف او الصورة
                                        Type: 1, // نوع الرساله (ملف)
                                    };
                                    // ارسل طلب
                                    request.post({
                                        url: url,
                                        method: "POST",
                                        body: data
                                    });
                                </div>
                                <div class="tab-pane" id="jquery">
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        MessageFileCaption: 'النص مع الرسالة',
                                        URL: 'https://whatsloop.net/resources/images/Pic.jpg',
                                        Type: 1, // نوع الرساله (ملف)
                                    };
                                    // Send a request
                                    $.ajax(url, {
                                        type : 'POST'
                                        dataType: "json",
                                        data : data,
                                    });
                                </div>
                                <div class="tab-pane" id="curl">
                                    curl \
                                    -d '{"InstanceId": "xxxxx","Token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","Number": "966501484701","MessageFileCaption": "النص مع الرسالة","URL": "https://whatsloop.net/resources/images/Pic.jpg","Type": "1"}' \ # البيانات المرسلة
                                    -X POST \ # Type = POST
                                    "https://whatsloop.net/API/Send.php" # رابط التوجيه لارسال الرسائل
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
    </div>
    
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""><i class="mdi mdi-minus"></i></a>
                    </div>
                    <h5 class="card-title mb-0 text-white">{{ trans('main.send_sound') }}</h5>
                </div>
                <div id="cardCollpase3" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_sound_p1') }}</p>
                        <p>{{ trans('main.send_sound_p2') }} :</p>
                        <a href="https://audio.online-convert.com/convert-to-ogg" target="_blank">https://audio.online-convert.com/convert-to-ogg</a>
                        <p>{{ trans('main.send_sound_p3') }}</p>
                        <img src="{{ asset('images/soundTips.png') }}" alt="">
                        <p>{{ trans('main.send_file_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item"><a href="#php" data-toggle="tab" aria-expanded="false" class="nav-link active">PHP</a></li>
                                <li class="nav-item"><a href="#phpclass" data-toggle="tab" aria-expanded="true" class="nav-link">PHP Class</a></li>
                                <li class="nav-item"><a href="#node" data-toggle="tab" aria-expanded="false" class="nav-link">Node.js</a></li>
                                <li class="nav-item"><a href="#jquery" data-toggle="tab" aria-expanded="false" class="nav-link">JQuery</a></li>
                                <li class="nav-item"><a href="#curl" data-toggle="tab" aria-expanded="false" class="nav-link">Curl (Bash)</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="php">
                                    $data = [
                                        'InstanceId' => 'xxxxx', // رقم القناة
                                        'Token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        'Number' => '966501484701', // هاتف المستقبل
                                        'URL' => 'https://whatsloop.net/resources/images/Audio.ogg', // رابط الملف او الصورة
                                        'Type' => 3, // نوع الرساله (صوت)
                                    ];
                                    // رابط التوجيه لارسال الرسائل
                                    $url = 'https://whatsloop.net/API/Send.php';
                                    // تقديم طلب POST
                                    $options = stream_context_create(['http' => [
                                            'method'  => 'POST',
                                            'content' => http_build_query($data)
                                        ]
                                    ]);
                                    // ارسل طلب
                                    $result = file_get_contents($url, false, $options);
                                </div>
                                <div class="tab-pane" id="phpclass">
                                    include 'Whatsloop.class.php';
                                    $InstanceId = "xxxx";
                                    $WhatsLoopToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
                                    $WhatsLoop = new Whatsloop($InstanceId, $WhatsLoopToken);
                                    $WhatsLoop->sendVoice("966501484701","https://whatsloop.net/resources/images/Audio.ogg");
                                </div>
                                <div class="tab-pane" id="node">
                                    var request = require('request'); //bash: طلب تثبيت npm
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        URL: 'https://whatsloop.net/resources/images/Audio.ogg', // رابط الملف او الصورة
                                        Type: 3, // نوع الرساله (صوت)
                                    };
                                    // ارسل طلب
                                    request.post({
                                        url: url,
                                        method: "POST",
                                        body: data
                                    });
                                </div>
                                <div class="tab-pane" id="jquery">
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        URL: 'https://whatsloop.net/resources/images/Audio.ogg',
                                        Type: 1, // نوع الرساله (صوت)
                                    };
                                    // Send a request
                                    $.ajax(url, {
                                        type : 'POST'
                                        dataType: "json",
                                        data : data,
                                    });
                                </div>
                                <div class="tab-pane" id="curl">
                                    curl \
                                    -d '{"InstanceId": "xxxxx","Token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","Number": "966501484701","URL": "https://whatsloop.net/resources/images/Audio.ogg","Type": "3"}' \ # البيانات المرسلة
                                    -X POST \ # Type = POST
                                    "https://whatsloop.net/API/Send.php" # رابط التوجيه لارسال الرسائل
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""><i class="mdi mdi-minus"></i></a>
                    </div>
                    <h5 class="card-title mb-0 text-white">{{ trans('main.send_location') }}</h5>
                </div>
                <div id="cardCollpase3" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_location_p1') }}</p>
                        {{ trans('main.example') }} : <a href="https://www.google.com.eg/maps/place/Digital+Servers+Center/@21.5982195,39.1586724,17z/data=!3m1!4b1!4m5!3m4!1s0x15c3d09b97e2fb0d:0x3bafaf5c1752cb0c!8m2!3d21.5982195!4d39.1608611" target="_blank">https://www.google.com.eg/maps/place/Digital+Servers+Center/@21.5982195,39.1586724,17z/data=!3m1!4b1!4m5!3m4!1s0x15c3d09b97e2fb0d:0x3bafaf5c1752cb0c!8m2!3d21.5982195!4d39.1608611</a>
                        <p>{{ trans('main.send_location_p2') }}</p>
                        <p>{{ trans('main.send_location_p3') }}</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item"><a href="#php" data-toggle="tab" aria-expanded="false" class="nav-link active">PHP</a></li>
                                <li class="nav-item"><a href="#phpclass" data-toggle="tab" aria-expanded="true" class="nav-link">PHP Class</a></li>
                                <li class="nav-item"><a href="#node" data-toggle="tab" aria-expanded="false" class="nav-link">Node.js</a></li>
                                <li class="nav-item"><a href="#jquery" data-toggle="tab" aria-expanded="false" class="nav-link">JQuery</a></li>
                                <li class="nav-item"><a href="#curl" data-toggle="tab" aria-expanded="false" class="nav-link">Curl (Bash)</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="php">
                                    $data = [
                                        'InstanceId' => 'xxxxx', // رقم القناة
                                        'Token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        'Number' => '966501484701', // هاتف المستقبل
                                        'Location' => '21.59822,39.160861', // النص مع الملف ( مع الصورة فقط)
                                        'Message' => '6081 Quraysh Al Bawadi Jeddah 23531 Saudi Arabia', // رابط الملف او الصورة
                                        'Type' => 6, // نوع الرساله (موقع)
                                    ];
                                    // رابط التوجيه لارسال الرسائل
                                    $url = 'https://whatsloop.net/API/Send.php';
                                    // تقديم طلب POST
                                    $options = stream_context_create(['http' => [
                                            'method'  => 'POST',
                                            'content' => http_build_query($data)
                                        ]
                                    ]);
                                    // ارسل طلب
                                    $result = file_get_contents($url, false, $options);
                                </div>
                                <div class="tab-pane" id="phpclass">
                                    include 'Whatsloop.class.php';
                                    $InstanceId = "xxxx";
                                    $WhatsLoopToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
                                    $WhatsLoop = new Whatsloop($InstanceId, $WhatsLoopToken);
                                    $WhatsLoop->sendLocation("966501484701","21.59822,39.160861","6081 Quraysh Al Bawadi Jeddah 23531 Saudi Arabia", );
                                </div>
                                <div class="tab-pane" id="node">
                                    var request = require('request'); //bash: طلب تثبيت npm
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        Location: '21.59822,39.160861',
                                        Message: '6081 Quraysh Al Bawadi Jeddah 23531 Saudi Arabia', // رابط الملف او الصورة
                                        Type: 6, // نوع الرساله (موقع)
                                    };
                                    // ارسل طلب
                                    request.post({
                                        url: url,
                                        method: "POST",
                                        body: data
                                    });
                                </div>
                                <div class="tab-pane" id="jquery">
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        Location: '21.59822,39.160861',
                                        Message: '6081 Quraysh Al Bawadi Jeddah 23531 Saudi Arabia',
                                        Type: 1, // نوع الرساله (ملف)
                                    };
                                    // Send a request
                                    $.ajax(url, {
                                        type : 'POST'
                                        dataType: "json",
                                        data : data,
                                    });
                                </div>
                                <div class="tab-pane" id="curl">
                                    curl \
                                    -d '{"InstanceId": "xxxxx","Token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","Number": "966501484701","Location": "21.59822,39.160861","Message": "6081 Quraysh Al Bawadi Jeddah 23531 Saudi Arabia","Type": "6"}' \ # البيانات المرسلة
                                    -X POST \ # Type = POST
                                    "https://whatsloop.net/API/Send.php" # رابط التوجيه لارسال الرسائل
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""><i class="mdi mdi-minus"></i></a>
                    </div>
                    <h5 class="card-title mb-0 text-white">{{ trans('main.send_contact') }}</h5>
                </div>
                <div id="cardCollpase3" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_contact_p1') }}</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item"><a href="#php" data-toggle="tab" aria-expanded="false" class="nav-link active">PHP</a></li>
                                <li class="nav-item"><a href="#phpclass" data-toggle="tab" aria-expanded="true" class="nav-link">PHP Class</a></li>
                                <li class="nav-item"><a href="#node" data-toggle="tab" aria-expanded="false" class="nav-link">Node.js</a></li>
                                <li class="nav-item"><a href="#jquery" data-toggle="tab" aria-expanded="false" class="nav-link">JQuery</a></li>
                                <li class="nav-item"><a href="#curl" data-toggle="tab" aria-expanded="false" class="nav-link">Curl (Bash)</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="php">
                                    $data = [
                                        'InstanceId' => 'xxxxx', // رقم القناة
                                        'Token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        'Number' => '966501484701', // هاتف المستقبل
                                        'Contact' => '966501484701', // رقم الهاتف المرسل
                                        'Type' => 5, // نوع الرساله (جهه اتصال)
                                    ];
                                    // رابط التوجيه لارسال الرسائل
                                    $url = 'https://whatsloop.net/API/Send.php';
                                    // تقديم طلب POST
                                    $options = stream_context_create(['http' => [
                                            'method'  => 'POST',
                                            'content' => http_build_query($data)
                                        ]
                                    ]);
                                    // ارسل طلب
                                    $result = file_get_contents($url, false, $options);
                                </div>
                                <div class="tab-pane" id="phpclass">
                                    include 'Whatsloop.class.php';
                                    $InstanceId = "xxxx";
                                    $WhatsLoopToken = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
                                    $WhatsLoop = new Whatsloop($InstanceId, $WhatsLoopToken);
                                    $WhatsLoop->sendContact("966501484701","966501484701" );
                                </div>
                                <div class="tab-pane" id="node">
                                    var request = require('request'); //bash: طلب تثبيت npm
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        Contact: '966501484701', // رقم الهاتف المرسل
                                        Type: 5, // نوع الرساله (جهه اتصال)
                                    };
                                    // ارسل طلب
                                    request.post({
                                        url: url,
                                        method: "POST",
                                        body: data
                                    });
                                </div>
                                <div class="tab-pane" id="jquery">
                                    // رابط التوجيه لارسال الرسائل
                                    var url = 'https://whatsloop.net/API/Send.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف المستقبل
                                        Contact: '966501484701', // رقم الهاتف المرسل
                                        Type: 5, // نوع الرساله (جهه اتصال)
                                    };
                                    // Send a request
                                    $.ajax(url, {
                                        type : 'POST'
                                        dataType: "json",
                                        data : data,
                                    });
                                </div>
                                <div class="tab-pane" id="curl">
                                    curl \
                                    -d '{"InstanceId": "xxxxx","Token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","Number": "966501484701","Contact": "966501484701","Type": "5"}' \ # البيانات المرسلة
                                    -X POST \ # Type = POST
                                    "https://whatsloop.net/API/Send.php" # رابط التوجيه لارسال الرسائل
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header bg-dark py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""><i class="mdi mdi-minus"></i></a>
                    </div>
                    <h5 class="card-title mb-0 text-white">{{ trans('main.edit_contact') }}</h5>
                </div>
                <div id="cardCollpase3" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.edit_contact_p1') }}</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code">
                            <ul class="nav nav-tabs nav-bordered">
                                <li class="nav-item"><a href="#php" data-toggle="tab" aria-expanded="false" class="nav-link active">PHP</a></li>
                                <li class="nav-item"><a href="#node" data-toggle="tab" aria-expanded="false" class="nav-link">Node.js</a></li>
                                <li class="nav-item"><a href="#jquery" data-toggle="tab" aria-expanded="false" class="nav-link">JQuery</a></li>
                                <li class="nav-item"><a href="#curl" data-toggle="tab" aria-expanded="false" class="nav-link">Curl (Bash)</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="php">
                                    $data = [
                                        'InstanceId' => 'xxxxx', // رقم القناة
                                        'Token' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        'Number' => '966501484701', // هاتف جهة الاتصال
                                        'NewName' => 'New Name Here', // الاسم الجديد لجهة الاتصال
                                    ];
                                    // رابط التوجيه لتغير اسم جهة الاتصال
                                    $url = 'https://whatsloop.net/API/UpdateContact.php';
                                    // تقديم طلب POST
                                    $options = stream_context_create(['http' => [
                                            'method'  => 'POST',
                                            'content' => http_build_query($data)
                                        ]
                                    ]);
                                    // ارسل طلب
                                    $result = file_get_contents($url, false, $options);
                                </div>
                                <div class="tab-pane" id="node">
                                    var request = require('request'); //bash: طلب تثبيت npm
                                    // رابط التوجيه لتغير اسم جهة الاتصال
                                    var url = 'https://whatsloop.net/API/UpdateContact.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف جهة الاتصال
                                        NewName: 'New Name Here', // الاسم الجديد لجهة الاتصال
                                    };
                                    // ارسل طلب
                                    request.post({
                                        url: url,
                                        method: "POST",
                                        body: data
                                    });
                                </div>
                                <div class="tab-pane" id="jquery">
                                    // رابط التوجيه لتغير اسم جهة الاتصال
                                    var url = 'https://whatsloop.net/API/UpdateContact.php';
                                    var data = {
                                        InstanceId: 'xxxxx', // رقم القناة
                                        Token: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', // رمز المصادقة ( Token )
                                        Number: '966501484701', // هاتف جهة الاتصال
                                        NewName: 'New Name Here', // الاسم الجديد لجهة الاتصال
                                    };
                                    // Send a request
                                    $.ajax(url, {
                                        type : 'POST'
                                        dataType: "json",
                                        data : data,
                                    });
                                </div>
                                <div class="tab-pane" id="curl">
                                    curl \
                                    -d '{"InstanceId": "xxxxx","Token": "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx","Number": "966501484701","NewName": "New Name Here"}' \ # البيانات المرسلة
                                    -X POST \ # Type = POST
                                    "https://whatsloop.net/API/UpdateContact.php" # رابط التوجيه لتغير اسم جهة الاتصال
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- end col-->
    </div>
   
    
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('components/profile_services.js') }}" type="text/javascript"></script>
@endsection
