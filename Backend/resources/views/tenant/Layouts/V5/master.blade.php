<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
	<head>
		<meta charset="UTF-8" />
	    <!-- IE Compatibility Meta -->
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <!-- First Mobile Meta  -->
		<meta name="viewport" content="width=device-width, height=device-height ,  maximum-scale=1 , initial-scale=1">
		<title>واتس لووب | Whats Loop | @yield('title')</title>
		<meta name="description" content="#" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		@yield('extra-metas')
		@include('tenant.Layouts.V5.head')
		@livewireStyles
	</head>
	<!--end::Head-->
	
	<body>
		<!-- Begin page -->
		<input type="hidden" name="countriesCode" value="{{ Helper::getCountryCode() ? Helper::getCountryCode()->countryCode : 'sa' }}">
		@include('tenant.Layouts.V5.header')

		@if(!in_array(Request::segment(1),['menu','packages','checkout','postBundle','updateSubscription']) && (Request::segment(1) != 'invoices' && Request::segment(4) != 'checkout')  && (Request::segment(1) != 'profile' && Request::segment(3) != 'transferPayment'))
		@include('tenant.Layouts.V5.sidebar')
		@endif

		@include('tenant.Layouts.V5.breadcrumb')
		{{-- @include('tenant.Layouts.V5.userStatus') --}}
		@if(!in_array(Request::segment(1),['QR','checkout','packages','updateSubscription','postBundle','sync']) && Request::segment(3) != 'transferPayment' && !Session::has('hasJob')  && (Request::segment(1) != 'invoices' && Request::segment(4) != 'checkout')  && (Request::segment(1) != 'profile' && Request::segment(3) != 'transferPayment'))
		@livewire('check-reconnection',[
			'requestSemgent' => Request::segment(1),
			'addons' => Session::get('addons'),
			'tenant_id' => TENANT_ID
			])
		@endif
		
		@yield('content')

		
		@yield('modals')

		@include('tenant.Layouts.V5.footer')
		@include('tenant.Layouts.V5.scripts')
        @include('tenant.Partials.notf_messages')
	</body>
	<!--end::Body-->
</html>