<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
	<head>
		<meta charset="utf-8" />
		<title>واتس لووب | Whats Loop | @yield('title')</title>
		<meta name="description" content="#" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		@include('tenant.Layouts.head')
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	@php $themeObj = App\Models\UserTheme::getUserTheme(USER_ID); @endphp
	{{-- {{ dd($themeObj) }} --}}
	<body class="loading" data-layout='{ "mode": "{{ $themeObj!=null ? $themeObj->theme : 'light' }}","width": "{{ $themeObj!=null ? $themeObj->width : 'fluid' }}","topbar": {"color": "{{ $themeObj!=null ? $themeObj->top_bar : 'dark' }}"},"menuPosition": "{{ $themeObj!=null ? $themeObj->menus_position : 'fixed' }}", "sidebar": {"size": "{{ $themeObj!=null ? $themeObj->sidebar_size : 'light' }}","showuser" : "{{ $themeObj!=null ? $themeObj->user_info : 'false' }}"}}'>
		<!-- Begin page -->
        <div id="wrapper">
			@include('tenant.Layouts.header')
			@include('tenant.Layouts.sidebar')
			
			<div class="content-page">
                <div class="content">
					@yield('content')
				</div>
				@include('tenant.Layouts.footer')
			</div>

			@include('tenant.Layouts.rightSideBar')
			
			@yield('modals')

			@include('tenant.Layouts.scripts')
	        @include('tenant.Partials.notf_messages')
		</div>
	</body>
	<!--end::Body-->
</html>