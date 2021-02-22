<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{ \App\Models\User::getData(\App\Models\User::getOne(USER_ID))->photo }}" alt="user-img" title="Mat Helme"
                class="rounded-circle avatar-md">
            <div class="dropdown">
                <a href="javascript: void(0);" class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                    data-toggle="dropdown">{{ FULL_NAME }}</a>
                <div class="dropdown-menu user-pro-dropdown">

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-user mr-1"></i>
                        <span>{{ trans('main.myAccount') }}</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-settings mr-1"></i>
                        <span>{{ trans('main.settings') }}</span>
                    </a>

                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <i class="fe-log-out mr-1"></i>
                        <span>{{ trans('main.logout') }}</span>
                    </a>

                </div>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <ul id="side-menu">    
                @if(\Helper::checkRules('list-dashboard'))
                <li>
                    <a href="{{ URL::to('/dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> {{ trans('main.dashboard') }} </span>
                    </a>
                </li>
                @endif
                @if(\Helper::checkRules('list-bots'))
                <li class="{{ Active(URL::to('/bots*'),'menuitem-active') }}">
                    <a href="#sidebarBots" data-toggle="collapse">
                        <i class="fas fa-robot"></i>
                        <span> {{ trans('main.chatBot') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    @if(\Helper::checkRules('list-bots'))
                    <div class="collapse {{ Active(URL::to('/bots*'),'show') }}" id="sidebarBots">
                        <ul class="nav-second-level">
                            <li class="{{ Active(URL::to('/bots*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/bots') }}">{{ trans('main.bot') }}</a>
                            </li>
                        </ul>
                    </div>
                    @endif
                </li>
                @endif
                @if(\Helper::checkRules('list-templates'))
                <li class="{{ Active(URL::to('/templates*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/templates') }}">
                        <i class="fas fa-envelope-open-text"></i>
                        <span> {{ trans('main.templates') }} </span>
                    </a>
                </li>
                @endif
                @if(\Helper::checkRules('list-replies'))
                <li class="{{ Active(URL::to('/replies*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/replies') }}">
                        <i class="far fa-comment-alt"></i>
                        <span> {{ trans('main.replies') }} </span>
                    </a>
                </li>
                @endif
                @if(\Helper::checkRules('list-categories'))
                <li class="{{ Active(URL::to('/categories*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/categories') }}">
                        <i class="fas fa-tags"></i>
                        <span> {{ trans('main.categories') }} </span>
                    </a>
                </li>
                @endif
                @if(\Helper::checkRules('list-users,list-groups'))
                <li class="{{ Active(URL::to('/users*'),'menuitem-active') }} {{ Active(URL::to('/groups*'),'menuitem-active') }}">
                    <a href="#sidebarUsers" data-toggle="collapse">
                        <i class="fa fa-user-tie"></i>
                        <span> {{ trans('main.users') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/users*'),'show') }} {{ Active(URL::to('/groups*'),'show') }}" id="sidebarUsers">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-users'))
                            <li class="{{ Active(URL::to('/users*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/users') }}">{{ trans('main.users') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('list-groups'))
                            <li class="{{ Active(URL::to('/groups*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/groups') }}">{{ trans('main.groups') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->