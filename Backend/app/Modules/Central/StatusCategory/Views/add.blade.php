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
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.name_ar') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('title_ar') }}" name="title_ar" id="inputEmail3" placeholder="{{ trans('main.name_ar') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.name_en') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('title_en') }}" name="title_en" id="inputPassword3" placeholder="{{ trans('main.name_en') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.descriptionAr') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control" name="description_ar" placeholder="{{ trans('main.descriptionAr') }}">{{ old('description_ar') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.descriptionEn') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control" name="description_en" placeholder="{{ trans('main.descriptionEn') }}">{{ old('description_en') }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="status">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>{{ trans('main.active') }}</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{ trans('main.notActive') }}</option>
                                </select>
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