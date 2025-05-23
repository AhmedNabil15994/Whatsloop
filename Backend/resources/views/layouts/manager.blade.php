
<!DOCTYPE html>
<!--
Template Name: Metronic - Bootstrap 4 HTML, React, Angular 9 & VueJS Admin Dashboard Theme
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: https://1.envato.market/EA4JP
Renew Support: https://1.envato.market/EA4JP
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<html lang="{{app()->getLocale() == 'en' ? 'en' : 'ar'}}" dir="{{app()->getLocale() == 'en' ? 'ltr' : 'rtl'}}">
	<!--begin::Head-->
	<head><base href="../../">
		<meta charset="utf-8" />
		<title>@yield('title')</title>
		<meta name="description" content="Page with empty content" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<!--begin::Fonts-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<!--end::Fonts-->


		@if(app()->getLocale() == 'en')
			<!--begin::Global Theme Styles(used by all pages)-->
			<link href="{{global_asset('met/assets/plugins/global/plugins.bundle.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/plugins/custom/prismjs/prismjs.bundle.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/style.bundle.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<!--end::Global Theme Styles-->

			<!--begin::Layout Themes(used by all pages)-->
			<link href="{{global_asset('met/assets/css/themes/layout/header/base/light.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/themes/layout/header/menu/light.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/themes/layout/brand/dark.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/themes/layout/aside/dark.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<!--end::Layout Themes-->

		@else
			<!--begin::Global Theme Styles(used by all pages)-->
			<link href="{{global_asset('met/assets/plugins/global/plugins.bundle.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/plugins/custom/prismjs/prismjs.bundle.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/style.bundle.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<!--end::Global Theme Styles-->

			<!--begin::Layout Themes(used by all pages)-->
			<link href="{{global_asset('met/assets/css/themes/layout/header/base/light.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/themes/layout/header/menu/light.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/themes/layout/brand/dark.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<link href="{{global_asset('met/assets/css/themes/layout/aside/dark.rtl.css?v=7.0.4')}}" rel="stylesheet" type="text/css" />
			<!--end::Layout Themes-->
		@endif
        
		<link rel="shortcut icon" href="{{global_asset('met/assets/media/logos/favicon.ico')}}" />

		@stack('after-styles')

	</head>
	<!--end::Head-->

    <!--begin::Body-->
    <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
        <!--begin::Header Mobile-->
		<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
			<!--begin::Logo-->
			<a href="index.html">
				<img alt="Logo" src="{{global_asset('images/logo.png')}}" />
			</a>
			<!--end::Logo-->
			<!--begin::Toolbar-->
			<div class="d-flex align-items-center">
				<!--begin::Aside Mobile Toggle-->
				<button class="btn p-0 burger-icon burger-icon-left" id="kt_aside_mobile_toggle">
					<span></span>
				</button>
				<!--end::Aside Mobile Toggle-->
				<!--begin::Header Menu Mobile Toggle-->
				<button class="btn p-0 burger-icon ml-4" id="kt_header_mobile_toggle">
					<span></span>
				</button>
				<!--end::Header Menu Mobile Toggle-->
				<!--begin::Topbar Mobile Toggle-->
				<button class="btn btn-hover-text-primary p-0 ml-2" id="kt_header_mobile_topbar_toggle">
					<span class="svg-icon svg-icon-xl">
						<!--begin::Svg Icon | path:assets/media/svg/icons/General/User.svg-->
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon points="0 0 24 0 24 24 0 24" />
								<path d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
								<path d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
							</g>
						</svg>
						<!--end::Svg Icon-->
					</span>
				</button>
				<!--end::Topbar Mobile Toggle-->
			</div>
			<!--end::Toolbar-->
		</div>
		<!--end::Header Mobile-->

        <div class="d-flex flex-column flex-root">
                <div class="d-flex flex-row flex-column-fluid page">
                        <div class="aside aside-left aside-fixed d-flex flex-column flex-row-auto" id="kt_aside">
                            @include('includes.sidebar')
                        </div>
                        <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">

                            @include('includes.header')

                            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                                <div class="d-flex flex-column-fluid">
                                    <div class="container">
										@include('includes.partials.messages')

                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                            <div class="footer bg-white py-4 d-flex flex-lg-column" id="kt_footer">
                                @include('includes.footer')
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        @stack('before-scripts')

        <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>


        <script src="{{global_asset('met/assets/plugins/global/plugins.bundle.js?v=7.0.4')}}"></script>
        <script src="{{global_asset('met/assets/plugins/custom/prismjs/prismjs.bundle.js?v=7.0.4')}}"></script>
        <script src="{{global_asset('met/assets/js/scripts.bundle.js?v=7.0.4')}}"></script>
        <script src="{{global_asset('met/assets/plugins/custom/fullcalendar/fullcalendar.bundle.js?v=7.0.4')}}"></script>
        <script src="{{global_asset('met/assets/plugins/custom/gmaps/gmaps.js?v=7.0.4')}}"></script>
        <script src="{{global_asset('met/assets/js/pages/widgets.js?v=7.0.4')}}"></script>
        <script src="{{global_asset('met/assets/plugins/custom/datatables/datatables.bundle.js?v=7.0.4')}}"></script>

        <script src="{{global_asset('js/main.js')}}" type="text/javascript"></script>

        <script>
            window._token = '{{ csrf_token() }}';
        </script>
        @stack('after-scripts')

    </body>


</html>