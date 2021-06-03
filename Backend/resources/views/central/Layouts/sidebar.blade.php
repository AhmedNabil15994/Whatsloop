<!-- ========== Left Sidebar Start ========== -->
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!-- User box -->
        <div class="user-box text-center">
            <img src="{{ \App\Models\CentralUser::getData(\App\Models\CentralUser::getOne(USER_ID))->photo }}" alt="user-img" title="Mat Helme"
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
                <li class="{{ Active(URL::to('/dashboard*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> {{ trans('main.dashboard') }} </span>
                    </a>
                </li>

                @if(\Helper::checkRules('list-memberships,list-features'))
                <li class="{{ Active(URL::to('/memberships*'),'menuitem-active') }} {{ Active(URL::to('/features*'),'menuitem-active') }}">
                    <a href="#sidebarMembs" data-toggle="collapse">
                        <i class="menu-icon far fa-id-card"></i>
                        <span> {{ trans('main.packages') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/memberships*'),'show') }} {{ Active(URL::to('/features*'),'show') }}" id="sidebarMembs">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-memberships'))
                            <li class="{{ Active(URL::to('/memberships*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/memberships') }}">{{ trans('main.packages') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('list-features'))
                            <li class="{{ Active(URL::to('/features*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/features') }}">{{ trans('main.features') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('list-addons,list-extraQuotas'))
                <li class="{{ Active(URL::to('/addons*'),'menuitem-active') }} {{ Active(URL::to('/extraQuotas*'),'menuitem-active') }}">
                    <a href="#sidebarAddons" data-toggle="collapse">
                        <i class="menu-icon  fas fa-star"></i>
                        <span> {{ trans('main.addons') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/addons*'),'show') }} {{ Active(URL::to('/extraQuotas*'),'show') }}" id="sidebarAddons">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-addons'))
                            <li class="{{ Active(URL::to('/addons*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/addons') }}">{{ trans('main.addons') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('list-extraQuotas'))
                            <li class="{{ Active(URL::to('/extraQuotas*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/extraQuotas') }}">{{ trans('main.extraQuotas') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('list-clients'))
                <li class="{{ Active(URL::to('/clients*'),'menuitem-active') }}">
                    <a href="#sidebarClients" data-toggle="collapse">
                        <i class="fa fa-users"></i>
                        <span> {{ trans('main.clients') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/clients*'),'show') }}" id="sidebarClients">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-clients'))
                            <li class="{{ Active(URL::to('/clients*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/clients') }}">{{ trans('main.clients') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('list-invoices'))
                <li class="{{ Active(URL::to('/invoices*'),'menuitem-active') }}">
                    <a href="#sidebarInvoices" data-toggle="collapse">
                        <i class="fas fa-file-invoice"></i>
                        <span> {{ trans('main.invoices') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/invoices*'),'show') }}" id="sidebarInvoices">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-invoices'))
                            <li class="{{ Active(URL::to('/invoices*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/invoices') }}">{{ trans('main.invoices') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('list-tickets,list-departments'))
                <li class="{{ Active(URL::to('/tickets*'),'menuitem-active') }} {{ Active(URL::to('/departments*'),'menuitem-active') }}">
                    <a href="#sidebarSupport" data-toggle="collapse">
                        <i class="ti-support"></i>
                        <span> {{ trans('main.support') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/tickets*'),'show') }} {{ Active(URL::to('/departments*'),'show') }}" id="sidebarSupport">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-tickets'))
                            <li class="{{ Active(URL::to('/tickets*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/tickets') }}">{{ trans('main.tickets') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('list-departments'))
                            <li class="{{ Active(URL::to('/departments*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/departments') }}">{{ trans('main.departments') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
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

                @if(\Helper::checkRules('list-faqs'))
                <li class="{{ Active(URL::to('/faqs*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/faqs') }}">
                        <i class=" fas fa-question"></i>
                        <span> {{ trans('main.faqs') }} </span>
                    </a>
                </li>
                @endif
                
                @if(\Helper::checkRules('list-changeLogs'))
                <li class="{{ Active(URL::to('/changeLogs*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/changeLogs') }}">
                        <i class=" dripicons-blog"></i>
                        <span> {{ trans('main.changeLogs') }} </span>
                    </a>
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