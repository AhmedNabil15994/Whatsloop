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
	@php 
		$mode = \App\Models\UserTheme::where('user_id',USER_ID)->first();
	@endphp
	<body class="bodyCpanel overflowH {{ $mode && $mode->theme == 1 ? 'dark-mode' : '' }}">
		<!-- Begin page -->
		@if(Request::segment(1) != 'profile')
		<input type="hidden" name="countriesCode" value="{{ Helper::getCountryCode() ? Helper::getCountryCode()->countryCode : 'sa' }}">
		@endif
		@include('tenant.Layouts.V5.sidebar')

		<div class="cpanelStyle activeMenu">
			@include('tenant.Layouts.V5.header')

			@if(!in_array(Request::segment(1),['dashboard','menu','sync','services','tickets']) && (Request::segment(1) != 'profile' && Request::segment(2) != 'apiGuide')  && (Request::segment(1) == 'invoices' && Request::segment(2) != 'view'))
			@include('tenant.Layouts.V5.breadcrumb')
			@endif
			{{-- @include('tenant.Layouts.V5.userStatus') --}}
			<div class="containerCpanel formNumbers">
				@if(!in_array(Request::segment(1),['QR','msgsArchive','checkout','addGroupNumbers','packages','updateSubscription','postBundle','sync']) && Request::segment(3) != 'transferPayment' && !Session::has('hasJob') && (Request::segment(1) != 'profile' && Request::segment(2) != 'apiGuide') && IS_ADMIN)
				@livewire('check-reconnection',[
					'requestSemgent' => Request::segment(1),
					'addons' => Session::get('addons'),
					'tenant_id' => TENANT_ID
					])
				@endif

				@if(in_array(10,Session::get('addons')))
				<div class="Additions">
			        <h2 class="title">{{ trans('main.botPlusNotify') }}</h2>
			        <a href="#" class="btnAdd" style="visibility: hidden;"></a>
			    </div> 
				@endif

				@yield('content')
			</div>

		</div>
		
		@yield('modals')

		@include('tenant.Layouts.V5.footer')
		@include('tenant.Layouts.V5.scripts')
        @include('tenant.Partials.notf_messages')
	</body>
	<!--end::Body-->
</html>