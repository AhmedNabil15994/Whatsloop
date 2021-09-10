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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postWebhookSetting') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                            <div class="col-9">
                                <div class="checkbox checkbox-success mb-2">
                                    <label class="ckbox prem">
                                        <input type="checkbox" name="webhook_on" {{ \App\Models\Variable::getVar('WEBHOOK_ON') == 1 ? 'checked' : '' }} >
                                        <span> </span>
                                    </label>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.webhookURL') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ \App\Models\Variable::getVar('WEBHOOK_URL') }}" name="webhook_url" placeholder="{{ trans('main.webhookURL') }}">
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9 text-right">
                                <a href="{{ URL::to('/dashboard') }}" type="reset" class="btn btn-danger Reset float-left">{{ trans('main.back') }}</a>
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
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
