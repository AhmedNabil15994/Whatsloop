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

                       {{--  @if(\Helper::checkRules('addons'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/addons') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons fas fa-star badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.addons') }}</h4>
                                                <p>{{ trans('main.addons_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif

                        @if(\Helper::checkRules('extraQuotas'))
                        <div class="col-4">
                            <div class="card myCard">
                                <a href="{{ URL::to('/profile/extraQuotas') }}">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-1">
                                                <i class="myicons fas fa-star badge-outline-success"></i> 
                                            </div>
                                            <div class="col-11 pr-3 pl-3">
                                                <h4 class="header-title">{{ trans('main.extraQuotas') }}</h4>
                                                <p>{{ trans('main.extraQuotas_p') }}</p>
                                            </div>
                                        </div>
                                    </div> <!-- end card body-->
                                </a>
                            </div> <!-- end card -->
                        </div>
                        @endif --}}
                        
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
