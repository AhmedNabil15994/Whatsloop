<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
    <head>
        <meta charset="utf-8" />
        <title>واتس لووب | Whats Loop | {{ trans('auth.login') }}</title>
        <meta name="description" content="#" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('tenancy/assets/css/login-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('tenancy/assets/css/intlTelInput.css') }}">
        <link href="{{ asset('tenancy/assets/css/toastr.min.css') }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('tenancy/assets/css/touches.css') }}">
        <link rel="stylesheet" href="{{ asset('tenancy/assets/css/login-style.css') }}">
    </head>
    <body class="authPages loading authentication-bg authentication-bg-pattern">
        <section class="main--page">
            <div class="container-fluid">
                <div class="row login-page">
                    <div class="col-lg-12 col-xl-6 control-side">
                        <div class="logo">
                            <img src="{{ asset('tenancy/assets/images/logo.png') }}" alt="Whatsapp-loop">
                        </div>
                        <div class="user-form">
                            <input type="hidden" name="country_code" value="{{ $data->code }}">
                            <form action="">
                                @csrf
                                <div class="form--title">{{ trans('auth.loginToPanel') }}</div>
                                <input type="tel" id="telephone" name="phone" placeholder="{{ trans('auth.phonePlaceHolder') }}">
                                <input type="password" name="password" placeholder="{{ trans('auth.passwordPlaceHolder') }}">
                                <div class="codes hidden">
                                    <input placeholder="{{ trans('auth.codePlaceHolder') }}" type="tel" name="code">
                                </div>
                                <button type="button" class="loginBut">{{ trans('auth.loginButton') }}</button>
                                <a href="{{ URL::to('/getResetPassword') }}" class="nav-link theme__dark">{{ trans('auth.forgotPassword') }}</a>
                                <a href="{{ URL::to('/register') }}" class="nav-link theme__light">{{ trans('auth.newClient') }}</a>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-6 slogan-side">
                        <div class="overlay"></div>
                        <img src="{{ asset('tenancy/assets/images/whatsapp-chat.png') }}" alt="">
                    </div>
                </div>
            </div>
        </section>


        <!-- end page -->
        <script src="{{ asset('tenancy/assets/js/vendor.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/js/login-bootstrap.min.js') }}"></script>
        {{-- <script src="{{ asset('tenancy/assets/js/login-main.js') }}"></script> --}}
        <script src="{{ asset('tenancy/assets/js/toastr.min.js') }}"></script>
        <script src="{{ asset('tenancy/assets/components/notifications.js') }}"></script>
        <script src="{{ asset('tenancy/assets/js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('tenancy/assets/js/utils.js') }}" type="text/javascript"></script>
        <script src="{{ asset('tenancy/assets/components/login.js') }}" type="text/javascript"></script>
        @include('central.Partials.notf_messages')
    </body>
</html>