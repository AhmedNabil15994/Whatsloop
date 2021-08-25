<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
<!-- Bootstrap css -->
<link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet" />



<!-- Icons css -->

<!--  Owl-carousel css-->
{{-- <link href="{{ asset('plugins/owl-carousel/owl.carousel.css') }}" rel="stylesheet" /> --}}

<!--  Right-sidemenu css -->
<link href="{{ asset('plugins/sidebar/sidebar.css') }}" rel="stylesheet">

<!-- Sidemenu css -->

<!-- Maps css -->
{{-- <link href="{{ asset('plugins/jqvmap/jqvmap.min.css') }}" rel="stylesheet"> --}}

<!-- Jvectormap css -->
{{-- <link href="{{ asset('plugins/jqvmap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" /> --}}


<!--- Color css-->

<!---Skinmodes css-->
<link href="{{ asset('libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link href="{{ asset('libs/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" /> --}}
<!-- icons -->
<link href="{{ asset('css/toastr.min.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">

@if(DIRECTION == 'ltr')
<link rel="stylesheet" type="text/css" href="{{ asset('switcher/css/switcher.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('switcher/css/demo.css') }}">
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/intlTelInput.css') }}">
<link rel="stylesheet" href="{{ asset('css/sidemenu.css') }}">
<!-- style css -->
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
<link href="{{ asset('css/style-dark.css') }}" rel="stylesheet">

<link id="theme" href="{{ asset('css/colors/color.css') }}" rel="stylesheet">
<link href="{{ asset('css/skin-modes.css') }}" rel="stylesheet" />
@else
<link rel="stylesheet" type="text/css" href="{{ asset('switcher/css/switcher-rtl.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('switcher/css/demo.css') }}">
<link href="{{ asset('css-rtl/icons.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('plugins/telephoneinput/telephoneinput-rtl.css') }}">
<link rel="stylesheet" href="{{ asset('css-rtl/sidemenu.css') }}">
<link href="{{ asset('css-rtl/style.css') }}" rel="stylesheet">
<link href="{{ asset('css-rtl/style-dark.css') }}" rel="stylesheet">
<link id="theme" href="{{ asset('css-rtl/colors/color.css') }}" rel="stylesheet">
<link href="{{ asset('css-rtl/skin-modes.css') }}" rel="stylesheet" />
<link href="{{ asset('css-rtl/animate.css') }}" rel="stylesheet">
@endif
<link href="{{ asset('css/touches.css') }}" rel="stylesheet" type="text/css">
<!-- third party css -->
@yield('styles')
<!-- third party css end -->