<link rel="icon" href="{{ URL::asset('V5/images/logoChannel.png') }}" type="image/ico" />
<link rel="stylesheet" href="{{ asset('V5/css/font.css') }}" />

<link rel="stylesheet" href="{{ asset('V5/css/flaticon.css') }}" />

<link rel="stylesheet" type="text/css" href="{{ asset('V5/plugins/sweet-alert/sweetalert.css') }}">
<link href="{{ asset('V5/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('V5/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}">
<link href="{{ asset('V5/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="{{ asset('V5/css/jquery-ui.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/intlTelInput.min.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/animate.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/bootstrap.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/font-awesome.min.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/owl.carousel.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/toastr.min.css') }}"  type="text/css">
<link rel="stylesheet" href="{{ asset('V5/css/buttons.css') }}" />
@if(DIRECTION == 'rtl')
<link rel="stylesheet" href="{{ asset('V5/css/bootstrap-rtl.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/style.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/responisve.css') }}" />
@else
<link rel="stylesheet" href="{{ asset('V5/css/ltr.css') }}" />
@endif
<link rel="stylesheet" href="{{ asset('V5/css/photoswipe.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/dark.css') }}" />
<link rel="stylesheet" href="{{ asset('V5/css/touches.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker-standalone.min.css" integrity="sha256-SMGbWcp5wJOVXYlZJyAXqoVWaE/vgFA5xfrH3i/jVw0=" crossorigin="anonymous" />
<!-- third party css -->
@yield('styles')
<!-- third party css end 