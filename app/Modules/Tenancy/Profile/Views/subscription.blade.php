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
        <div class="col-md-6 col-xl-2">
            <div class="card-box">
                <i class="fab fa-rocketchat text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.messages') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.messages') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">31,570</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card-box">
                <i class="fas fa-reply text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.sentMessages') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.sentMessages') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">3101</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card-box">
                <i class="fas fa-redo text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.incomeMessages') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.incomeMessages') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">203</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card-box">
                <i class="fas fa-comment text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.conversations') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.conversations') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">1202</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card-box">
                <i class="fas fa-users text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.contacts') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.contacts') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->contactsCount }}</span></h2>
            </div>
        </div>
        <div class="col-md-6 col-xl-2">
            <div class="card-box">
                <i class="fas fa-user text-muted float-right" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="{{ trans('main.users') }}"></i>
                <h4 class="mt-0 font-16">{{ trans('main.users') }}</h4>
                <h2 class="text-success my-3 text-center"><span data-plugin="counterup">{{ $data->usersCount }}</span></h2>
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
                            <button type="button" class="btn btn-success waves-effect waves-light">
                                Screenshot<span class="btn-label-right"><i class="fas fa-camera"></i></span>
                            </button>
                            <button type="button" class="btn btn-info waves-effect waves-light">
                                Sync<span class="btn-label-right"><i class="fas fa-undo"></i></span>
                            </button>
                            <button type="button" class="btn btn-warning waves-effect waves-light">
                                Re-establish Connection<span class="btn-label-right"><i class="fas fa-redo"></i></span>
                            </button>
                            <button type="button" class="btn btn-danger waves-effect waves-light">
                                Close Connection<span class="btn-label-right"><i class="far fa-times-circle"></i></span>
                            </button>
                        </div>
                    </div> 
                    <hr class="mt-3 mb-3">
                    <div class="row">
                        <div class="button-list text-center">
                            <button type="button" class="btn btn-primary waves-effect waves-light">
                                Read All Messages<span class="btn-label-right"><i class="fas fa-eye"></i></span>
                            </button>
                            <button type="button" class="btn btn-dark waves-effect waves-light">
                                Un-read All Messages<span class="btn-label-right"><i class="fas fa-eye-slash"></i></span>
                            </button>
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
                                                        {{ trans('main.phone') }} : <b>{{ $data->data->phone }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.connection_date') }}: <b style="direction: ltr;display: inline-block;">{{ DATE_TIME }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_status') }} : <b><div class="label label-lg label-light-success label-inline">{{ trans('main.phone_connected') }}</div></b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.msgSync') }} : <b><div class="label label-lg label-light-success label-inline">{{ trans('main.synced') }}</div></b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.contSync') }} : <b><div class="label label-lg label-light-success label-inline">{{ trans('main.synced') }}</div></b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans("main.phone_battery") }} : <b>100%</b>
                                                            </a>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_type') }} : <b>Apple</b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_model') }} : <b>iPhone 7 Plus</b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.os_ver') }} : <b>14.1</b>
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
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 93.66%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="80"></div>
                                        </div>
                                        <span class="font-weight-bolder text-dark">3255 {{ trans('main.day') }}</span>
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

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('components/profile_services.js') }}" type="text/javascript"></script>
@endsection
