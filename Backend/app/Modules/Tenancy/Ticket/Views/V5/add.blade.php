{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
    .supportForm{
        padding: 20px;
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="form">
            <div class="row">
                <div class="col-xs-12">
                    <h4 class="title"><i class="fa fa-plus"></i> {{ trans('main.add') . ' '. trans('main.ticket') }}</h4>
                </div>
            </div>
            <div class="supportForm">
                <div class="card-body">
                    
                    <form class="form-horizontal supportForm" method="POST" enctype="multipart/form-data" action="{{ URL::to('/tickets/create') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <label class="titleLabel">{{ trans('main.department') }}:</label>
                            </div>
                            <div class="col-md-8">
                                <div class="selectStyle">
                                    <select data-toggle="select2" name="department_id">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($data->departments as $department)
                                        <option value="{{ $department->id }}" {{ $department->id == old('department_id') ? 'selected' : '' }}>{{ $department->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="titleLabel">{{ trans('main.subject') }}:</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" value="{{ old('subject') }}" name="subject" id="inputEmail3" placeholder="{{ trans('main.subject') }}" />
                                <input type="hidden" name="status" value="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="titleLabel">{{ trans('main.messageContent') }}:</label>
                            </div>
                            <div class="col-md-8">
                                <textarea  name="description" placeholder="{{ trans('main.messageContent') }}">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        @if(\Helper::checkRules('uploadImage-ticket'))
                        <div class="row">
                            <div class="col-md-4">
                                <label class="titleLabel">{{ trans('main.files') }} :</label>
                            </div>
                            <div class="col-md-8">
                                <div class="dropzone" id="kt_dropzone_1">
                                    <div class="fallback">
                                        <input name="files" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        <h3>{{ trans('main.dropzoneP') }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="nextPrev clearfix">
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                <button type="submit" name="Submit" class="btnNext AddBTN">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                        @endif
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection