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
    .myCard{
        border: 1px solid #CCC;
        border-radius: 5px;
    }
    .checked{
        border: 2px solid #1abc9c;
    }
    .myCard .card-body{
        position: relative;
    }
    .myCard .selected{
        position: absolute;
        left: 10px;
        top: 10px;
    }
    .myCard .selected.selected2{
        top: 30px;
    }
    html[dir="ltr"] .myCard .selected{
        left: auto;
        right: 10px;
    }
    .form-group.row{
        width: 100%;
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
                    <div class="row">
                        @foreach($data->addons as $addon)
                        <div class="col-4">
                            <form class="mainForm" action="{{ URL::to('/profile/addons/'.$addon->id)  }}" method="post">
                                @csrf
                                @php
                                    $found = [];
                                    if(in_array($addon->id, $data->data->addons != null ?  unserialize($data->data->addons) : [])){
                                        @$found = $data->userAddons2[$addon->id][0];
                                    }
                                @endphp
                                <div class="card myCard {{ in_array($addon->id, $data->userAddons) ? 'checked' : '' }}" data-toggle=".first">
                                    <div class="card-body">
                                        @if(in_array($addon->id, $data->userAddons))
                                        <span class="selected badge badge-dark">{{ trans('main.selected') }}</span>
                                        <span class="selected selected2 badge badge-dark"> {{ trans('main.end_date') }}: {{ $data->userAddons2[$addon->id][1] }}</span>
                                        @endif
                                        <h3 class="card-title {{ in_array($addon->id, $data->userAddons) ? 'mt-3' : '' }}">{{ $addon->title }}</h3>
                                        <div class="row mainCol">
                                            <div class="form-group row">
                                                <div class="col-4" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="monthly{{ $addon->id }}" class="monthly" {{ $found == 1 ? 'checked' : '' }} type="checkbox" name="addons[{{ $addon->id }}][1]">
                                                        <label for="monthly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                                <label class="col-8 col-form-label">{{ $addon->monthly_after_vat . ' ' . trans('main.sar') . ' '.trans('main.monthly') }}</label>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-4" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="yearly{{ $addon->id }}" class="yearly" {{ $found == 2 ? 'checked' : '' }} type="checkbox" name="addons[{{ $addon->id }}][2]">
                                                        <label for="yearly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                                <label class="col-8 col-form-label">{{ $addon->annual_after_vat . ' ' . trans('main.sar') . ' '.trans('main.yearly') }}</label>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div> 
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
<script src="{{ asset('components/addClient.js') }}" type="text/javascript"></script>
@endsection
