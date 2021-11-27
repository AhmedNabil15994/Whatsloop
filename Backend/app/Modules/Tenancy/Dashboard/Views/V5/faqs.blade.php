@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.faqs'))
@section('styles')

@endsection


{{-- Content --}}
@section('content')
<!-- row -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div>
                    <h6 class="card-title mb-1">{{ trans('main.faq_title') }}</h6>
                    <p class="tx-12 text-muted card-sub-title">{{ trans('main.faq_p') }}</p>
                </div>
                <div aria-multiselectable="true" class="accordion" id="accordion" role="tablist">
                    @foreach($data as $key => $one)
                    <div class="card mb-0 faq">
                        <div class="card-header" id="headingOne{{ $key }}" role="tab">
                            <a aria-controls="collapseOne{{ $key }}" aria-expanded="true" data-toggle="collapse" href="#collapseOne{{ $key }}">{{ $one->title }}</a>
                        </div>
                        <div aria-labelledby="headingOne{{ $key }}" class="collapse {{ $key == 0 ? 'show' : '' }}" data-parent="#accordion" id="collapseOne{{ $key }}" role="tabpanel">
                            <div class="card-body pd-2">
                                <p class="tx-18 text-muted card-sub-title">{{ $one->description }}</p>
                                @if($one->photo != '')
                                @if($one->type == 'photo')
                                <img src="{{ $one->photo }}" alt="">
                                @else
                                <video width="320" height="240" controls>
                                    <source src="{{ $one->photo }}" type="video/mp4">
                                </video>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div><!-- accordion -->
            </div>
        </div>
    </div>
</div>
<!-- row closed -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
@endsection
