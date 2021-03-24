{{-- Extends layout --}}
<!DOCTYPE html>
<html lang="{{ LANGUAGE_PREF }}" dir="{{ DIRECTION }}">
	<head>
		<meta charset="utf-8" />
		<title>واتس لووب | Whats Loop | {{ trans('main.livechat') }}</title>
		<meta name="description" content="#" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
    	<meta name="userID" content="{{ USER_ID }}">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    	<link rel="stylesheet" href="{{ mix('css/app.css') }}">
	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body>
		<!-- Begin page -->
        <div id="wrapper">
			<div class="content-page">
                <div class="content">
                	<div id="app">
					    <chat-application></chat-application>
					</div>
				</div>
			</div>
		</div>
    	<script src="{{mix('js/app.js')}}"></script>
	</body>
	<!--end::Body-->
</html>