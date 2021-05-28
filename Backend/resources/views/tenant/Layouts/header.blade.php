<!-- Topbar Start -->
<div class="navbar-custom">
    <div class="container-fluid">
        <ul class="list-unstyled topnav-menu float-right mb-0">

            <li class="dropdown d-lg-inline-block topbar-dropdown">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <img src="{{ DIRECTION == 'ltr' ? asset('images/flags/us.jpg') : asset('images/flags/ksa.png') }}" alt="user-image" height="16">
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item lang-item">
                        <img src="{{ asset('images/flags/us.jpg') }}" alt="user-image" class="mr-1" height="12"> <span class="align-middle">{{ trans('main.english') }}</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item lang-item">
                        <img src="{{ asset('images/flags/ksa.png') }}" alt="user-image" class="mr-1" height="12"> <span class="align-middle">{{ trans('main.arabic') }}</span>
                    </a>

                </div>
            </li>

            <li class="dropdown notification-list topbar-dropdown">
                <a class="nav-link dropdown-toggle nav-user mr-0 waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    @php 
                        $userObj = \App\Models\User::getData(\App\Models\User::getOne(USER_ID));
                    @endphp
                    <img src="{{ $userObj->photo }}" alt="user-image" class="rounded-circle">
                    <span class="pro-user-name ml-1">
                        {{ FULL_NAME }} <i class="mdi mdi-chevron-down"></i> 
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                    <!-- item-->
                    <div class="dropdown-header noti-title">
                        <h6 class="text-overflow m-0">{{ trans('auth.welcome') }} {{ FULL_NAME }} !</h6>
                    </div>

                    <!-- item-->
                    <a href="{{ URL::to('/profile') }}" class="dropdown-item notify-item">
                        <i class="fe-user"></i>
                        <span>{{ trans('main.myAccount') }}</span>
                    </a>

                    <div class="dropdown-divider"></div>

                    <!-- item-->
                    <a href="{{ URL::to('/logout') }}" class="dropdown-item notify-item">
                        <i class="fe-log-out"></i>
                        <span>{{ trans('main.logout') }}</span>
                    </a>

                </div>
            </li>

            {{-- <li class="dropdown notification-list">
                <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">
                    <i class="fe-settings noti-icon"></i>
                </a>
            </li> --}}
        </ul>
        <!-- LOGO -->
        <div class="logo-box">
            <a href="{{ URL::to('/dashboard') }}" class="logo logo-dark text-center">
                <span class="logo-sm">
                    <img src="{{ asset('images/favicon.ico') }}" alt="" height="22">
                    <!-- <span class="logo-lg-text-light">UBold</span> -->
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('images/logo.png') }}" alt="" height="35">
                    <!-- <span class="logo-lg-text-light">U</span> -->
                </span>
            </a>

            <a href="{{ URL::to('/dashboard') }}" class="logo logo-light text-center">
                <span class="logo-sm">
                    <img src="{{ asset('images/favicon.ico') }}" alt="" height="22">
                </span>
                <span class="logo-lg">
                    <img src="{{ asset('images/logo.png') }}" alt="" height="35">
                </span>
            </a>
        </div>

        <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
            <li>
                <button class="button-menu-mobile waves-effect waves-light" style="margin-top: 4px">
                    <i class="fe-menu"></i>
                </button>
            </li>
            <li>
                <!-- Mobile menu toggle (Horizontal Layout)-->
                <a class="navbar-toggle nav-link" data-toggle="collapse" data-target="#topnav-menu-content">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
                <!-- End mobile menu toggle-->
            </li>   
            <li class="dropdown d-lg-inline-block topbar-dropdown">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="align-middle">{{ Session::has('channel') ? \App\Models\UserChannels::getOne(Session::get('channel'))->name : trans('main.chooseChannel') }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- item-->
                    @foreach($userObj->channels as $channel)
                    <a href="javascript:void(0);" class="dropdown-item channel-item">
                        <span class="align-middle" data-area="{{ $channel->id }}">{{ $channel->name }}</span>
                    </a>
                    @endforeach
                </div>
            </li>
            
        </ul>
        <div class="clearfix"></div>
    </div>
</div>
<!-- end Topbar -->