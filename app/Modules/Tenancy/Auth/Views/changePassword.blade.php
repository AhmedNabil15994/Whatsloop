<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>واتس لووب | Whats Loop | {{ trans('auth.change') }}</title>
        <meta name="description" content="#" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        @include('tenant.Layouts.head')
        <link rel="stylesheet" href="{{ asset('css/intlTelInput.css') }}">
    </head>
    <body class="authPages loading authentication-bg authentication-bg-pattern">
        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card bg-pattern">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <div class="auth-logo">
                                        <a href="#" class="logo logo-dark text-center">
                                            <span class="logo-lg">
                                                <img src="{{ asset('images/logo.png') }}" alt="" height="22">
                                            </span>
                                        </a>
                                        <a href="#" class="logo logo-light text-center">
                                            <span class="logo-lg">
                                                <img src="{{ asset('images/logo.png') }}" alt="" height="22">
                                            </span>
                                        </a>
                                    </div>
                                    <p class="text-muted mb-4 mt-3">{{ trans('auth.change') }}</p>
                                </div>
                                <form action="{{ URL::to('/completeReset') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="password">{{ trans('auth.password') }}</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" class="form-control" name="password" placeholder="{{ trans('auth.passwordPlaceHolder') }}">
                                            <div class="input-group-append" data-password="false">
                                                <div class="input-group-text">
                                                    <span class="password-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="password">{{ trans('auth.passwordConf') }}</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" class="form-control" name="password_confirmation" placeholder="{{ trans('auth.passwordConfPlaceHolder') }}">
                                            <div class="input-group-append" data-password="false">
                                                <div class="input-group-text">
                                                    <span class="password-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 text-center">
                                        <button class="ladda-button btn btn-primary btn-block loginBut" dir="ltr" data-style="expand-right">
                                            <span class="ladda-label">{{ trans('auth.change') }}</span>
                                            <span class="ladda-spinner"></span>
                                            <div class="ladda-progress" style="width: 75px;"></div>
                                        </button>
                                    </div>
                                </form>
                            </div> <!-- end card-body -->
                        </div>
                        <!-- end card -->
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p> <a href="{{ URL::to('/login') }}" class="text-white-50 ml-1">{{ trans('auth.loginButton') }}</a></p>
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->
        @include('tenant.Layouts.scripts')
        @include('tenant.Partials.notf_messages')
    </body>
</html>