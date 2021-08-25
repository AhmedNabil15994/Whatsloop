{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .check-title{
        margin-left: 25px;
        margin-right: 25px;
        margin-top: 15px;
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
    <!-- end page title --> 
    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postPaymentInfo') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.address') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="address" value="{{ $data->paymentInfo ? $data->paymentInfo->address : '' }}" placeholder="{{ trans('main.address') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.address') }} 2 :</label>
                            <div class="col-9">
                                <input class="form-control" name="address2" value="{{ $data->paymentInfo ? $data->paymentInfo->address2 : '' }}" placeholder="{{ trans('main.address') }} 2">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.city') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="city" value="{{ $data->paymentInfo ? $data->paymentInfo->city : '' }}" placeholder="{{ trans('main.city') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.region') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="region" value="{{ $data->paymentInfo ? $data->paymentInfo->region : '' }}" placeholder="{{ trans('main.region') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.postal_code') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="postal_code" value="{{ $data->paymentInfo ? $data->paymentInfo->postal_code : '' }}" placeholder="{{ trans('main.postal_code') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.country') }} :</label>
                            <div class="col-9">
                                <input class="form-control" value="{{ $data->paymentInfo ? $data->paymentInfo->country : '' }}" name="country" placeholder="{{ trans('main.country') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.paymentMethod') }} :</label>
                            <div class="col-9">
                                <select name="payment_method" data-toggle="select2" class="form-control">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->paymentInfo->payment_method == 1 ? 'selected' : '' }}>{{ trans('main.mada') }}</option>
                                    <option value="2" {{ $data->paymentInfo->payment_method == 2 ? 'selected' : '' }}>{{ trans('main.visaMaster') }}</option>
                                    <option value="3" {{ $data->paymentInfo->payment_method == 3 ? 'selected' : '' }}>{{ trans('main.bankTransfer') }}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.currency') }} :</label>
                            <div class="col-9">
                                <select name="currency" data-toggle="select2" class="form-control">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->paymentInfo->currency == 1 ? 'selected' : '' }}>{{ trans('main.sar') }}</option>
                                    <option value="2" {{ $data->paymentInfo->currency == 2 ? 'selected' : '' }}>{{ trans('main.usd') }}</option>
                                </select>
                            </div>
                        </div> 
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTN">{{ trans('main.edit') }}</button>
                                <a href="{{ URL::to('/profile') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
        <div class="col-4">
            <div class="card-box text-center">
                <img src="{{ $data->data->photo }}" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                <h4 class="mb-0">{{ $data->data->name }}</h4>
                <p class="text-muted">{{ $data->data->group }}</p>
                <div class="text-left mt-3">
                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.name') }} :</strong> <span class="ml-2">{{ $data->data->name }}</span></p>

                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.phone') }} :</strong><span class="ml-2">{{ $data->data->phone }}</span></p>

                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.email') }} :</strong> <span class="ml-2">{{ $data->data->email }}</span></p>

                    <p class="text-muted mb-2 font-13"><strong>{{ trans('main.company_name') }} :</strong><span class="ml-2">{{ $data->data->company }}</span></p>

                    <p class="text-muted mb-1 font-13"><strong>{{ trans('main.channel') }} :</strong> <span class="ml-2"># {{ $data->data->channelCodes }}</span></p>
                </div>
            </div>
        </div>
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('modals')
@include('tenant.Partials.photoswipe_modal')
@endsection


@section('scripts')
<script src="{{ asset('components/phone.js') }}"></script>
<script src="{{ asset('/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('/components/myPhotoSwipe.js') }}"></script>      
@endsection
