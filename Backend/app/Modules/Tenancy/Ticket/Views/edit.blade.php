{{-- Extends layout --}}
@extends('tenant.Layouts.master')
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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="status">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ 1 == $data->data->status ? 'selected' : '' }}>{{ trans('main.open') }}</option>
                                    <option value="2" {{ 2 == $data->data->status ? 'selected' : '' }}>{{ trans('main.answered') }}</option>
                                    <option value="3" {{ 3 == $data->data->status ? 'selected' : '' }}>{{ trans('main.customerReply') }}</option>
                                    <option value="4" {{ 4 == $data->data->status ? 'selected' : '' }}>{{ trans('main.onHold') }}</option>
                                    <option value="5" {{ 5 == $data->data->status ? 'selected' : '' }}>{{ trans('main.inProgress') }}</option>
                                    <option value="6" {{ 6 == $data->data->status ? 'selected' : '' }}>{{ trans('main.closed') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.client') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="user_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == $data->data->user_id ? 'selected' : '' }}>{{ '#'.$client->id .' - '. $client->name }}</option>
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
                                    <option value="{{ $department->id }}" {{ $department->id == $data->data->department_id ? 'selected' : '' }}>{{ $department->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.subject') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->subject }}" name="subject" id="inputEmail3" placeholder="{{ trans('main.subject') }}">
                                <input type="hidden" name="status" value="{{ $data->data->status }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.messageContent') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control summernote" name="description" placeholder="{{ trans('main.messageContent') }}">{{ $data->data->description }}</textarea>
                            </div>
                        </div>
                        @if(\Helper::checkRules('uploadImage-'.$data->designElems['mainData']['nameOne']))
                        <div class="form-group row mb-2">
                            <label class="label label-danger label-pill label-inline mr-2" style="margin-bottom: 20px;">{{ trans('main.files') }}:</label>
                            <div class="col-lg-12">
                                <div class="dropzone dropzone-multi" id="kt_dropzone_5">
                                <div class="dropzone-panel mb-lg-0 mb-2">
                                    <a class="dropzone-select btn btn-primary font-weight-bold btn-sm">{{ trans('main.attachFiles') }}</a>
                                    {{-- <a class="dropzone-upload btn btn-success font-weight-bold btn-sm">{{ trans('main.uploadAll') }}</a> --}}
                                </div>
                                <div class="dropzone-items">
                                    @foreach($data->data->files as $oneFile)
                                    <div class="dropzone-item edited">
                                        <div class="dropzone-file">
                                            <div class="dropzone-filename" title="{{ $oneFile->photo_name }}">
                                                <span data-dz-name=""><a href="{{ $oneFile->photo }}" target="_blank">{{ $oneFile->photo_name }}</a></span>
                                                <strong>(<span data-dz-size="">{{ $oneFile->photo_size }}</span>)</strong>
                                            </div>
                                            <div class="dropzone-error" data-dz-errormessage=""></div>
                                        </div>
                                        <div class="dropzone-progress">
                                            <div class="progress">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuemin="100" aria-valuemax="100" aria-valuenow="100" data-dz-uploadprogress=""></div>
                                            </div>
                                        </div>
                                        <div class="dropzone-toolbar">
                                            <span class="dropzone-delete DeleteFiles" data-dz-remove="" data-area="{{ $data->data->id }}" data-name="{{ $oneFile->photo_name }}">
                                                <i class="fa fa-times"></i>
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <span class="form-text text-muted">{{ trans('main.maxFiles') }}</span>
                            </div>
                        </div>
                        @endif
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

@section('modals')
@include('tenant.Partials.photoswipe_modal')
@endsection


{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('js/photoswipe.min.js') }}"></script>
<script src="{{ asset('js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('components/myPhotoSwipe.js') }}"></script>      
@endsection
