{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

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
                    <form class="formPayment" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="inputEmail3" class="titleLabel">{{ trans('main.coupon_code') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="" value="{{ old('code') }}" name="code" id="inputEmail3" placeholder="{{ trans('main.coupon_code') }}">
                                <input type="hidden" name="status">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.discount_type') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <select name="discount_type" class="" data-toggle="select2">
                                    <option value="1" {{ old('discount_type') == 1 ? 'selected' : '' }}>{{ trans('main.discount_type_1') }}</option>
                                    <option value="2" {{ old('discount_type') == 2 ? 'selected' : '' }}>{{ trans('main.discount_type_2') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.discount_value') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input class="" type="text" name="discount_value" value="{{ old('discount_value') }}" maxlength="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.valid_type') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <select name="valid_type" class="" data-toggle="select2">
                                    <option value="1" {{ old('valid_type') == 1 ? 'selected' : '' }}>{{ trans('main.valid_type_1') }}</option>
                                    <option value="2" {{ old('valid_type') == 2 ? 'selected' : '' }}>{{ trans('main.valid_type_2') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.valid_value') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input class=" mb-5 datetimepicker-inputs" type="text" name="valid_value" value="{{ old('valid_value') }}">
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
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