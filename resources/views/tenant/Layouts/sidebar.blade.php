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

                @if(\Helper::checkRules('list-users,list-groups'))
                <li>
                    <a href="#sidebarEcommerce" data-toggle="collapse">
                        <i class="fa fa-user-tie"></i>
                        <span> {{ trans('main.users') }} </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarEcommerce">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ URL::to('/users') }}">{{ trans('main.users') }}</a>
                            </li>
                            <li>
                                <a href="{{ URL::to('/groups') }}">{{ trans('main.groups') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                <li>
                    <a href="#sidebarEcommerce" data-toggle="collapse">
                        <i class="mdi mdi-cart-outline"></i>
                        <span> Ecommerce </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="sidebarEcommerce">
                        <ul class="nav-second-level">
                            <li>
                                <a href="ecommerce-dashboard.html">Dashboard</a>
                            </li>
                            <li>
                                <a href="ecommerce-products.html">Products</a>
                            </li>
                            <li>
                                <a href="ecommerce-product-detail.html">Product Detail</a>
                            </li>
                            <li>
                                <a href="ecommerce-product-edit.html">Add Product</a>
                            </li>
                            <li>
                                <a href="ecommerce-customers.html">Customers</a>
                            </li>
                            <li>
                                <a href="ecommerce-orders.html">Orders</a>
                            </li>
                            <li>
                                <a href="ecommerce-order-detail.html">Order Detail</a>
                            </li>
                            <li>
                                <a href="ecommerce-sellers.html">Sellers</a>
                            </li>
                            <li>
                                <a href="ecommerce-cart.html">Shopping Cart</a>
                            </li>
                            <li>
                                <a href="ecommerce-checkout.html">Checkout</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
<!-- Left Sidebar End -->