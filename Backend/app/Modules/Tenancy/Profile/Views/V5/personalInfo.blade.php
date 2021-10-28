{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
    .form .btnsTabs li{
        width: 200px;
    }
    .form textarea{
        height: 250px;
    }
    .form p.data{
        display: inherit;
    }
    .col-xs-12.text-right.actions .nextPrev{
        padding: 10px 30px 30px 30px;
    }
    .form .content{
        padding-bottom: 0;
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="formNumbers">
        <div class="row">
            <div class="col-md-12">
                <div class="form supportForm">
                    <ul class="btnsTabs" id="tabs1">
                        <li id="tab1" class="active">{{ trans('main.account_setting') }}</li>
                        <li id="tab2">{{ trans('main.changePassword') }}</li>
                        @if(\Helper::checkRules('paymentInfo,taxInfo'))
                        <li id="tab3">{{ trans('main.payment_setting') }}</li>
                        @endif
                    </ul>
                    <div class="tabs tabs1">
                        <div class="tab tab1">
                            <div class="content">
                              <div class="row">
                                <div class="col-xs-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/updatePersonalInfo') }}">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.name2') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input name="name" value="{{ $data->data->name }}" placeholder="{{ trans('main.name2') }}">
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.company_name') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input name="company" value="{{ $data->data->company }}" placeholder="{{ trans('main.company_name') }}">
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.email') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="email" value="{{ $data->data->email }}" name="email" placeholder="{{ trans('main.email') }}">
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.phone') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input class="teles" dir="ltr" type="tel" value="{{ $data->data->phone }}" name="phone" placeholder="{{ trans('main.phone') }}">
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.domain') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="text" value="{{ $data->data->domain }}" name="domain" placeholder="{{ trans('main.domain') }}">
                                                    </div>
                                                </div> 
                                                <div class="row d-hidden">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.pinCode') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input name="pin_code" value="{{ $data->data->pin_code }}" placeholder="{{ trans('main.pinCode') }}">
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.emergencyNumber') }}</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <input type="tel" name="emergency_number" value="{{ $data->data->emergency_number }}" class=" emergency_number" dir="ltr">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.twoAuthFactor') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
                                                        <select name="two_auth" data-toggle="select2">
                                                            <option value="">{{ trans('main.choose') }}</option>
                                                            <option value="0" {{ $data->data->two_auth == 0 ? 'selected' : '' }}>{{ trans('main.no') }}</option>
                                                            <option value="1" {{ $data->data->two_auth == 1 ? 'selected' : '' }}>{{ trans('main.yes') }}</option>
                                                        </select>
                                                    </div>
                                                </div> 
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="titleLabel">{{ trans('main.image') }} :</label>
                                                    </div>
                                                    <div class="col-md-9">
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
                                                <div class="row">
                                                    <div class="col-xs-12 text-right actions">
                                                        <div class="nextPrev clearfix ">
                                                            <a href="{{ URL::to('/dashboard') }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                                            <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>  
                            </div>
                        </div>
                        <div class="tab tab2">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postChangePassword') }}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('auth.password') }}</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="password" name="password" placeholder="{{ trans('auth.passwordPlaceHolder') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('auth.passwordConf') }}</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input type="password" name="password_confirmation" placeholder="{{ trans('auth.passwordConfPlaceHolder') }}">
                                                        </div>
                                                    </div>
                                                    <hr class="mt-5">
                                                    <div class="row">
                                                        <div class="col-xs-12 text-right actions">
                                                            <div class="nextPrev clearfix ">
                                                                <a href="{{ URL::to('/dashboard') }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                                                <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(\Helper::checkRules('paymentInfo,taxInfo'))
                        <div class="tab tab3">
                            <div class="content">
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/profile/postPaymentInfo') }}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.address') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input name="address" value="{{ $data->paymentInfo ? $data->paymentInfo->address : '' }}" placeholder="{{ trans('main.address') }}">
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.address') }} 2 :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input name="address2" value="{{ $data->paymentInfo ? $data->paymentInfo->address2 : '' }}" placeholder="{{ trans('main.address') }} 2">
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.city') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input name="city" value="{{ $data->paymentInfo ? $data->paymentInfo->city : '' }}" placeholder="{{ trans('main.city') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.region') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input name="region" value="{{ $data->paymentInfo ? $data->paymentInfo->region : '' }}" placeholder="{{ trans('main.region') }}">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.postal_code') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input name="postal_code" value="{{ $data->paymentInfo ? $data->paymentInfo->postal_code : '' }}" placeholder="{{ trans('main.postal_code') }}">
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.country') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input value="{{ $data->paymentInfo ? $data->paymentInfo->country : '' }}" name="country" placeholder="{{ trans('main.country') }}">
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.paymentMethod') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="selectStyle">
                                                                <select name="payment_method" data-toggle="select2">
                                                                    <option value="">{{ trans('main.choose') }}</option>
                                                                    <option value="1" {{ $data->paymentInfo && $data->paymentInfo->payment_method == 1 ? 'selected' : '' }}>{{ trans('main.mada') }}</option>
                                                                    <option value="2" {{ $data->paymentInfo && $data->paymentInfo->payment_method == 2 ? 'selected' : '' }}>{{ trans('main.visaMaster') }}</option>
                                                                    <option value="3" {{ $data->paymentInfo && $data->paymentInfo->payment_method == 3 ? 'selected' : '' }}>{{ trans('main.bankTransfer') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.currency') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <div class="selectStyle">
                                                                <select name="currency" data-toggle="select2">
                                                                    <option value="">{{ trans('main.choose') }}</option>
                                                                    <option value="1" {{ $data->paymentInfo && $data->paymentInfo->currency == 1 ? 'selected' : '' }}>{{ trans('main.sar') }}</option>
                                                                    <option value="2" {{ $data->paymentInfo && $data->paymentInfo->currency == 2 ? 'selected' : '' }}>{{ trans('main.usd') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <label class="titleLabel">{{ trans('main.tax_id') }} :</label>
                                                        </div>
                                                        <div class="col-md-9">
                                                            <input value="{{ $data->paymentInfo ? $data->paymentInfo->tax_id : '' }}" name="tax_id" placeholder="{{ trans('main.tax_id') }}">
                                                        </div>
                                                    </div> 
                                                    <hr class="mt-5">
                                                    <div class="row">
                                                        <div class="col-xs-12 text-right actions">
                                                            <div class="nextPrev clearfix ">
                                                                <a href="{{ URL::to('/dashboard') }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                                                <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                </form>
                                            </div>
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
    </div>
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
