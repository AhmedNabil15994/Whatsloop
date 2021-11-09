{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('tenancy/assets/css/default-skin.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('tenancy/assets/css/photoswipe.css') }}">
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xs-12">
            <div class="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <form class="form-horizontal formPayment" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputEmail3" class="titleLabel">{{ trans('main.order_no') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="" readonly value="{{ $data->data->order_no }}" name="order_no" id="inputEmail3" placeholder="{{ trans('main.order_no') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.client') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="" readonly value="{{ $data->data->client }}" name="client" id="inputPassword3" placeholder="{{ trans('main.client') }}">
                                <input type="hidden" name="status" value="{{ $data->data->status }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.phone') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="" readonly value="{{ $data->data->phone }}" name="phone" id="inputPassword3" placeholder="{{ trans('main.phone') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.total') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="" readonly value="{{ $data->data->total }}" name="total" id="inputPassword3" placeholder="{{ trans('main.total') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.created_at') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="" readonly value="{{ $data->data->created_at }}" name="created_at" id="inputPassword3" placeholder="{{ trans('main.created_at') }}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputEmail3" class="titleLabel">{{ trans('main.status') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <select class="" data-toggle="select2" name="status">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->data->status == 1 ? 'selected' : '' }}>{{ trans('main.requestSent') }}</option>
                                    <option value="2" {{ $data->data->status == 2 ? 'selected' : '' }}>{{ trans('main.accept') }}</option>
                                    <option value="3" {{ $data->data->status == 3 ? 'selected' : '' }}>{{ trans('main.refuse') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.image') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="dropzone" id="kt_dropzone_11">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        {{-- <h3>{{ trans('main.dropzoneP') }}</h3> --}}
                                    </div>
                                    @if($data->data->photo != '')
                                    <div class="dz-preview dz-image-preview" id="my-preview">  
                                        <div class="dz-image">
                                            <img alt="image" src="{{ $data->data->photo }}">
                                        </div>  
                                        <div class="dz-details">
                                            <div class="dz-size">
                                                <span><strong>{{ $data->data->photo_size }}</strong></span>
                                            </div>
                                            <div class="dz-filename">
                                                <span data-dz-name="">{{ $data->data->photo_name }}</span>
                                            </div>
                                            <div class="PhotoBTNS">
                                                <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                   <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                        <a href="{{ $data->data->photo }}" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                        <img src="{{ $data->data->photo }}" itemprop="thumbnail" style="display: none;">
                                                    </figure>
                                                </div>              
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
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
@include('central.Partials.photoswipe_modal')
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('tenancy/assets/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/components/myPhotoSwipe.js') }}"></script>       
@endsection
