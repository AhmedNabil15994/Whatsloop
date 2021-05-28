{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-11">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>

        <div class="col-1 text-right">
            <div class="btn-group dropleft mb-3 mt-2">
                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog"></i>
                </button>
                <div class="dropdown-menu">
                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}"><i class="fa fa-plus"></i> {{ trans('main.add') }}</a>
                    @endif
                    @if(\Helper::checkRules('sort-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/arrange') }}"><i class="fa fa-sort-numeric-up"></i> {{ trans('main.sort') }}</a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/charts') }}"><i class="fas fa-chart-bar"></i> {{ trans('main.charts') }}</a>
                    @endif
                </div>
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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.client') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="user_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == old('user_id') ? 'selected' : '' }}>{{ '#'.$client->id .' - '. $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.department') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="department_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->departments as $department)
                                    <option value="{{ $department->id }}" {{ $department->id == old('department_id') ? 'selected' : '' }}>{{ $department->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.priority') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="priority_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ 1 == old('priority_id') ? 'selected' : '' }}>{{ trans('main.low') }}</option>
                                    <option value="2" {{ 2 == old('priority_id') ? 'selected' : '' }}>{{ trans('main.medium') }}</option>
                                    <option value="3" {{ 3 == old('priority_id') ? 'selected' : '' }}>{{ trans('main.high') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.subject') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('subject') }}" name="subject" id="inputEmail3" placeholder="{{ trans('main.subject') }}">
                                <input type="hidden" name="status" value="">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.messageContent') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control summernote" name="description" placeholder="{{ trans('main.messageContent') }}">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        @if(\Helper::checkRules('uploadImage-'.$data->designElems['mainData']['nameOne']))
                        <div class="form-group m-form__group row" style="padding-right: 0;padding-left: 0;padding-bottom: 10px;">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">{{ trans('main.files') }}:</label>
                            <div class="col-lg-12">
                                <div class="dropzone dropzone-multi" id="kt_dropzone_4">
                                    <div class="dropzone-panel mb-lg-0 mb-2">
                                        <a class="dropzone-select btn btn-primary  btn-sm">{{ trans('main.attachFiles') }}</a>
                                        {{-- <a class="dropzone-upload btn btn-success  btn-sm">{{ trans('main.uploadAll') }}</a> --}}
                                    </div>
                                    <div class="dropzone-items">
                                        <div class="dropzone-item" style="display:none">
                                            <div class="dropzone-file">
                                                <div class="dropzone-filename" title="some_image_file_name.jpg">
                                                    <span data-dz-name=""></span>
                                                    <strong>(
                                                    <span data-dz-size=""></span>)</strong>
                                                </div>
                                                <div class="dropzone-error" data-dz-errormessage=""></div>
                                            </div>
                                            <div class="dropzone-progress">
                                                <div class="progress">
                                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                </div>
                                            </div>
                                            <div class="dropzone-toolbar">
                                                <span class="dropzone-delete" data-dz-remove="">
                                                    <i class="fa fa-times"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <span class="form-text text-muted">{{ trans('main.maxFiles') }}</span>
                            </div>
                        </div>
                        @endif
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
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="fas fa-align-center"></i> {{ trans('main.lastActions') }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="timeline" dir="ltr">
                        @foreach($data->timelines as $key => $timeline)
                        <article class="timeline-item {{ $key%2 == 1 ? 'timeline-item-left' : '' }}">
                            <div class="timeline-desk">
                                <div class="timeline-box">
                                    <span class="arrow"></span>
                                    <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                                    <h4 class="mt-0 font-16">{{ $timeline->typeText }}</h4>
                                    <p class="text-muted mb-1"><i class="fa fa-clock"></i> <small>{{ $timeline->created_at2 }}</small></p>
                                    <p class="mb-0"><i class="fa fa-user-tie"></i> {{ $timeline->username }}</p>
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection