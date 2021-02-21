<script src="{{ asset('js/vendor.min.js') }}"></script>
<!-- Chart JS -->
<!-- Sweet Alerts js -->
<script src="{{ asset('libs/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- Sweet alert init js-->
<script src="{{ asset('libs/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('libs/chart.js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('libs/moment/min/moment.min.js') }}"></script>
<script src="{{ asset('libs/jquery.scrollto/jquery.scrollTo.min.js') }}"></script>
<script src="{{ asset('libs/dropzone/min/dropzone.min.js') }}"></script>
@yield('topScripts')
<!-- Chat app -->
{{-- <script src="{{ asset('js/pages/jquery.chat.js') }}"></script> --}}
<!-- Todo app -->
{{-- <script src="{{ asset('js/pages/jquery.todo.js') }}"></script> --}}
<!-- Dashboard init JS -->
<!-- App js-->
<script src="{{ asset('js/app.min.js') }}"></script>

<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('components/notifications.js') }}"></script>
<script src="{{ asset('components/multi-lang.js') }}"></script>
<script src="{{ asset('components/multi-channels.js') }}"></script>

<!-- Loading buttons js -->
<script src="{{ asset('libs/ladda/spin.min.js') }}"></script>
<script src="{{ asset('libs/ladda/ladda.min.js') }}"></script>

<!-- Buttons init js-->
<script src="{{ asset('js/pages/loading-btn.init.js') }}"></script>
<script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" type="text/javascript"></script>


<script src="{{ asset('components/globals.js') }}"></script>
<!-- third party js -->
@yield('scripts')
<!-- third party js ends -->