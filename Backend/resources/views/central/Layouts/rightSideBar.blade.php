<!-- Right Sidebar -->
<div class="right-bar">
    @php $themeObj = []; @endphp
    <div data-simplebar class="h-100">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-bordered nav-justified" role="tablist">
            <li class="nav-item">
                <a class="nav-link py-2" data-toggle="tab" href="#chat-tab" role="tab">
                    <i class="mdi mdi-message-text d-block font-22 my-1"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2" data-toggle="tab" href="#tasks-tab" role="tab">
                    <i class="mdi mdi-format-list-checkbox d-block font-22 my-1"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link py-2 active" data-toggle="tab" href="#settings-tab" role="tab">
                    <i class="mdi mdi-cog-outline d-block font-22 my-1"></i>
                </a>
            </li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content pt-0">
            <div class="tab-pane active" id="settings-tab" role="tabpanel">
                <h6 class="font-weight-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
                    <span class="d-block py-1">{{ trans('main.themeSettings') }}</span>
                </h6>
                <div class="p-3 theme">
                    <div class="alert alert-warning" role="alert">
                        <strong>{{ trans('main.customize') }} </strong> {{ trans('main.customizeP') }}
                    </div>
                    <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">{{ trans('main.colorScheme') }}</h6>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" name="theme" value="light"
                            id="light-mode-check" {{ $themeObj == null ? 'checked' : ($themeObj->theme == "light" ? 'checked' : '') }}/>
                        <label class="custom-control-label" for="light-mode-check">{{ trans('main.lightMode') }}</label>
                    </div>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" name="theme" value="dark"
                            id="dark-mode-check" {{ $themeObj != null && $themeObj->theme == "dark" ? 'checked' : '' }} />
                        <label class="custom-control-label" for="dark-mode-check">{{ trans('main.darkMode') }}</label>
                    </div>
                    <!-- Width -->
                    <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">{{ trans('main.width') }}</h6>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj == null ? 'checked' : ($themeObj->width == "fluid" ? 'checked' : '') }} name="width" value="fluid" id="fluid-check" checked />
                        <label class="custom-control-label" for="fluid-check">{{ trans('main.fluid') }}</label>
                    </div>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj != null && $themeObj->width == "boxed" ? 'checked' : '' }} name="width" value="boxed" id="boxed-check" />
                        <label class="custom-control-label" for="boxed-check">{{ trans('main.boxed') }}</label>
                    </div>
                    <!-- Menu positions -->
                    <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">{{ trans('main.menus') }}</h6>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj == null ? 'checked' : ($themeObj->menus_position == "fixed" ? 'checked' : '') }} name="menus_position" value="fixed" id="fixed-check"
                            checked />
                        <label class="custom-control-label" for="fixed-check">{{ trans('main.fixed') }}</label>
                    </div>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj != null && $themeObj->menus_position == "scrollable" ? 'checked' : '' }} name="menus_position" value="scrollable"
                            id="scrollable-check" />
                        <label class="custom-control-label" for="scrollable-check">{{ trans('main.scrollable') }}</label>
                    </div>
                    <!-- size -->
                    <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">{{ trans('main.leftSize') }}</h6>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj == null ? 'checked' : ($themeObj->sidebar_size == "default" ? 'checked' : '') }} name="sidebar_size" value="default"
                            id="default-size-check" checked />
                        <label class="custom-control-label" for="default-size-check">{{ trans('main.default') }}</label>
                    </div>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj != null && $themeObj->sidebar_size == "condensed" ? 'checked' : '' }} name="sidebar_size" value="condensed"
                            id="condensed-check" />
                        <label class="custom-control-label" for="condensed-check">{{ trans('main.condensed') }} <small>{{ trans('main.extraSmall') }}</small></label>
                    </div>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj != null && $themeObj->sidebar_size == "compact" ? 'checked' : '' }} name="sidebar_size" value="compact"
                            id="compact-check" />
                        <label class="custom-control-label" for="compact-check">{{ trans('main.compact') }} <small>{{ trans('main.smallSize') }}</small></label>
                    </div>
                    <!-- User info -->
                    <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">{{ trans('main.sidebarInfo') }}</h6>
                    <div class="custom-control custom-switch mb-1">
                        <input type="checkbox" class="custom-control-input" {{ $themeObj != null && $themeObj->user_info == "true" ? 'checked' : '' }} name="user_info" value="{{ $themeObj != null && $themeObj->user_info == 'true' ? 'false' : 'true' }}" id="sidebaruser-check" />
                        <label class="custom-control-label" for="sidebaruser-check">{{ trans('main.enable') }}</label>
                    </div>
                    <!-- Topbar -->
                    <h6 class="font-weight-medium font-14 mt-4 mb-2 pb-1">{{ trans('main.topbar') }}</h6>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj == null ? 'checked' : ($themeObj->top_bar == "dark" ? 'checked' : '') }} name="top_bar" value="dark" id="darktopbar-check"
                            checked />
                        <label class="custom-control-label" for="darktopbar-check">{{ trans('main.dark') }}</label>
                    </div>
                    <div class="custom-control custom-switch mb-1">
                        <input type="radio" class="custom-control-input" {{ $themeObj != null && $themeObj->top_bar == "light" ? 'checked' : '' }} name="top_bar" value="light" id="lighttopbar-check" />
                        <label class="custom-control-label" for="lighttopbar-check">{{ trans('main.light') }}</label>
                    </div>
                    <button class="btn btn-primary btn-block mt-4" id="resetBtn">{{ trans('main.resetDef') }}</button>
                </div>
            </div>
        </div>
    </div> <!-- end slimscroll-menu-->
</div>
<!-- /Right-bar -->
<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>