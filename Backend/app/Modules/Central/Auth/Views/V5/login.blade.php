<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
    <head>
        <meta charset="UTF-8" />
        <!-- IE Compatibility Meta -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- First Mobile Meta  -->
        <meta name="viewport" content="width=device-width, height=device-height ,  maximum-scale=1 , initial-scale=1">
        <title>واتس لووب | Whats Loop | {{ trans('auth.login') }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/flaticon.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/jquery-ui.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/intlTelInput.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/animate.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/bootstrap.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/bootstrap-rtl.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/font-awesome.min.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/owl.carousel.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/style.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/responisve.css') }}" />
        <link rel="stylesheet" href="{{ asset('tenancy/assets/V5/css/dark.css') }}" />
        <link href="{{ asset('tenancy/assets/V5/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
        <!--[if lt IE 9]>
            <script src="js/html5shiv.min.js"></script>
            <script src="js/respond.min.js"></script>
        <![endif]-->   
    </head>
    <body>
    
        <div class="loginPage clearfix">
            <div class="right">
                <a href="#" class="logoLogin"><img src="{{ asset('tenancy/assets/V5/images/logo.png') }}" alt="" /></a>
                <input type="hidden" name="country_code" value="{{ $data->code }}">
                <div class="center">
                    <form class="formLogin" id="loginForm">
                        @csrf
                        <h2 class="title">{{ trans('auth.loginToPanel') }}</h2>
                        <div class="inputStyle telStyle">
                            <input class="phone" type="tel"  id="telephone" name="phone" placeholder="{{ trans('auth.phonePlaceHolder') }}" />
                            <i class="flaticon-phone-call"></i>
                        </div>
                        <div class="inputStyle">
                            <input type="password" name="password" placeholder="{{ trans('auth.passwordPlaceHolder') }}" />
                            <i class="flaticon-shopping-bag"></i>
                        </div>
                        <div class="codes inputStyle hidden">
                            <input placeholder="{{ trans('auth.codePlaceHolder') }}" type="tel" name="code">
                            <i class="flaticon-phone-call"></i>
                        </div>
                        <button class="btnStyle loginBut">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                              <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"/>
                                <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"/>
                              </g>
                            </svg>
                            {{ trans('auth.loginButton') }}
                        </button>
                        <a href="{{ URL::to('/checkAvailability') }}" class="btnStyle forgetBtn" style="background: #eee;padding-top: 20px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                              <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"/>
                                <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"/>
                              </g>
                            </svg>
                            {{ trans('auth.newClient') }}
                        </a>
                        <center>
                            <a href="{{ URL::to('/getResetPassword') }}" class="forgetBtn">{{ trans('auth.forgotPassword') }}</a>
                        </center>
                    </form>
                        
                </div>
                <p class="copyrights">{{ trans('main.rights') }} - &copy; {{ trans('main.appName') }} {{ date('Y') }}</p>
            </div>
            <div class="left">
                <img src="{{ asset('tenancy/assets/V5/images/loginBg.png') }}" alt="" />
            </div>
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
        <script src="{{ asset('tenancy/assets/V5/js/toastr.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/components/notifications.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/js/utils.js') }}" type="text/javascript"></script>
        <script src="{{ asset('tenancy/assets/V5/js/custom.js') }}"></script>
        <script src="{{ asset('tenancy/assets/V5/components/login.js') }}" type="text/javascript"></script>
        
        @include('tenant.Partials.notf_messages')
    </body>

</html>
