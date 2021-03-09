<div id="kt_header" class="header header-fixed">
    <div class="container-fluid d-flex align-items-stretch justify-content-between">
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <div class="header-menu header-menu-mobile header-menu-layout-default" id="kt_header_menu">
            
                <ul class="menu-nav">
                    <li class="menu-item" aria-haspopup="true">
                        <a href="#" class="menu-link">
                            <i class="menu-icon flaticon-home"></i>
                        </a>
                    </li>
                    
                </ul>

            </div>
        </div>
        <!--begin::Topbar-->
        <div class="topbar">

            <div class="dropdown">
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px" aria-expanded="true">
                    <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                        <img class="h-20px w-20px rounded-sm" src="{{asset('images/logo.png')}}" alt="">
                    </div>
                    <span class="d-md-down-none">ahmed</span>
                </div>

                <div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right" style="">
                    <ul class="navi navi-hover py-4">
                        <li class="navi-item">
                            
                        </li>
                        @if(Auth::guard('web')->check())
                            <li class="navi-item">
                                <a class="navi-link" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endif
                        @if(Auth::guard('managers')->check())
                            <li class="navi-item">
                                <a class="navi-link" href="{{ route('manager.logout') }}"
                                        onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('manager.logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        @endif

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>