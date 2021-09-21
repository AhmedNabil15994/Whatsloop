<!-- JQuery min js -->
<script src="{{ asset('plugins/jquery/jquery-3.5.1.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.7.1/gsap.min.js"></script>
<!-- Bootstrap4 js-->
<script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>

<!--Internal  Chart.bundle js -->
<script src="{{ asset('plugins/chart.js/Chart.bundle.min.js') }}"></script>

<!-- Ionicons js -->
<script src="{{ asset('plugins/ionicons/ionicons.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('plugins/moment/moment.js') }}"></script>

<!--Internal Sparkline js -->
<script src="{{ asset('plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
<!--Internal  Flot js-->
<script src="{{ asset('plugins/jquery.flot/jquery.flot.js') }}"></script>
<script src="{{ asset('plugins/jquery.flot/jquery.flot.pie.js') }}"></script>
<script src="{{ asset('plugins/jquery.flot/jquery.flot.resize.js') }}"></script>
<script src="{{ asset('plugins/jquery.flot/jquery.flot.categories.js') }}"></script>
<script src="{{ asset('js/dashboard.sampledata.js') }}"></script>
<script src="{{ asset('js/chart.flot.sampledata.js') }}"></script>

<!--Internal Apexchart js-->
<script src="{{ asset('js/apexcharts.js') }}"></script>

<!-- Chart-circle js -->
<script src="{{ asset('js/circle-progress.min.js') }}"></script>
<script src="{{ asset('js/chart-circle.js') }}"></script>

<!-- Rating js-->
<script src="{{ asset('plugins/rating/jquery.barrating.js') }}"></script>

<!-- Suggestion js-->
<script src="{{ asset('plugins/suggestion/jquery.input-dropdown.js') }}"></script>
<script src="{{ asset('js/search.js') }}"></script>

<!--Internal  Perfect-scrollbar js -->
<script src="{{ asset('plugins/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('plugins/perfect-scrollbar/p-scroll.js') }}"></script>
<script src="{{ asset('libs/dropzone/min/dropzone.min.js') }}"></script>

<!-- Eva-icons js -->
<script src="{{ asset('js/eva-icons.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" charset="UTF-8"></script>
<!-- right-sidebar js -->

@if(DIRECTION == 'ltr')
<script src="{{ asset('plugins/sidebar/sidebar.js') }}"></script>
<script src="{{ asset('switcher/js/switcher.js') }}"></script>
@else
<script src="{{ asset('plugins/sidebar/sidebar-rtl.js') }}"></script>
<script src="{{ asset('switcher/js/switcher-rtl.js') }}"></script>
@endif
<script src="{{ asset('plugins/sidebar/sidebar-custom.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ asset('plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
<!-- Sticky js -->
<script src="{{ asset('js/sticky.js') }}"></script>
<script src="{{ asset('js/modal-popup.js') }}"></script>

<!-- Left-menu js-->
<script src="{{ asset('plugins/side-menu/sidemenu.js') }}"></script>

<!-- ECharts js-->
<script src="{{ asset('plugins/echart/echart.js') }}"></script>

<script src="{{ asset('libs/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/jquery.maskedinput/jquery.maskedinput.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/spectrum-colorpicker/spectrum.js') }}" type="text/javascript"></script>
<script src="{{ asset('plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}" type="text/javascript"></script>
<!--Internal  index js -->
<script src="{{ asset('js/apexcharts.js') }}"></script>
<script src="{{ asset('plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
@livewireScripts 

@yield('topScripts')
<script src="{{ asset('js/index.js') }}"></script>

<!-- custom js -->
<script src="{{ asset('js/custom.js') }}"></script>
<script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('libs/selectize/js/standalone/selectize.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>

<script src="{{ asset('components/notifications.js') }}"></script>

<script src="{{ asset('components/multi-lang.js') }}"></script>

<script src="{{ asset('components/multi-channels.js') }}"></script>

<script src="{{ asset('js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('js/utils.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-elements.js') }}" type="text/javascript"></script>

<script src="{{ asset('components/globals.js') }}"></script>

<!-- third party js -->
@yield('scripts')
<!-- third party js ends -->