{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .check-title{
        margin-left: 25px;
        margin-right: 25px;
        margin-top: 15px;
    }
    html[dir="ltr"] .form input[type="checkbox"]{
        left: 0;
    }
    html[dir="rtl"] .form input[type="checkbox"]{
        right: 20px;
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="form">
                <div class="row">
                    <div class="col-xs-12">
                        <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                    </div>
                </div>
                <div class="formPayment">
                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postWebhookSetting') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.status') }} :</label>                            
                            </div>
                            <div class="col-md-9">
                                <div class="checkbox checkbox-success mb-2">
                                    <label class="ckbox prem">
                                        <input type="checkbox" name="webhook_on" {{ \App\Models\Variable::getVar('WEBHOOK_ON') == 1 ? 'checked' : '' }} >
                                        <span> </span>
                                    </label>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.webhookURL') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="{{ \App\Models\Variable::getVar('WEBHOOK_URL') }}" name="webhook_url" placeholder="{{ trans('main.webhookURL') }}">
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/dashboard') }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                </div>
                                <div class="clearfix"></div>
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
