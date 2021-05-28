<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
	<head>
		<meta charset="utf-8" />
		<title>واتس لووب | Whats Loop | @yield('title')</title>
		<meta name="description" content="#" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		@yield('extra-metas')
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		@include('central.Layouts.head')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	@php $themeObj = []; @endphp
	{{-- {{ dd($themeObj) }} --}}
	<body class="loading" data-layout='{ "mode": "{{ $themeObj!=null ? $themeObj->theme : 'light' }}","width": "{{ $themeObj!=null ? $themeObj->width : 'fluid' }}","topbar": {"color": "{{ $themeObj!=null ? $themeObj->top_bar : 'dark' }}"},"menuPosition": "{{ $themeObj!=null ? $themeObj->menus_position : 'fixed' }}", "sidebar": {"size": "{{ $themeObj!=null ? $themeObj->sidebar_size : 'light' }}","showuser" : "{{ $themeObj!=null ? $themeObj->user_info : 'false' }}"}}'>
		<!-- Begin page -->

		<input type="hidden" name="countriesCode" value="{{ Helper::getCountryCode() ? Helper::getCountryCode()->countryCode : 'sa' }}">
        <div id="wrapper">
			@include('central.Layouts.header')
			@include('central.Layouts.sidebar')
			
			<div class="content-page">
                <div class="content">
					@yield('content')
				</div>
				@include('central.Layouts.footer')
			</div>

			@include('central.Layouts.rightSideBar')
			
			@yield('modals')

			@include('central.Layouts.scripts')
	        @include('central.Partials.notf_messages')
		</div>
	</body>
	<!--end::Body-->
</html>