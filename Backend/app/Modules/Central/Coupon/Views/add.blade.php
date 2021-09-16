{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.coupon_code') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('code') }}" name="code" id="inputEmail3" placeholder="{{ trans('main.coupon_code') }}">
                                <input type="hidden" name="status">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.discount_type') }} :</label>
                            <div class="col-9">
                                <select name="discount_type" class="form-control" data-toggle="select2">
                                    <option value="1" {{ old('discount_type') == 1 ? 'selected' : '' }}>{{ trans('main.discount_type_1') }}</option>
                                    <option value="2" {{ old('discount_type') == 2 ? 'selected' : '' }}>{{ trans('main.discount_type_2') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.discount_value') }} :</label>
                            <div class="col-9">
                                <input class="form-control" type="text" name="discount_value" value="{{ old('discount_value') }}" maxlength="" placeholder="">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.valid_type') }} :</label>
                            <div class="col-9">
                                <select name="valid_type" class="form-control" data-toggle="select2">
                                    <option value="1" {{ old('valid_type') == 1 ? 'selected' : '' }}>{{ trans('main.valid_type_1') }}</option>
                                    <option value="2" {{ old('valid_type') == 2 ? 'selected' : '' }}>{{ trans('main.valid_type_2') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.valid_value') }} :</label>
                            <div class="col-9">
                                <input class="form-control mb-5 datetimepicker-inputs" type="text" name="valid_value" value="{{ old('valid_value') }}">
                            </div>
                        </div>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
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