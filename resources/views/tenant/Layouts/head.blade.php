<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
<link href="https://fonts.googleapis.com/css?family=Tajawal:300,400,500,600,700" rel="stylesheet">
<link href="{{ asset('libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/summernote/summernote-bs4.min.css') }}" rel="stylesheet" type="text/css" />
<!-- App css -->
<link href="{{ asset('css/bootstrap-material.min.css') }}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
<link href="{{ asset('css/bootstrap-material-dark.min.css') }}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" />
@if(DIRECTION == 'ltr')
<link href="{{ asset('css/app-material.min.css') }}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
<!-- Dark css -->
<link href="{{ asset('css/app-material-dark.min.css') }}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" />
@else
<!-- RTL css -->
<link href="{{ asset('css/app-material-rtl.min.css') }}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
<!-- RTL css -->
<link href="{{ asset('css/app-material-dark-rtl.min.css') }}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" />
@endif
<link href="{{ asset('libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.datetimepicker.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('libs/bootstrap-select/css/bootstrap-select.min.css') }}">
<!-- icons -->
<link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/default-skin.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('css/photoswipe.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('libs/ladda/ladda-themeless.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/touches.css') }}" rel="stylesheet" type="text/css">
<!-- third party css -->
@yield('styles')
<!-- third party css end -->