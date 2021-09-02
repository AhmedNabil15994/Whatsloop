<!-- JQuery min js -->
<script src="{{ asset('tenancy/assets/plugins/jquery/jquery-3.5.1.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.1/gsap.min.js"></script>
<!-- Bootstrap4 js-->
<script src="{{ asset('tenancy/assets/plugins/bootstrap/popper.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!--Internal  Chart.bundle js -->
<script src="{{ asset('tenancy/assets/plugins/chart.js/Chart.bundle.min.js') }}"></script>

<!-- Ionicons js -->
<script src="{{ asset('tenancy/assets/plugins/ionicons/ionicons.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('tenancy/assets/plugins/moment/moment.js') }}"></script>

<!--Internal Sparkline js -->
<script src="{{ asset('tenancy/assets/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('tenancy/assets/plugins/raphael/raphael.min.js') }}"></script>
<!--Internal  Flot js-->
<script src="{{ asset('tenancy/assets/plugins/jquery.flot/jquery.flot.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/jquery.flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/jquery.flot/jquery.flot.categories.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/dashboard.sampledata.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/chart.flot.sampledata.js') }}"></script>

<!--Internal Apexchart js-->
<script src="{{ asset('tenancy/assets/js/apexcharts.js') }}"></script>

<!-- Chart-circle js -->
<script src="{{ asset('tenancy/assets/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/chart-circle.js') }}"></script>

<!-- Rating js-->
<script src="{{ asset('tenancy/assets/plugins/rating/jquery.barrating.js') }}"></script>

<!-- Suggestion js-->
<script src="{{ asset('tenancy/assets/plugins/suggestion/jquery.input-dropdown.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/search.js') }}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{ asset('tenancy/assets/plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/perfect-scrollbar/p-scroll.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/dropzone/min/dropzone.min.js') }}"></script>

<!-- Eva-icons js -->
<script src="{{ asset('tenancy/assets/js/eva-icons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('tenancy/assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" charset="UTF-8"></script>
<!-- right-sidebar js -->

@if(DIRECTION == 'ltr')
<script src="{{ asset('tenancy/assets/plugins/sidebar/sidebar.js') }}"></script>
<script src="{{ asset('tenancy/assets/switcher/js/switcher.js') }}"></script>
@else
<script src="{{ asset('tenancy/assets/plugins/sidebar/sidebar-rtl.js') }}"></script>
<script src="{{ asset('tenancy/assets/switcher/js/switcher-rtl.js') }}"></script>
@endif
<script src="{{ asset('tenancy/assets/plugins/sidebar/sidebar-custom.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
<!-- Sticky js -->
<script src="{{ asset('tenancy/assets/js/sticky.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/modal-popup.js') }}"></script>

<!-- Left-menu js-->
<script src="{{ asset('tenancy/assets/plugins/side-menu/sidemenu.js') }}"></script>

<!-- ECharts js-->
<script src="{{ asset('tenancy/assets/plugins/echart/echart.js') }}"></script>

<script src="{{ asset('tenancy/assets/libs/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('tenancy/assets/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('tenancy/assets/plugins/jquery.maskedinput/jquery.maskedinput.js') }}" type="text/javascript"></script>
<script src="{{ asset('tenancy/assets/plugins/spectrum-colorpicker/spectrum.js') }}" type="text/javascript"></script>
<script src="{{ asset('tenancy/assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('tenancy/assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js') }}" type="text/javascript"></script>
<!--Internal  index js -->
<script src="{{ asset('tenancy/assets/js/apexcharts.js') }}"></script>
@yield('topScripts')
<script src="{{ asset('tenancy/assets/js/index.js') }}"></script>

<!-- custom js -->
<script src="{{ asset('tenancy/assets/js/custom.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/selectize/js/standalone/selectize.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/toastr.min.js') }}"></script>

<script src="{{ asset('tenancy/assets/components/notifications.js') }}"></script>

<script src="{{ asset('tenancy/assets/components/multi-lang.js') }}"></script>

<script src="{{ asset('tenancy/assets/components/multi-channels.js') }}"></script>

<script src="{{ asset('tenancy/assets/js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('tenancy/assets/js/utils.js') }}" type="text/javascript"></script>

<script src="{{ asset('tenancy/assets/components/globals.js') }}"></script>

<!-- third party js -->
@yield('scripts')
<!-- third party js ends -->