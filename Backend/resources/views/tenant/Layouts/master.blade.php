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
		@livewireStyles
	</head>
	<!--end::Head-->
	
	<body class="main-body tena light-theme app sidebar-mini active leftmenu-color">
		<!-- Begin page -->
		<input type="hidden" name="countriesCode" value="{{ Helper::getCountryCode() ? Helper::getCountryCode()->countryCode : 'sa' }}">
		@if(!in_array(Request::segment(1),['menu','packages','checkout','postBundle']))
		@include('tenant.Layouts.sidebar')
		@endif
		<!-- main-content -->
		<div class="main-content app-content" {{ in_array(Request::segment(1),['menu','packages','checkout','postBundle']) ? ' style=margin:0 ': '' }}>
			@include('tenant.Layouts.header')
			<!-- container -->
			<div class="container-fluid mg-t-35 ">
				@include('tenant.Layouts.breadcrumb')
				{{-- @include('tenant.Layouts.userStatus') --}}
				@if(!in_array(Request::segment(1),['QR','checkout','packages','updateSubscription','postBundle','sync']) && Request::segment(3) != 'transferPayment' && !Session::has('hasJob'))
				@livewire('check-reconnection',[
					'requestSemgent' => Request::segment(1),
					'addons' => Session::get('addons'),
					'tenant_id' => TENANT_ID
					])
				@endif
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