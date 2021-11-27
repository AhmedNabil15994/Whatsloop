<script src="{{ asset('V5/js/jquery-1.11.2.min.js') }}"></script>
<script src="{{ asset('V5/js/jquery-ui.js') }}"></script>
<script src="{{ asset('V5/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('V5/js/owl.carousel.js') }}"></script>
<script src="{{ asset('V5/js/wow.min.js') }}"></script>
<script src="{{ asset('V5/js/intlTelInput.js') }}"></script>
<script src="{{ asset('V5/js/Chart.min.js') }}"></script>
<script src="{{ asset('V5/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('V5/js/jquery.nicescroll.js') }}"></script>

<!-- Moment js -->
<script src="{{ asset('V5/plugins/moment/moment.js') }}"></script>
<script src="{{ asset('V5/js/search.js') }}"></script>

<script src="{{ asset('V5/libs/dropzone/min/dropzone.min.js') }}"></script>


<script src="{{ asset('V5/plugins/sweet-alert/sweetalert.min.js') }}"></script>
<script src="{{ asset('V5/plugins/sweet-alert/jquery.sweet-alert.js') }}"></script>
<script src="{{ asset('V5/plugins/select2/js/select2.min.js') }}" type="text/javascript"></script>
{{-- 
    <script src="{{ asset('plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js') }}" type="text/javascript"></script>
    <script type="text/javascript" src="{{ asset('V5/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}" charset="UTF-8"></script>
 --}}
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js" integrity="sha256-5YmaxAwMjIpMrVlK84Y/+NjCpKnFYa8bWWBbUHSBGfU=" crossorigin="anonymous"></script>
@livewireScripts 
<script>
    window.livewire_app_url = "{{ URL::to('/') }}"; 
</script>
@yield('topScripts')
{{-- <script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
 --}}
<script src="{{ asset('V5/js/toastr.min.js') }}"></script>
<script src="{{ asset('V5/components/notifications.js') }}"></script>
<script src="{{ asset('V5/components/multi-lang.js') }}"></script>
<script src="{{ asset('V5/components/multi-channels.js') }}"></script>
<script src="{{ asset('V5/js/utils.js') }}" type="text/javascript"></script>
{{-- <script src="{{ asset('js/form-elements.js') }}" type="text/javascript"></script> --}}
<script src="{{ asset('V5/js/custom.js') }}"></script>
<script src="{{ asset('V5/components/globals.js') }}"></script>
@yield('scripts')
