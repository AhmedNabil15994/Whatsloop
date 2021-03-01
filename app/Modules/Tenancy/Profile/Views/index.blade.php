{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css">
    i{
        border: 0 !important;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>
    </div>     
    <!-- end page title --> 
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="header-title"><i class="badge-outline-success fa fa-user"></i> {{ trans('main.account_setting') }}</h4>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/personalInfo') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons fa fa-user-tie badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.account_setting') }}</h4>
                                                <p>{{ trans('main.account_setting_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/changePassword') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons fas fa-user-lock badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title"> {{ trans('main.changePassword') }}</h4>
                                                <p> {{ trans('main.changePassword_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @if(\Helper::checkRules('subscription'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/subscription') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons fa fa-bell badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.subscriptionManage') }}</h4>
                                                <p>{{ trans('main.subscriptionManage_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif
                    </div>    

                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>

    <!-- end row-->
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="header-title"><i class="badge-outline-success mdi mdi-finance"></i> {{ trans('main.financial_setting') }}</h4>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        @if(\Helper::checkRules('paymentInfo'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/paymentInfo') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons mdi mdi-credit-card badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.payment_setting') }}</h4>
                                                <p>{{ trans('main.payment_setting_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif
                        @if(\Helper::checkRules('taxInfo'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/taxInfo') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons mdi mdi-percent badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title"> {{ trans('main.tax_setting') }}</h4>
                                                <p> {{ trans('main.tax_setting_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif
                    </div>    
                                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="header-title"><i class="badge-outline-success fa fa-cogs"></i> {{ trans('main.website_setting') }}</h4>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        @if(\Helper::checkRules('notifications'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/notifications') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons mdi mdi-alert-octagram-outline badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.notifications') }}</h4>
                                                <p>{{ trans('main.notifications_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif
                        @if(\Helper::checkRules('offers'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/offers') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons mdi mdi-offer badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.offers') }}</h4>
                                                <p>{{ trans('main.offers_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/services') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons mdi mdi-lan-connect badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.service_tethering') }}</h4>
                                                <p>{{ trans('main.service_tethering_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                    </div>    
                                    
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}

@section('scripts')

@endsection
