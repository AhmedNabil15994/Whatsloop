{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css">
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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.name_ar') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->title_ar }}" name="title_ar" id="inputEmail3" placeholder="{{ trans('main.name_ar') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.name_en') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->title_en }}" name="title_en" id="inputPassword3" placeholder="{{ trans('main.name_en') }}">
                                <input type="hidden" name="status" value="{{ $data->data->status }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.descriptionAr') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control summernote" name="description_ar" placeholder="{{ trans('main.descriptionAr') }}">{{ $data->data->description_ar }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.descriptionEn') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control summernote" name="description_en" placeholder="{{ trans('main.descriptionEn') }}">{{ $data->data->description_en }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.monthly_after_vat') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->monthly_after_vat != 0 ? $data->data->monthly_after_vat : 0 }}" name="monthly_after_vat" id="inputPassword3" placeholder="{{ trans('main.monthly_after_vat') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.annual_after_vat') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->annual_after_vat != 0 ? $data->data->annual_after_vat : 0 }}" name="annual_after_vat" id="inputPassword3" placeholder="{{ trans('main.annual_after_vat') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.membership') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="membership_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->memberships as $membership)
                                    <option value="{{ $membership->id }}" {{ $data->data->membership_id == $membership->id ? 'selected' : '' }}>{{ $membership->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword5" class="col-3 col-form-label">{{ trans('main.addons') }} :</label>
                            <div class="col-9">
                                <div class="sortable-list tasklist list-unstyled">
                                    <div class="row">
                                        @foreach($data->addons as $key => $addon)
                                        <div class="col-xs-12 col-md-6 border-0">
                                            <div class="checkbox checkbox-blue checkbox-single float-left">
                                                <input type="checkbox" {{ in_array($addon->id, $data->data->addons) ? 'checked' : '' }} name="addons[]" value="{{ $addon->id }}">
                                                <label></label>
                                            </div>
                                            <p>{{ $addon->title }}</p>
                                            <div class="clearfix"></div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-info SelectAllCheckBox ml-2 mr-2">{{ trans('main.selectAll') }}</button>
                                            <button type="button" class="btn btn-danger UnSelectAllCheckBox">{{ trans('main.deselectAll') }}</button>
                                        </div>            
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
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

@section('topScripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
@endsection