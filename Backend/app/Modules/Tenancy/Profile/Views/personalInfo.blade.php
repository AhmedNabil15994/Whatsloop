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
                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/updatePersonalInfo') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.name') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="name" value="{{ $data->data->name }}" placeholder="{{ trans('main.name') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.company_name') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="company" value="{{ $data->data->company }}" placeholder="{{ trans('main.company_name') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.email') }} :</label>
                            <div class="col-9">
                                <input class="form-control" type="email" value="{{ $data->data->email }}" name="email" placeholder="{{ trans('main.email') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.phone') }} :</label>
                            <div class="col-9">
                                <input class="form-control teles" type="tel" value="{{ $data->data->phone }}" name="phone" placeholder="{{ trans('main.phone') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.domain') }} :</label>
                            <div class="col-9">
                                <input class="form-control" type="text" value="{{ $data->data->domain }}" name="domain" placeholder="{{ trans('main.domain') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.pinCode') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="pin_code" value="{{ $data->data->pin_code }}" placeholder="{{ trans('main.pinCode') }}">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.emergencyNumber') }}</label>
                            <div class="col-9">
                                <input type="tel" name="emergency_number" value="{{ $data->data->emergency_number }}" class="form-control teles">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-3 col-form-label">{{ trans('main.twoAuthFactor') }} :</label>
                            <div class="col-9">
                                <select name="two_auth" class="form-control">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="0" {{ $data->data->two_auth == 0 ? 'selected' : '' }}>{{ trans('main.no') }}</option>
                                    <option value="1" {{ $data->data->two_auth == 1 ? 'selected' : '' }}>{{ trans('main.yes') }}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.image') }} :</label>
                            <div class="col-9">
                                <div class="dropzone kt_dropzone_1" id="kt_dropzone_1">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 text-muted dripicons-cloud-upload"></i>
                                        <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
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
                                                <a class="DeletePhoto" data-type="url_image" data-area="{{ $data->data->id }}"><i class="fa fa-trash" data-name="{{ $data->data->photo_name }}" data-clname="Photo"></i> </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTN SaveBTNs">{{ trans('main.edit') }}</button>
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
