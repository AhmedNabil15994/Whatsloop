{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css">
<style>
    .note-editable{
        white-space: pre-line;
    }
</style>
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
                        <input type="hidden" class="form-control" value="{{ $data->data->status }}" name="status">
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.type') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="type" disabled readonly>
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->data->type == 1 ? 'selected' : '' }}>{{ trans('main.whatsAppMessage') }}</option>
                                    <option value="2" {{ $data->data->type == 2 ? 'selected' : '' }}>{{ trans('main.emailMessage') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.name_ar') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->title_ar }}" name="title_ar" id="inputEmail3" placeholder="{{ trans('main.name_ar') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.content_ar') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control summernote" name="content_ar" placeholder="{{ trans('main.content_ar') }}">{{ $data->data->content_ar }}</textarea>
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