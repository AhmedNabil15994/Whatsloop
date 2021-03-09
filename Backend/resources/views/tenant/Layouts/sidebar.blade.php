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
                <li>
                    <a href="{{ URL::to('/dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline"></i>
                        <span> {{ trans('main.dashboard') }} </span>
                    </a>
                </li>
                @if(\Helper::checkRules('salla-customers,salla-products,salla-orders,salla-reports,salla-templates'))
                <li class="{{ Active(URL::to('/services/salla*'),'menuitem-active') }}">
                    <a href="#sidebarSalla" data-toggle="collapse">
                        <i class=" fas fa-layer-group"></i>
                        <span> {{ trans('main.salla') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/services/salla*'),'show') }}" id="sidebarSalla">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('salla-customers'))
                            <li class="{{ Active(URL::to('/services/salla/customers'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/salla/customers') }}">{{ trans('main.customers') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('salla-products'))
                            <li class="{{ Active(URL::to('/services/salla/products'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/salla/products') }}">{{ trans('main.products') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('salla-orders'))
                            <li class="{{ Active(URL::to('/services/salla/orders'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/salla/orders') }}">{{ trans('main.orders') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('salla-reports'))
                            <li class="{{ Active(URL::to('/services/salla/reports'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/salla/reports') }}">{{ trans('main.notReports') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('salla-templates'))
                            <li class="{{ Active(URL::to('/services/salla/templates*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/salla/templates') }}">{{ trans('main.templates') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('zid-customers,zid-products,zid-orders,zid-reports,zid-templates'))
                <li class="{{ Active(URL::to('/services/zid*'),'menuitem-active') }}">
                    <a href="#sidebarZid" data-toggle="collapse">
                        <i class=" fas fa-layer-group"></i>
                        <span> {{ trans('main.zid') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/services/zid*'),'show') }}" id="sidebarZid">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('zid-customers'))
                            <li class="{{ Active(URL::to('/services/zid/customers'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/zid/customers') }}">{{ trans('main.customers') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('zid-products'))
                            <li class="{{ Active(URL::to('/services/zid/products'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/zid/products') }}">{{ trans('main.products') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('zid-orders'))
                            <li class="{{ Active(URL::to('/services/zid/orders'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/zid/orders') }}">{{ trans('main.orders') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('zid-reports'))
                            <li class="{{ Active(URL::to('/services/zid/reports'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/zid/reports') }}">{{ trans('main.notReports') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('zid-templates'))
                            <li class="{{ Active(URL::to('/services/zid/templates*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/services/zid/templates') }}">{{ trans('main.templates') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
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
                @if(\Helper::checkRules('list-group-numbers,add-number-to-group,list-contacts'))
                <li class="{{ Active(URL::to('/groupNumbers*'),'menuitem-active') }} {{ Active(URL::to('/contacts*'),'menuitem-active') }} {{ Active(URL::to('/addGroupNumbers*'),'menuitem-active') }}">
                    <a href="#sidebarGroupNumbers" data-toggle="collapse">
                        <i class="fas fa-users"></i>
                        <span> {{ trans('main.contacts') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/groupNumbers*'),'show') }} {{ Active(URL::to('/contacts*'),'show') }} {{ Active(URL::to('/addGroupNumbers*'),'show') }}" id="sidebarGroupNumbers">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-contacts'))
                            <li class="{{ Active(URL::to('/contacts*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/contacts') }}">{{ trans('main.contacts') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('list-group-numbers'))
                            <li class="{{ Active(URL::to('/groupNumbers*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/groupNumbers') }}">{{ trans('main.groupNumbers') }}</a>
                            </li>
                            @endif
                            @if(\Helper::checkRules('add-number-to-group'))
                            <li class="{{ Active(URL::to('/addGroupNumbers*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/addGroupNumbers') }}">{{ trans('main.addGroupNumbers') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('list-group-messages'))
                <li class="{{ Active(URL::to('/groupMsgs*'),'menuitem-active') }}">
                    <a href="#sidebarGPMSGS" data-toggle="collapse">
                        <i class="mdi mdi-send"></i>
                        <span> {{ trans('main.groupMsgs') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse {{ Active(URL::to('/groupMsgs*'),'show') }}" id="sidebarGPMSGS">
                        <ul class="nav-second-level">
                            @if(\Helper::checkRules('list-group-messages'))
                            <li class="{{ Active(URL::to('/groupMsgs*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/groupMsgs') }}">{{ trans('main.groupMsgsArc') }}</a>
                            </li>
                            @endif

                            @if(\Helper::checkRules('list-messages-archive'))
                            <li class="{{ Active(URL::to('/groupMsgs/add*'),'menuitem-active') }}">
                                <a href="{{ URL::to('/groupMsgs/add') }}">{{ trans('main.sendNewMessage') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>
                @endif

                @if(\Helper::checkRules('list-statuses'))
                <li class="{{ Active(URL::to('/statuses*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/statuses') }}">
                        <i class="mdi mdi-format-list-bulleted-type"></i>
                        <span> {{ trans('main.statuses') }} </span>
                    </a>
                </li>
                @endif

                @if(\Helper::checkRules('list-groupNumberRepors'))
                <li class="{{ Active(URL::to('/groupNumberRepors*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/groupNumberRepors') }}">
                        <i class="mdi mdi-file-account-outline"></i>
                        <span> {{ trans('main.groupNumberRepors') }} </span>
                    </a>
                </li>
                @endif

                @if(\Helper::checkRules('list-messages-archive'))
                <li class="{{ Active(URL::to('/msgsArchive*'),'menuitem-active') }}">
                    <a href="{{ URL::to('/msgsArchive') }}">
                        <i class="mdi mdi-archive-outline"></i>
                        <span> {{ trans('main.msgsArchive') }} </span>
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