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
                                <input type="hidden" name="status">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.color') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="color">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="primary" {{ old('color') == 'primary' ? 'selected' : '' }}>Primary</option>
                                    <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>Secondary</option>
                                    <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>Success</option>
                                    <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="info" {{ old('color') == 'info' ? 'selected' : '' }}>Ino</option>
                                    <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>Danger</option>
                                    <option value="light" {{ old('color') == 'light' ? 'selected' : '' }}>Light</option>
                                    <option value="dark" {{ old('color') == 'dark' ? 'selected' : '' }}>Dark</option>
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