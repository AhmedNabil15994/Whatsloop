<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
	<head>
		<meta charset="utf-8" />
		<title>واتس لووب | Whats Loop | @yield('title')</title>
		<meta name="description" content="#" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		@yield('extra-metas')
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		@include('tenant.Layouts.head')
	</head>
	<!--end::Head-->
	
	<body class="main-body tena light-theme app sidebar-mini active leftmenu-color">
		<!-- Begin page -->
		<input type="hidden" name="countriesCode" value="{{ Helper::getCountryCode() ? Helper::getCountryCode()->countryCode : 'sa' }}">
		@include('tenant.Layouts.sidebar')
		
		<!-- main-content -->
		<div class="main-content app-content">
			@include('tenant.Layouts.header')
			<!-- container -->
			<div class="container-fluid mg-t-20">
				@include('tenant.Layouts.breadcrumb')
				@yield('content')
			</div>
		</div>

		@include('tenant.Layouts.rightSideBar')
		
		@yield('modals')

		@include('tenant.Layouts.footer')
		@include('tenant.Layouts.scripts')
        @include('tenant.Partials.notf_messages')
	</body>
	<!--end::Body-->
</html>