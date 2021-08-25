@extends('tenant.Layouts.master')
@section('title',trans('main.packages'))
@section('styles')
<style type="text/css" media="screen">
    .features{
        /*min-height: 260px;*/
    }
    .features.unlim{
        min-height: 395px;
    }
    .features.unlim .card-body.text-center.border-top{
        padding-bottom: 3.5rem;
        border-bottom: 1px solid #edeef7 !important;
    }
</style>
@endsection


{{-- Content --}}
@section('content')
    <!-- row -->
    <div class="row">
        @php
        $colorsArr = ['primary','warning','success','danger']
        @endphp
        @foreach($data->memberships as $key => $membership)
        <div class="col-sm-6 col-lg-6 col-xl-3">
            <div class="card pricing-card overflow-hidden">
                <div class="card-status bg-{{ $colorsArr[$key] }}"></div>
                <div class="card-body text-center">
                    <div class="card-title mb-0 tx-22">
                        {{ $membership->title }}
                    </div>
                </div>
                @if($membership->id != 4)
                <div class="card-body text-center">
                    <div class="display-5 font-weight-semibold  my-2">{{ $membership->monthly_after_vat }} {{ trans('main.sar2') }} / {{ trans('main.month') }}</div>
                        <div class="display-6">{{ $membership->annual_after_vat }} {{ trans('main.sar2') }} / {{ trans('main.year') }}</div>
                </div>
                @endif
                <div class="features {{ $membership->id == 4 ? 'unlim' : '' }}">
                    @foreach($membership->featruesArr as $one)
                    <div class="card-body text-center border-top">
                        <div class="text-center mt-2">
                            <div class="display-{{ $membership->id != 4 ? '6' : '5' }} ">{{ $one }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-body text-center">
                    <div class="text-center mt-6">
                        <a href="{{ URL::to('/checkout?membership_id='.$membership->id) }}" class="btn btn-{{ $colorsArr[$key] }} btn-block">{{ trans('main.subscribe') }}</a>
                    </div>
                </div>
            </div>
        </div><!-- col-end -->
        @endforeach
    </div>
    <!-- end row -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
@endsection
