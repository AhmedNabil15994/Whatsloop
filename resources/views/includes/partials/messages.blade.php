@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        @foreach($errors->all() as $error)
            {{ $error }}<br/>
        @endforeach
    </div>
@elseif(session()->get('flash_success'))
{{--    <div class="alert alert-success" role="alert">--}}
{{--        <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--            <span aria-hidden="true">&times;</span>--}}
{{--        </button>--}}

{{--        @if(is_array(json_decode(session()->get('flash_success'), true)))--}}
{{--            {{ implode('', session()->get('flash_success')->all(':message<br/>')) }}--}}
{{--        @else--}}
{{--            {{ session()->get('flash_success') }}--}}
{{--        @endif--}}
{{--    </div>--}}
    @push('after-scripts')
    <script>
        Swal.fire({
            position: "top-right",
            icon: "success",
            title: '{{ is_array(json_decode(session()->get('flash_success'), true)) ? implode('', session()->get('flash_success')->all(':message<br/>')) : session()->get('flash_success') }}',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    @endpush

@elseif(session()->get('flash_warning'))
{{--    <div class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        @if(is_array(json_decode(session()->get('flash_warning'), true)))
            {{ implode('', session()->get('flash_warning')->all(':message<br/>')) }}
        @else
            {{ session()->get('flash_warning') }}
        @endif

    </div>--}}
    @push('after-scripts')
        <script>
            Swal.fire({
                position: "top-right",
                icon: "warning",
                title: '{{ is_array(json_decode(session()->get('flash_warning'), true)) ? implode('', session()->get('flash_warning')->all(':message<br/>')) : session()->get('flash_warning') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endpush

@elseif(session()->get('flash_info'))
{{--    <div class="alert alert-info" role="alert">--}}
{{--        <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--            <span aria-hidden="true">&times;</span>--}}
{{--        </button>--}}

{{--        @if(is_array(json_decode(session()->get('flash_info'), true)))--}}
{{--            {{ implode('', session()->get('flash_info')->all(':message<br/>')) }}--}}
{{--        @else--}}
{{--            {{ session()->get('flash_info') }}--}}
{{--        @endif--}}
{{--    </div>--}}

    @push('after-scripts')
        <script>
            Swal.fire({
                position: "top-right",
                icon: "info",
                title: '{{ is_array(json_decode(session()->get('flash_info'), true)) ? implode('', session()->get('flash_info')->all(':message<br/>')) : session()->get('flash_info') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endpush

@elseif(session()->get('flash_danger'))
{{--    <div class="alert alert-danger" role="alert">--}}
{{--        <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--            <span aria-hidden="true">&times;</span>--}}
{{--        </button>--}}

{{--        @if(is_array(json_decode(session()->get('flash_danger'), true)))--}}
{{--            {{ implode('', session()->get('flash_danger')->all(':message<br/>')) }}--}}
{{--        @else--}}
{{--            {{ session()->get('flash_danger') }}--}}
{{--        @endif--}}
{{--    </div>--}}

    @push('after-scripts')
        <script>
            Swal.fire({
                position: "top-right",
                icon: "error",
                title: '{{ is_array(json_decode(session()->get('flash_danger'), true)) ? implode('', session()->get('flash_danger')->all(':message<br/>')) : session()->get('flash_danger') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endpush

@elseif(session()->get('flash_message'))
{{--    <div class="alert alert-info" role="alert">--}}
{{--        <button type="button" class="close" data-dismiss="alert" aria-label="Close">--}}
{{--            <span aria-hidden="true">&times;</span>--}}
{{--        </button>--}}

{{--        @if(is_array(json_decode(session()->get('flash_message'), true)))--}}
{{--            {{ implode('', session()->get('flash_message')->all(':message<br/>')) }}--}}
{{--        @else--}}
{{--            {{ session()->get('flash_message') }}--}}
{{--        @endif--}}
{{--    </div>--}}

    @push('after-scripts')
        <script>
            Swal.fire({
                title: '{{ is_array(json_decode(session()->get('flash_message'), true)) ? implode('', session()->get('flash_message')->all(':message<br/>')) : session()->get('flash_message') }}',
                showConfirmButton: false,
                timer: 1500
            });
        </script>
    @endpush
@endif
