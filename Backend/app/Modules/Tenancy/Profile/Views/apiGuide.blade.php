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
                                    <a href="{{ asset('codes/phpClass/MainWhatsLoop.zip') }}" class="btn btn-success mr-2">{{ trans('main.downloadLibrary') }}</a>
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
                <div class="card-header label label-light-success py-3">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""></a>
                    </div>
                    <h5 class="card-title mb-0 text-black">{{ trans('main.send_text') }}</h5>
                </div>
                <div id="cardCollpase1" class="collapse example show" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_text_p1') }} .</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code example">
                            <div class="panel panel-primary tabs-style-1">
                                <div class=" tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <!-- Tabs -->
                                        <ul class="nav panel-tabs main-nav-line">
                                            <li><a href="#php" class="nav-link active" data-toggle="tab">PHP - CURL</a></li>
                                            <li><a href="#phpclass" class="nav-link" data-toggle="tab">PHP Class</a></li>
                                            <li><a href="#curl" class="nav-link" data-toggle="tab">Curl (Bash)</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body main-content-body-right border">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="php">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendMessage.php'))) }}
                                        </div>
                                        <div class="tab-pane" id="phpclass">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendMessage.php'))) }}
                                        </div>
                                        <div class="tab-pane" id="curl">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendMessage.php'))) }}
                                        </div>
                                    </div>
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
                <div class="card-header label label-light-success py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase2" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""></a>
                    </div>
                    <h5 class="card-title mb-0 text-black">{{ trans('main.send_file') }}</h5>
                </div>
                <div id="cardCollpase2" class="collapse example" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_file_p1') }}</p>
                        <p>{{ trans('main.send_file_p2') }}</p>
                        <p>{{ trans('main.send_file_p3') }}</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code example">
                            <div class="panel panel-primary tabs-style-1">
                                <div class=" tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <!-- Tabs -->
                                        <ul class="nav panel-tabs main-nav-line">
                                            <li><a href=".php" class="nav-link active" data-toggle="tab">PHP - CURL</a></li>
                                            <li><a href=".phpclass" class="nav-link" data-toggle="tab">PHP Class</a></li>
                                            <li><a href=".curl" class="nav-link" data-toggle="tab">Curl (Bash)</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body main-content-body-right border">
                                    <div class="tab-content">
                                        <div class="tab-pane active php">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendFile.php'))) }}
                                        </div>
                                        <div class="tab-pane phpclass">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendFile.php'))) }}
                                        </div>
                                        <div class="tab-pane curl">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendFile.php'))) }}
                                        </div>
                                    </div>
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
                <div class="card-header label label-light-success py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase2" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""></a>
                    </div>
                    <h5 class="card-title mb-0 text-black">{{ trans('main.send_sound') }}</h5>
                </div>
                <div id="cardCollpase2" class="collapse example" style="">
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
                        <div class="col code example">
                            <div class="panel panel-primary tabs-style-1">
                                <div class=" tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <!-- Tabs -->
                                        <ul class="nav panel-tabs main-nav-line">
                                            <li><a href=".php2" class="nav-link active" data-toggle="tab">PHP - CURL</a></li>
                                            <li><a href=".phpclass2" class="nav-link" data-toggle="tab">PHP Class</a></li>
                                            <li><a href=".curl2" class="nav-link" data-toggle="tab">Curl (Bash)</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body main-content-body-right border">
                                    <div class="tab-content">
                                        <div class="tab-pane active php2">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendPTT.php'))) }}
                                        </div>
                                        <div class="tab-pane phpclass2">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendPTT.php'))) }}
                                        </div>
                                        <div class="tab-pane curl2">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendPTT.php'))) }}
                                        </div>
                                    </div>
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
                <div class="card-header label label-light-success py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase2" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""></a>
                    </div>
                    <h5 class="card-title mb-0 text-black">{{ trans('main.send_location') }}</h5>
                </div>
                <div id="cardCollpase2" class="collapse example" style="">
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
                        <div class="col code example">
                            <div class="panel panel-primary tabs-style-1">
                                <div class=" tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <!-- Tabs -->
                                        <ul class="nav panel-tabs main-nav-line">
                                            <li><a href=".php2" class="nav-link active" data-toggle="tab">PHP - CURL</a></li>
                                            <li><a href=".phpclass2" class="nav-link" data-toggle="tab">PHP Class</a></li>
                                            <li><a href=".curl2" class="nav-link" data-toggle="tab">Curl (Bash)</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body main-content-body-right border">
                                    <div class="tab-content">
                                        <div class="tab-pane active php2">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendLocation.php'))) }}
                                        </div>
                                        <div class="tab-pane phpclass2">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendLocation.php'))) }}
                                        </div>
                                        <div class="tab-pane curl2">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendLocation.php'))) }}
                                        </div>
                                    </div>
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
                <div class="card-header label label-light-success py-3 text-white">
                    <div class="card-widgets">
                        <a data-toggle="collapse" href="#cardCollpase2" role="button" aria-expanded="true" aria-controls="cardCollpase2" class=""></a>
                    </div>
                    <h5 class="card-title mb-0 text-black">{{ trans('main.send_link') }}</h5>
                </div>
                <div id="cardCollpase2" class="collapse example" style="">
                    <div class="card-body">
                        <p>{{ trans('main.send_link_p1') }}</p>
                        <p>{{ trans('main.send_text_p2') }}</p>
                        <div class="col text-right mt-5">
                            <span class="example-toggle example-toggled" data-toggle="tooltip" title="" data-original-title="{{ trans('main.show_code') }}"><i class="fa fa-eye-slash"></i></span>
                            <span class="example-copy" data-toggle="tooltip" title="" data-original-title="{{ trans('main.copy_code') }}"><i class="fa fa-copy"></i></span>
                        </div>
                        <div class="col code example">
                            <div class="panel panel-primary tabs-style-1">
                                <div class=" tab-menu-heading">
                                    <div class="tabs-menu1">
                                        <!-- Tabs -->
                                        <ul class="nav panel-tabs main-nav-line">
                                            <li><a href=".php4" class="nav-link active" data-toggle="tab">PHP - CURL</a></li>
                                            <li><a href=".phpclass4" class="nav-link" data-toggle="tab">PHP Class</a></li>
                                            <li><a href=".curl4" class="nav-link" data-toggle="tab">Curl (Bash)</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body main-content-body-right border">
                                    <div class="tab-content">
                                        <div class="tab-pane active php4">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpCurl/sendLink.php'))) }}
                                        </div>
                                        <div class="tab-pane phpclass4">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/phpClass/sendLink.php'))) }}
                                        </div>
                                        <div class="tab-pane curl4">
                                            {{ preg_replace('/\b\d+\b/', '', highlight_file(public_path('tenancy/assets/codes/curl/sendLink.php'))) }}
                                        </div>
                                    </div>
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
<script src="{{ asset('plugins/tabs/jquery.multipurpose_tabcontent.js') }}"></script>
<script src="{{ asset('components/apiGuide.js') }}" type="text/javascript"></script>
@endsection
