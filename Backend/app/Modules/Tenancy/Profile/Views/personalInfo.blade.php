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
<div class="row">
    <div class="panel panel-primary tabs-style-2">
        <div class=" tab-menu-heading">
            <div class="tabs-menu1">
                <!-- Tabs -->
                <ul class="nav panel-tabs main-nav-line">
                    <li><a href="#tab4" class="nav-link active" data-toggle="tab"><i class="fa fa-user"></i> {{ trans('main.account_setting') }}</a></li>
                    <li><a href="#tab5" class="nav-link" data-toggle="tab"><i class="fas fa-user-lock"></i> {{ trans('main.changePassword') }}</a></li>
                    @if(\Helper::checkRules('paymentInfo,taxInfo'))
                    <li><a href="#tab6" class="nav-link" data-toggle="tab"><i class="mdi mdi-credit-card"></i> {{ trans('main.payment_setting') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('notifications'))
                    <li><a href="#tab7" class="nav-link" data-toggle="tab"><i class="fe fe-bell"></i> {{ trans('main.notifications') }}</a></li>
                    @endif
                    @if(\Helper::checkRules('offers'))
                    <li><a href="#tab8" class="nav-link" data-toggle="tab"><i class="fe fe-radio"></i> {{ trans('main.offers') }}</a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="panel-body tabs-menu-body main-content-body-right border">
            <div class="tab-content">
                <div class="tab-pane active" id="tab4">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
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
                                                <select name="two_auth" data-toggle="select2" class="form-control">
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
                                                        <i class="h1 si si-cloud-upload"></i>
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
                                            <div class="col-9 text-right">
                                                <button class="btn btn-success" type="submit">{{ trans('main.edit') }}</button>
                                                <a href="{{ URL::to('/profile') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab5">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postChangePassword') }}">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="password">{{ trans('auth.password') }}</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="form-control" name="password" placeholder="{{ trans('auth.passwordPlaceHolder') }}">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="password">{{ trans('auth.passwordConf') }}</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="form-control" name="password_confirmation" placeholder="{{ trans('auth.passwordConfPlaceHolder') }}">
                                            </div>
                                        </div>
                                        <hr class="mt-5">
                                        <div class="form-group justify-content-end row">
                                            <div class="col-9 text-right">
                                                <button class="btn btn-success" type="submit">{{ trans('main.edit') }}</button>
                                                <a href="{{ URL::to('/profile') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(\Helper::checkRules('paymentInfo,taxInfo'))
                <div class="tab-pane" id="tab6">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
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
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">{{ trans('main.tax_id') }} :</label>
                                            <div class="col-9">
                                                <input class="form-control" value="{{ $data->paymentInfo ? $data->paymentInfo->tax_id : '' }}" name="tax_id" placeholder="{{ trans('main.tax_id') }}">
                                            </div>
                                        </div> 
                                        <hr class="mt-5">
                                        <div class="form-group justify-content-end row">
                                            <div class="col-9 text-right">
                                                <button class="btn btn-success" type="submit">{{ trans('main.edit') }}</button>
                                                <a href="{{ URL::to('/profile') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(\Helper::checkRules('notifications'))
                <div class="tab-pane" id="tab7">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postNotifications') }}">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">{{ trans('main.notifications') }} :</label>
                                            <div class="col-9">
                                                <div class="checkbox checkbox-success mb-2">
                                                    <input id="checkbox3" type="checkbox" name="notifications" {{ $data->data->notifications == 1 ? 'checked' : '' }} >
                                                    <label for="checkbox3"></label>
                                                </div>
                                            </div>
                                        </div> 
                                        <hr class="mt-5">
                                        <div class="form-group justify-content-end row">
                                            <div class="col-9 text-right">
                                                <button class="btn btn-success" type="submit">{{ trans('main.edit') }}</button>
                                                <a href="{{ URL::to('/profile') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @if(\Helper::checkRules('offers'))
                <div class="tab-pane" id="tab8">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postOffers') }}">
                                        @csrf
                                        <div class="form-group row">
                                            <label class="col-3 col-form-label">{{ trans('main.offers') }} :</label>
                                            <div class="col-9">
                                                <div class="checkbox checkbox-success mb-2">
                                                    <input id="checkbox3" type="checkbox" name="offers" {{ $data->data->offers == 1 ? 'checked' : '' }} >
                                                    <label for="checkbox3"></label>
                                                </div>
                                            </div>
                                        </div> 
                                        <hr class="mt-5">
                                        <div class="form-group justify-content-end row">
                                            <div class="col-9 text-right">
                                                <button class="btn btn-success" type="submit">{{ trans('main.edit') }}</button>
                                                <a href="{{ URL::to('/profile') }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
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
