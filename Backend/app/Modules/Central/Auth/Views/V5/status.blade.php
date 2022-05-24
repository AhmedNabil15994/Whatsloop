<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
    <head>
        <meta charset="UTF-8" />
        <!-- IE Compatibility Meta -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- First Mobile Meta  -->
        <meta name="viewport" content="width=device-width, height=device-height ,  maximum-scale=1 , initial-scale=1">
        <title>واتس لووب | Whats Loop | @yield('title')</title>
        <meta name="description" content="#" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('tenancy/assets/V5/images/logoChannel.png') }}" type="image/ico" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/font.css') }}" />

        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/flaticon.css') }}" />

        <link rel="stylesheet" type="text/css" href="{{ asset('tenancy/assets/V5/plugins/sweet-alert/sweetalert.css') }}">
        <link href="{{ asset('tenancy/assets/V5/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/jquery-ui.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/intlTelInput.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/animate.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/font-awesome.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/owl.carousel.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/toastr.min.css') }}"  type="text/css">
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/buttons.css') }}" />
        @if(DIRECTION == 'rtl')
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/bootstrap-rtl.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/style.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/responisve.css') }}" />
        @else
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/ltr.css') }}" />
        @endif
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/dark.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/touches.css') }}" />
        <style>
            .accordion,
            .helpCenter .contentStyle .contentTitle {
                background-color: #e8e8e8;
            }
            .accordion .accordion-title:not(.contentTitle){
                display: inline-block;
                background-color: #e8e8e8;
                width: 80%;
            }
            span.status{
                display: inline-block;
                width: 16px;
                height: 16px;
                border-radius: 50%;
                margin: 20px;
            }
            span.status.success{
                background-color: #00BFB5;
            }
            span.status.danger{
                background-color: #FF6D6D;
            }
            span.status2{
                margin: 5px;
            }
            p.mb-3{
                padding: 25px;
                white-space: break-spaces;
            }
            .titleHelp,.subTitle{
                width: 49%;
            }
            .numbTicket {
                min-width: 120px;
                height: 50px;
                background-color: #fff;
                padding-top: 8px;
                max-width: fit-content;
            }
            .numbTicket a {
                width: 90px;
                height: 35px;
                border-radius: 5px;
                background-color: #00BFB5;
                color: #fff;
                font-size: 16px;
                font-family: "Tajawal-Medium";
                display: block;
                margin: 0 auto;
                text-align: center;
                line-height: 35px;
            }
            .numbTicket:after {
                content: "";
                position: absolute;
                left: 7px;
                top: 1px;
                height: 50px;
                background: url({{asset('tenancy/assets/V5/images/Subtraction%203.png')}}) no-repeat;
                width: 18px;
            }
            .mt-15{
                margin-top: 15px;
            }
            html[dir="ltr"] .subTitle{
                text-align: right !important;
            }
            html[dir="rtl"] .pull-right{
                float: left !important;
            }
        </style>
    </head>
    <!--end::Head-->

    <body class="">
        <!-- Begin page -->
        <input type="hidden" name="countriesCode" value="{{ Helper::getCountryCode() ? Helper::getCountryCode()->countryCode : 'sa' }}">
        
        <div class="bgOpacity"></div>
        <div class="header clearfix">
            @php
                $currentTime = date('H:i');
                $text = '';
                if($currentTime >= "06:00" && $currentTime <= "11:59"){
                    $text = trans('main.morning');
                }elseif($currentTime >= "12:00" && $currentTime <= "17:59"){
                    $text = trans('main.afternoon');
                }elseif($currentTime >= "18:00" || $currentTime >= "00:00" && $currentTime <= "05:59" ){
                    $text = trans('main.evening');
                }
            @endphp
            <div class="user">
                <i class="icon flaticon-user-3"></i> {{ $text }}
            </div>
            
            <div class="profile">
                <i class="flaticon-menu-1 openProfile"></i>
                <div class="profileStyle">
                    <div class="head">
                        <i class="fa fa-angle-left iconClose"></i>
                        <i class="icon flaticon-user-3"></i>
                        <h2 class="name">{{ trans('main.guest') }}</h2>
                        <span class="account"></span>
                    </div>
                    <ul class="listProfile">
                        
                    </ul>
                    <div class="btnsHeader clearfix">
                        @if(DIRECTION == 'ltr')
                        <a href="#" class="lang user-langs lang-item" data-next-area="ar">ع</a>
                        @else
                        <a href="#" class="lang user-langs lang-item" data-next-area="en">EN</a>
                        @endif
                    </div>
                </div>
            </div>
            @if(DIRECTION == 'ltr')
            <a href="#" class="lang user-langs lang-item" data-next-area="ar">ع</a>
            @else
            <a href="#" class="lang user-langs lang-item" data-next-area="en">EN</a>
            @endif
        </div>

        <div class="qutas-ards" style="background: #f9f9f9;">
            <div class="container">
                <div class="tabs tabs1">
                    <div class="tab tab1">
                        <div class="row mb-2">
                            <h2 class="title">{{ trans('main.statuses') }}</h2>
                        </div>
                        @if(!empty($data->data))
                        <div class="row">

                            @foreach($data->data as $oneItem)
                            <div class="sendMsg accordion" id="accordion">
                                <span class="status {{$oneItem->status == 1 ? 'success' : 'danger'}} pull-right" data-toggle="tooltip" data-placement="top" title="{{ $oneItem->statusText }}"></span>
                                <h2 class="title accordion-title" id="accordion-title ">{{ $oneItem->title }}
                                    @if($oneItem->description != '')
                                    <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="{{ DIRECTION == 'rtl' ? 'left' : 'right' }}" title="{{ $oneItem->description }}"></i>
                                    @endif 
                                </h2>
                                <div class="details accordion-content" id="accordion-content">        
                                    @foreach($oneItem->statuses as $oneStatus)
                                    <div class="desc form mb-2">
                                        {{ $oneStatus->title }}
                                        @if($oneStatus->description != '')
                                        <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="{{ DIRECTION == 'rtl' ? 'left' : 'right' }}" title="{{ $oneStatus->description }}"></i>
                                        @endif 
                                        <span class="status status2 {{$oneStatus->status == 1 ? 'success' : 'danger'}} pull-right" data-toggle="tooltip" data-placement="top" title="{{ $oneStatus->statusText }}"></span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="row text-center">
                            <div class="card-box">
                                <img class="mb-3" src="{{asset('tenancy/assets/V5/images/logo.png')}}" alt="">
                                <h5 class="mt-0 text-da font-16">{{ trans('main.noDataFound') }}</h5>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="qutas-ards" style="background: #f9f9f9;">
            <div class="container">
                <div class="tabs tabs1">
                    <div class="tab tab1">
                        <div class="card changLogs">
                            <div class="row mb-2">
                                <h2 class="title">{{ trans('main.changeLogs') }}</h2>
                            </div>
                            @if(!empty($data->changeLogs))
                            <div class="row">
                                <div class="col-md-8 logs-col">
                                    <div class="content">
                                        @foreach($data->changeLogs as $logKey => $oneLog)

                                        <div class="helpCenter">
                                            @if($oneLog->category != '')
                                            <div class="numbTicket titleHelp pull-left">
                                                <span class="numb"></span>
                                                <a href="#">{{ $oneLog->category }}</a>
                                            </div>
                                            @endif
                                            <span class="subTitle pull-right text-left mt-15">{{ $oneLog->dateForHuman }}</span>
                                            <div class="clearfix"></div>
                                            <div class="accordion {{ $logKey == 0 ? 'active' : '' }}" id="accordion">
                                                <div class="contentStyle {{ $logKey == 0 ? 'active' : '' }}">
                                                    <h2 class="contentTitle accordion-title" id="accordion-title">{{ $oneLog->title }}</h2>
                                                    <div class="details accordion-content" id="accordion-content">
                                                        <p class="mb-3">{!! $oneLog->description !!}</p>
                                                        <img src="{{ $oneLog->photo }}" />
                                                    </div>
                                                </div>                                                
                                            </div>
                                        </div>
                                        <hr class="mb-3">
                                        @endforeach
                                    </div>
                                </div><!-- end col-->
                                <div class="col-md-4">
                                    <div class="content">
                                        <form class="searchCategory">
                                            <h2 class="titleSearch">{{ trans('main.filterByCat') }}</h2>
                                            <div class="filter">
                                                @foreach($data->categories as $categoryKey => $oneCategory)
                                                <label class="checkStyle ckbox">
                                                    <input type="checkbox" name="category_id" data-area="{{ $oneCategory->id }}" {{ Request::has('category_id') && Request::get('category_id') == $oneCategory->id ? 'checked' : '' }} />
                                                    <i></i>
                                                    {{ $oneCategory->title }} 
                                                </label>
                                                @endforeach
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div> 
                            @else
                            <div class="row text-center">
                                <div class="card-box">
                                    <img class="mb-3" src="{{asset('tenancy/assets/V5/images/logo.png')}}" alt="">
                                    <h5 class="mt-0 text-da font-16">{{ trans('main.noDataFound') }}</h5>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p class="copyrights">{{ trans('main.rights') }} - &copy; {{ trans('main.appName') }} {{ date('Y') }}</p>
            <i class="top back-top">
                <svg xmlns="http://www.w3.org/2000/svg" width="27.759" height="25.586" viewBox="0 0 27.759 25.586">
                  <g id="Group_1367" data-name="Group 1367" transform="translate(-36.121 -1712.53)">
                    <path id="Path_915" data-name="Path 915" d="M6601,4290.526,6614.525,4277l13.526,13.526" transform="translate(-6564.526 -2563.763)" fill="none" stroke="#fff" stroke-width="1"/>
                    <path id="Path_916" data-name="Path 916" d="M6601,4290.526,6614.525,4277l13.526,13.526" transform="translate(-6564.526 -2552.763)" fill="none" stroke="#fff" stroke-width="1"/>
                  </g>
                </svg>
            </i>
        </div>

        <div class="menuDownHeight"></div>
        <div class="menuDown">
            <ul class="linksList">
                
            </ul>
        </div>
        

        <script src="{{ asset('tenancy/assets/V5/js/jquery-1.11.2.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/jquery-ui.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/owl.carousel.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/wow.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/intlTelInput.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/Chart.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/circle-progress.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/jquery.nicescroll.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/plugins/moment/moment.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/search.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/libs/dropzone/min/dropzone.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/plugins/sweet-alert/sweetalert.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('tenancy/assets/V5/js/toastr.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/components/notifications.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/components/multi-lang.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/components/multi-channels.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/utils.js') }}" type="text/javascript"></script>
        <script src="{{ asset('tenancy/assets/V5/js/custom.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/components/globals.js') }}"></script>


        @include('tenant.Partials.notf_messages')
    </body>
    <!--end::Body-->
</html>