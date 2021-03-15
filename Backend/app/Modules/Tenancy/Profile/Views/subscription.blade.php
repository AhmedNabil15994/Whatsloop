{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css">
    i{
        border: 0 !important;
    }

    .button-list {
        display: block;
        margin: auto;
    }
    a b{
        display: inline-block;
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
                        <li class="breadcrumb-item"><a href="{{ URL::to('/profile') }}">{{ trans('main.myAccount') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>
    </div>     


    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card-box">
                <i class="fab fa-rocketchat text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.messages') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.messages') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->allMessages }}</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card-box">
                <i class="fas fa-reply text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.sentMessages') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.sentMessages') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->sentMessages }}</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card-box">
                <i class="fas fa-redo text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.incomeMessages') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.incomeMessages') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->incomingMessages }}</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card-box">
                <i class="fas fa-users text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.contacts') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.contacts') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->contactsCount }}</span></h2>
            </div>
        </div>
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ trans('main.actions') }}</h4>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="button-list text-center">
                            <a class="btn screen text-light btn-success waves-effect waves-light">
                                {{ trans('main.screenshot') }}<span class="btn-label-right"><i class="fas fa-camera"></i></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/sync') }}" class="btn btn-info waves-effect waves-light">
                                {{ trans('main.sync') }}<span class="btn-label-right"><i class="fas fa-undo"></i></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/reconnect') }}" class="btn btn-warning waves-effect waves-light">
                                {{ trans('main.reestablish') }}<span class="btn-label-right"><i class="fas fa-redo"></i></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/closeConn') }}" class="btn btn-danger waves-effect waves-light">
                                {{ trans('main.closeConn') }}<span class="btn-label-right"><i class="far fa-times-circle"></i></span>
                            </a>
                        </div>
                    </div> 
                    <hr class="mt-3 mb-3">
                    <div class="row">
                        <div class="button-list text-center">
                            <a href="{{ URL::to('/profile/subscription/read/1') }}" class="btn btn-primary waves-effect waves-light">
                                {{ trans('main.readAll') }}<span class="btn-label-right"><i class="fas fa-eye"></i></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/read/0') }}" class="btn btn-dark waves-effect waves-light">
                                {{ trans('main.unreadAll') }}<span class="btn-label-right"><i class="fas fa-eye-slash"></i></span>
                            </a>
                        </div>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-2">
                            <img src="{{ asset('images/logoOnly.jpg') }}" width="100%" alt="">
                        </div>
                        <div class="col-10">
                            <div class="row">
                                <!--begin::Title-->
                                <div class="d-flex justify-content-between flex-wrap mt-1">
                                    <div class="d-flex mr-3">
                                        <a href="#" class="text-dark font-size-h5 font-weight-bold mr-3">{{ $data->channel->name }}</a>
                                        <a href="#">
                                            <i class="fas fa-check-circle badge-outline-success"></i>
                                        </a>
                                    </div>
                                </div>
                                <!--end::Title-->
                                <!--begin::Content-->
                                <div class="d-flex flex-wrap justify-content-between mt-1">
                                    <div class="d-flex flex-column flex-grow-1 pr-8">
                                        <div class="d-flex flex-wrap mb-4">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.channel') }} : <b>{{ $data->channel->id }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.phone') }} : <b>{{ str_replace('@c.us', '', $data->me->id) }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.connection_date') }}: <b style="direction: ltr;display: inline-block;">{{ $data->status->created_at }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_status') }} : <b><div class="label label-lg label-light-success label-inline">{{ $data->status->statusText }}</div></b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.msgSync') }} : <b><div class="label label-lg label-light-success label-inline">{{ $data->allMessages > 0 ? trans('main.synced') : trans('main.notSynced') }}</div></b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.contSync') }} : <b><div class="label label-lg label-light-success label-inline">{{ $data->contactsCount > 0 ? trans('main.synced') : trans('main.notSynced') }}</div></b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans("main.phone_battery") }} : <b>{{ $data->me->battery }}%</b>
                                                            </a>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_type') }} : <b>{{ $data->me->device['manufacturer'] }}</b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_model') }} : <b>{{ $data->me->device['model'] }}</b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.os_ver') }} : <b>{{ $data->me->device['os_version'] }}</b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center w-25 flex-fill float-right mt-lg-12 mt-8">
                                        <span class="font-weight-bold text-dark-75">{{ trans('main.leftDays') }}</span>
                                        <div class="progress progress-xs mx-3 w-100">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $data->channel->rate }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="80"></div>
                                        </div>
                                        <span class="font-weight-bolder text-dark">{{ $data->channel->leftDays }} {{ trans('main.day') }}</span>
                                    </div>
                                </div>
                                <!--end::Content-->
                            </div>
                        </div>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    
</div> <!-- container -->
@endsection

@section('modals')
@include('tenant.Partials.screen_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('components/subscription.js') }}" type="text/javascript"></script>
@endsection
