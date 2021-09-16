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
                            <a class="btn screen text-light label label-light-success tx-bold waves-effect waves-light">
                                <i class="fas fa-camera"></i> {{ trans('main.screenshot') }}<span class="btn-label-right"></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/sync') }}" class="btn label tx-bold label-light-info waves-effect waves-light">
                                <i class="fas fa-undo"></i> {{ trans('main.sync') }}<span class="btn-label-right"></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/reconnect') }}" class="btn label tx-bold label-light-warning waves-effect waves-light">
                                <i class="fas fa-redo"></i> {{ trans('main.reestablish') }}<span class="btn-label-right"></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/closeConn') }}" class="btn label tx-bold label-light-danger waves-effect waves-light">
                                <i class="far fa-times-circle"></i> {{ trans('main.closeConn') }}<span class="btn-label-right"></span>
                            </a>
                        </div>
                    </div> 
                    <hr class="mt-3 mb-3">
                    <div class="row">
                        <div class="button-list text-center">
                            <a href="{{ URL::to('/profile/subscription/read/1') }}" class="btn label tx-bold label-light-success waves-effect waves-light">
                                <i class="fas fa-eye"></i> {{ trans('main.readAll') }}<span class="btn-label-right"></span>
                            </a>
                            <a href="{{ URL::to('/profile/subscription/read/0') }}" class="btn label tx-bold label-light-primary waves-effect waves-light">
                                <i class="fas fa-eye-slash"></i> {{ trans('main.unreadAll') }}<span class="btn-label-right"></span>
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
                            <img class="me-avatar" src="{{ @$data->me->avatar == null ? asset('images/logoOnly.jpg') : @$data->me->avatar }}"  alt="">
                        </div>
                        <div class="col-10">
                            <div class="row">
                                <!--begin::Title-->
                                {{-- <div class="d-flex justify-content-between flex-wrap mt-1">
                                    <div class="d-flex mr-3">
                                        <a href="#" class="text-dark font-size-h5 font-weight-bold mr-3">{{ $data->channel != null ? $data->channel->name : '' }}</a>
                                        <a href="#">
                                            <i class="fas fa-check-circle badge-outline-success"></i>
                                        </a>
                                    </div>
                                </div> --}}
                                <!--end::Title-->
                                <!--begin::Content-->
                                <div class="d-flex flex-wrap justify-content-between mt-1">
                                    <div class="d-flex flex-column flex-grow-1 pr-8">
                                        <div class="d-flex flex-wrap mb-4">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.channel') }} : <b># {{ $data->channel != null ? $data->channel->instanceId : '' }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.phone') }} : <b>{{ str_replace('@c.us', '', @$data->me->id) }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-4">
                                                    <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                        {{ trans('main.connection_date') }}: <b style="direction: ltr;display: inline-block;">{{ $data->status != null ? $data->status->created_at : '' }}</b>
                                                    </a>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary">
                                                                <b>{{ trans('main.phone_status') }} : </b><div class="label label-lg label label-light-success d-inline">{{ $data->status != null ? $data->status->statusText : '' }}</div>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary ">
                                                                <b>{{ trans('main.msgSync') }} : </b><div class="label label-lg label label-light-success d-inline">{{ $data->allMessages > 0 ? trans('main.synced') : trans('main.notSynced') }}</div>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <a class="text-dark-50 text-hover-primary ">
                                                                <b>{{ trans('main.contSync') }} : </b><div class="label label-lg label label-light-success d-inline">{{ $data->contactsCount > 0 ? trans('main.synced') : trans('main.notSynced') }}</div>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans("main.phone_battery") }} : <b>{{ @$data->me->battery }}%</b>
                                                            </a>
                                                        </div>

                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_type') }} : <b>{{ @$data->me->device['manufacturer'] }}</b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.phone_model') }} : <b>{{ @$data->me->device['model'] }}</b>
                                                            </a>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <a class="text-dark-50 text-hover-primary font-weight-bold">
                                                                {{ trans('main.os_ver') }} : <b>{{ @$data->me->device['os_version'] }}</b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center w-25 flex-fill float-right mt-lg-12 mt-8">
                                        <span class="font-weight-bold text-dark-75">{{ trans('main.leftDays') }}</span>
                                        <div class="progress progress-xs mx-3 w-85">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $data->channel != null ? $data->channel->rate : '' }}%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="80"></div>
                                        </div>
                                        <span class="font-weight-bolder text-dark">{{ $data->channel != null ? $data->channel->leftDays : '' }} {{ trans('main.day') }}</span>
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

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="mainCol mb-4">
                                <div class="row">
                                    <div class="col-8">
                                        <h3 class="card-title "> {{ trans('main.currentPackage') }}</h3> 
                                    </div>
                                    @if(IS_ADMIN && $data->subscription->package_id != 3 && $data->subscription->package_id != 4)
                                    <div class="col-4 text-right">
                                        <a href="{{ URL::to('/updateSubscription?type=membership') }}" class="btn btn-dark"> <i class="fa fa-pencil-alt"></i> {{ trans('main.upgrade') }}</a> 
                                    </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-6 text-left text-gray">{{ trans('main.packageName') }}</div>
                                    <div class="col-6 text-right text-gray">{{ trans('main.substatus') }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-6 text-left">
                                        <span class="btn btn-outline-primary">{{ $data->subscription->package_name }}</span> 
                                    </div>
                                    <div class="col-6 text-right">
                                        <span class="btn btn-{{ $data->subscription->channelStatus == 1 ? 'success' : 'danger' }}">{{ $data->subscription->channelStatus == 1 ? trans('main.active') : trans('main.notActive') }}</span> 
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="mainCol">
                                <div class="row">
                                    <div class="col-6">
                                        <h3 class="card-title mb-2"> {{ trans('main.nextMillestone') }}</h3> 
                                    </div>
                                    @if(!in_array(date('d',strtotime($data->subscription->end_date)) , [1,28,29,30,31]) && IS_ADMIN)
                                    <div class="col-6 text-right">
                                        <a href="#" class="btn btn-dark"> <i class="mdi mdi-transfer"></i> {{ trans('main.transferPayment') }}</a> 
                                    </div>
                                    @endif
                                </div>
                                <div class="row mb-4">
                                    <div class="col-4 info">
                                        <span class="text-gray">{{ $data->subscription->end_date }}</span>
                                    </div>
                                    <div class="col-3 noPadd">
                                        <span class="btn btn-outline-primary">{{ $data->subscription->leftDays }} {{ trans('main.leftDays') }}</span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 info">
                                        <span class="text-gray">{{ trans('main.substartDate') }}</span>
                                    </div>
                                    <div class="col-2">
                                        <span class="btn btn-primary">{{ $data->subscription->start_date }}</span>
                                    </div>
                                    <div class="col-4 info">
                                        <span class="text-gray">{{ trans('main.subendDate') }}</span>
                                    </div>
                                    <div class="col-2">
                                        <span class="btn btn-primary">{{ $data->subscription->end_date }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="mainCol">
                                <div class="row">
                                    <div class="col-8">
                                        <h3 class="card-title mb-7"> {{ trans('main.addons') }}</h3> 
                                    </div>
                                    @if(IS_ADMIN)
                                    <div class="col-4 text-right">
                                        <a href="{{ URL::to('/updateSubscription?type=addon') }}" class="btn btn-dark"> <i class="fa fa-pencil-alt"></i> {{ trans('main.edit') }}</a> 
                                    </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @foreach($data->subscription->addons as $addon)
                                    <div class="col-sm-12 col-lg-4 addons">
                                        <div class="card custom-card">
                                            <div class="card-body text-center">
                                                <div class="user-lock text-center">
                                                    <div class="dropdown text-right">
                                                        <a href="#" class="option-dots" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                            <i class="fe fe-more-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right shadow" x-placement="bottom-end">
                                                            @if($addon->status == 2)
                                                            <a class="dropdown-item" href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/4') }}"><i class="fe fe-refresh-ccw mr-2"></i> {{ trans('main.renew') }}</a>
                                                            <a class="dropdown-item" href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/5') }}"><i class="fe fe-trash-2 mr-2"></i> {{ trans('main.delete') }}</a>
                                                            @elseif($addon->status == 1)
                                                            <a class="dropdown-item" href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/3') }}"><i class="la la-close mr-2"></i> {{ trans('main.disable') }}</a>
                                                            @elseif($addon->status == 3)
                                                            <a class="dropdown-item" href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/1') }}"><i class="la la-check mr-2"></i> {{ trans('main.enable') }}</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <h5 class="mb-1 mt-3  card-title">{{ $addon->Addon->title }}</h5>
                                                <p class="mb-2 mt-1 btn btn-{{ $addon->status == 1 ? 'success' : 'danger' }}">{{ $addon->statusText }}</p>
                                                <p class="text-muted text-center mt-1">{{ $addon->start_date . ' -- ' . $addon->end_date }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="mainCol">
                                <div class="row">
                                    <div class="col-8">
                                        <h3 class="card-title mb-7"> {{ trans('main.extraQuotas') }}</h3> 
                                    </div>
                                    @if(IS_ADMIN)
                                    <div class="col-4 text-right">
                                        <a href="{{ URL::to('/updateSubscription?type=extra_quota') }}" class="btn btn-dark"> <i class="fa fa-pencil-alt"></i> {{ trans('main.edit') }}</a> 
                                    </div>
                                    @endif
                                </div>
                                <div class="row">
                                    @foreach($data->subscription->extra_quotas as $extra_quota)
                                    <div class="col-sm-12 col-lg-4 extra_quota">
                                        <div class="card custom-card">
                                            <div class="card-body text-center">
                                                @if($extra_quota->status == 2)
                                                <div class="user-lock text-center">
                                                    <div class="dropdown text-right">
                                                        <a href="#" class="option-dots" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                            <i class="fe fe-more-vertical"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-right shadow" x-placement="bottom-end">
                                                            <a class="dropdown-item" href="{{ URL::to('/updateQuotaStatus/'.$extra_quota->id.'/4') }}"><i class="fe fe-refresh-ccw mr-2"></i> {{ trans('main.renew') }}</a>
                                                            <a class="dropdown-item" href="{{ URL::to('/updateQuotaStatus/'.$extra_quota->id.'/5') }}"><i class="fe fe-trash-2 mr-2"></i> {{ trans('main.delete') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <h5 class="mb-1 mt-3  card-title">{{ $extra_quota->ExtraQuota->extra_count . ' '.$extra_quota->ExtraQuota->extraTypeText . ' ' . ($extra_quota->ExtraQuota->extra_type == 1 ? trans('main.msgPerDay') : '')}}</h5>
                                                <p class="mb-2 mt-1 btn btn-{{ $extra_quota->status == 1 ? 'success' : 'danger' }}">{{ $extra_quota->statusText }}</p>
                                                <p class="text-muted text-center mt-1">{{ $extra_quota->start_date . ' -- ' . $extra_quota->end_date }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <div class="row">
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="plan-card text-center">
                        <i class="fas fa-comments plan-icon text-primary"></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.messages') }}</h6>
                        <h2 class="mb-2">{{ $data->allMessages }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="plan-card text-center">
                        <i class="fas fa-share plan-icon text-primary"></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.sentMessages') }}</h6>
                        <h2 class="mb-2">{{ $data->sentMessages }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="plan-card text-center">
                        <i class="fas fa-envelope plan-icon text-primary"></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.incomeMessages') }}</h6>
                        <h2 class="mb-2">{{ $data->incomingMessages }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="plan-card text-center">
                        <i class="fas fa-address-book plan-icon text-primary"></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.contacts') }}</h6>
                        <h2 class="mb-2">{{ $data->contactsCount }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
</div> <!-- container -->
@endsection

@section('modals')
@include('tenant.Partials.screen_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('components/subscription.js') }}" type="text/javascript"></script>
@endsection
