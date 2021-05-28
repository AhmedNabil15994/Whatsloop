<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}">
    <head>
        <meta charset="utf-8" />
        <title>واتس لووب | Whats Loop | {{ trans('auth.login') }}</title>
        <meta name="description" content="#" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/login-bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/intlTelInput.css') }}">
        <link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
    </head>
    <body class="authPages loading authentication-bg authentication-bg-pattern">
        <section class="main--page">
            <div class="container-fluid">
                <div class="row login-page">
                    <div class="col-lg-12 col-xl-6 control-side">
                        <div class="logo">
                            <img src="{{ asset('images/logo.png') }}" alt="Whatsapp-loop">
                        </div>
                        <div class="user-form">
                            <form action="">
                                @csrf
                                <div class="form--title">{{ trans('auth.loginToPanel') }}</div>
                                <div class="codes">
                                    <input placeholder="{{ trans('auth.codePlaceHolder') }}" type="tel" name="code" value="{{ Request::get('code') }}">
                                </div>
                                <button type="button" class="check">{{ trans('auth.loginButton') }}</button>
                                {{-- <a href="{{ URL::to('/getResetPassword') }}" class="nav-link theme__dark">{{ trans('auth.forgotPassword') }}</a> --}}
                                {{-- <a href="#" class="nav-link theme__light">إنساء حساب جديد</a> --}}
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-6 slogan-side">
                        <div class="overlay"></div>
                        <img src="{{ asset('images/whatsapp-chat.png') }}" alt="">
                    </div>
                </div>
            </div>
        </section>

        @include('tenant.Partials.notf_messages')

        <!-- end page -->
        <script src="{{ asset('js/vendor.min.js') }}"></script>
        <script src="{{ asset('js/login-bootstrap.min.js') }}"></script>
        {{-- <script src="{{ asset('js/login-main.js') }}"></script> --}}
        <script src="{{ asset('js/toastr.min.js') }}"></script>
        <script src="{{ asset('components/notifications.js') }}"></script>
        <script src="{{ asset('js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('js/utils.js') }}" type="text/javascript"></script>
        <script src="{{ asset('components/login.js') }}" type="text/javascript"></script>
    </body>
</html>