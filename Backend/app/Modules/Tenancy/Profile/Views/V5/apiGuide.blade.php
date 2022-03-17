{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css">
    .form .btnsTabs{
        position: unset;
        margin-top: 25px;
        height: 54px;
    }
    .form .content{
        text-align: left;
        direction: ltr;
    }
    .tab{
        border: 1px solid #ddd;
        border-radius: 5px;
    }
    code{
        overflow-wrap: break-word;
    }
</style>
@endsection
@section('content')

<div class="containerCpanel">
    <div class="apiGuide clearfix">
        <h2 class="title">{{ trans('main.intro') }}</h2>
        <div class="details">
            <h2 class="titleApi">{{ trans('main.instructions') }}</h2>
            <div class="desc">
                {{ trans('main.instructions_p1') }}-
                <br>
                {{ trans('main.instructions_p2') }}-
                <br>
                {{ trans('main.instructions_p3') }}-
                <br>
                {{ trans('main.instructions_p4') }}-
            </div>
            <a href="{{ asset('codes/phpClass/MainWhatsLoop.zip') }}" class="updateBtn float-right">{{ trans('main.downloadLibrary') }}</a>
            <div class="clearfix"></div>
        </div>
    </div>
    
    <div class="sendMsg accordion" id="accordion">
        <h2 class="title accordion-title" id="accordion-title ">{{ trans('main.send_text') }}</h2>
        <div class="details accordion-content" id="accordion-content">
            <div class="desc form">
                {{ trans('main.send_text_p1') }}
                <br>
                {{ trans('main.send_text_p2') }}
                <ul class="btnsTabs" id="tabs1">
                    <li id="tab1" class="active">PHP - CURL</li>
                    <li id="tab2">PHP Class</li>
                    <li id="tab3">Curl (Bash)</li>
                </ul>
                <div class="tabs tabs1">
                    <div class="tab tab1 active">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendMessage.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab2">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendMessage.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab3">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendMessage.php'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sendMsg accordion" id="accordion">
        <h2 class="title accordion-title" id="accordion-title ">{{ trans('main.send_file') }}</h2>
        <div class="details accordion-content" id="accordion-content">
            <div class="desc form">
                {{ trans('main.send_file_p1') }}
                <br>
                {{ trans('main.send_file_p2') }}
                <br>
                {{ trans('main.send_file_p3') }}
                <br>
                {{ trans('main.send_text_p2') }}
                <ul class="btnsTabs" id="tabs1">
                    <li id="tab1" class="active">PHP - CURL</li>
                    <li id="tab2">PHP Class</li>
                    <li id="tab3">Curl (Bash)</li>
                </ul>
                <div class="tabs tabs1">
                    <div class="tab tab1 active">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendFile.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab2">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendFile.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab3">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendFile.php'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sendMsg accordion" id="accordion">
        <h2 class="title accordion-title" id="accordion-title ">{{ trans('main.send_sound') }}</h2>
        <div class="details accordion-content" id="accordion-content">
            <div class="desc form">
                {{ trans('main.send_sound_p1') }}
                <br>
                {{ trans('main.send_sound_p2') }}
                <br>
                <a href="https://audio.online-convert.com/convert-to-ogg" target="_blank">https://audio.online-convert.com/convert-to-ogg</a>
                {{ trans('main.send_file_p3') }}
                <br>
                {{ trans('main.send_sound_p3') }}
                <br>
                <img src="{{ asset('images/soundTips.png') }}" alt="">
                <br>
                {{ trans('main.send_file_p2') }}
                <ul class="btnsTabs" id="tabs1">
                    <li id="tab1" class="active">PHP - CURL</li>
                    <li id="tab2">PHP Class</li>
                    <li id="tab3">Curl (Bash)</li>
                </ul>
                <div class="tabs tabs1">
                    <div class="tab tab1 active">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendPTT.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab2">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendPTT.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab3">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendPTT.php'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sendMsg accordion" id="accordion">
        <h2 class="title accordion-title" id="accordion-title ">{{ trans('main.send_location') }}</h2>
        <div class="details accordion-content" id="accordion-content">
            <div class="desc form">
                {{ trans('main.send_location_p1') }}
                <br>
                {{ trans('main.example') }} : <a href="https://www.google.com.eg/maps/place/Digital+Servers+Center/@21.5982195,39.1586724,17z/data=!3m1!4b1!4m5!3m4!1s0x15c3d09b97e2fb0d:0x3bafaf5c1752cb0c!8m2!3d21.5982195!4d39.1608611" target="_blank">https://www.google.com.eg/maps/place/Digital+Servers+Center/@21.5982195,39.1586724,17z/data=!3m1!4b1!4m5!3m4!1s0x15c3d09b97e2fb0d:0x3bafaf5c1752cb0c!8m2!3d21.5982195!4d39.1608611</a>
                <br>
                {{ trans('main.send_location_p2') }}
                <br>
                {{ trans('main.send_location_p3') }}
                <br>
                {{ trans('main.send_text_p2') }}
                <ul class="btnsTabs" id="tabs1">
                    <li id="tab1" class="active">PHP - CURL</li>
                    <li id="tab2">PHP Class</li>
                    <li id="tab3">Curl (Bash)</li>
                </ul>
                <div class="tabs tabs1">
                    <div class="tab tab1 active">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendLocation.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab2">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendLocation.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab3">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendLocation.php'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sendMsg accordion" id="accordion">
        <h2 class="title accordion-title" id="accordion-title ">{{ trans('main.send_link') }}</h2>
        <div class="details accordion-content" id="accordion-content">
            <div class="desc form">
                {{ trans('main.send_link_p1') }}
                <br>
                {{ trans('main.send_text_p2') }}
                <ul class="btnsTabs" id="tabs1">
                    <li id="tab1" class="active">PHP - CURL</li>
                    <li id="tab2">PHP Class</li>
                    <li id="tab3">Curl (Bash)</li>
                </ul>
                <div class="tabs tabs1">
                    <div class="tab tab1 active">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendLink.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab2">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendLink.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab3">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendLink.php'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="sendMsg accordion" id="accordion">
        <h2 class="title accordion-title" id="accordion-title ">{{ trans('main.send_buttons') }}</h2>
        <div class="details accordion-content" id="accordion-content">
            <div class="desc form">
                <ul class="btnsTabs" id="tabs1">
                    <li id="tab1" class="active">PHP - CURL</li>
                    <li id="tab2">PHP Class</li>
                    <li id="tab3">Curl (Bash)</li>
                </ul>
                <div class="tabs tabs1">
                    <div class="tab tab1 active">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendButtons.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab2">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendButtons.php'))) }}
                        </div>
                    </div>
                    <div class="tab tab3">
                        <div class="content">
                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendButtons.php'))) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
