@extends('tenant.Layouts.master')
@section('title',trans('main.packages'))
@section('styles')
<style type="text/css" media="screen">
    .card.card-pricing{
        min-height: 520px;
    }
    .card-body .btn-primary{
        display: block;
        margin: auto;
        position: absolute;
    bottom: 25px;
    left: calc(50% - 50px);
    }
</style>
@endsection


{{-- Content --}}
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">{{ trans('main.packages') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ trans('main.packages') }}</h4>
            </div>
        </div>
    </div>     
    <!-- end page title --> 

    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Pricing Title-->
            <div class="text-center">
                <h3 class="mb-2"><b>{{ trans('main.packages') }}</b></h3>
                <p class="text-muted w-50 m-auto">
                    We have plans and prices that fit your business perfectly. Make your client site a success with our products.
                </p>
            </div>
            <!-- Plans -->
            <div class="row my-4">
                @foreach($data->memberships as $membership)
                <div class="col-md-3">
                    <div class="card card-pricing">
                        <div class="card-body text-center">
                            <p class="card-pricing-plan-name font-weight-bold text-uppercase">{{ $membership->title }}</p>
                            <span class="card-pricing-icon text-primary">
                                <i class="fe-users"></i>
                            </span>
                            @if($membership->id != 4)
                            <h2 class="card-pricing-price">{{ $membership->monthly_after_vat }} {{ trans('main.sar') }}<span>/ {{ trans('main.month') }}</span></h2>
                            <h2 class="card-pricing-price" style="padding-top: 0">{{ $membership->annual_after_vat }} {{ trans('main.sar') }}<span>/ {{ trans('main.year') }}</span></h2>
                            @endif
                            <ul class="card-pricing-features">
                                @foreach($membership->featruesArr as $one)
                                <li>{{ $one }}</li>
                                @endforeach
                            </ul>
                            @if($membership->id != 4)
                            <a href="{{ URL::to('/checkout?membership_id='.$membership->id) }}" class="btn btn-primary waves-effect waves-light mt-4 mb-2 width-sm">{{ trans('main.subscribe') }}</a>
                            @endif
                        </div>
                    </div> <!-- end Pricing_card -->
                </div> <!-- end col -->
                @endforeach
            </div>
            <!-- end row -->

        </div> <!-- end col-->
    </div>
    <!-- end row -->
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
@endsection
