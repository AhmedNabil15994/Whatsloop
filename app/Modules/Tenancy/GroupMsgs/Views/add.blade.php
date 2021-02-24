{{-- Extends layout --}}
@extends('tenant.Layouts.master')
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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/groupMsgs/create') }}">
                        @csrf
                        <input type="hidden" name="status">
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.group') }} :</label>
                            <div class="col-9">
                                <select class="selectpicker" data-style="btn-outline-myPR" name="group_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->channel . ' - '.$group->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.message_type') }} :</label>
                            <div class="col-9">
                                <select class="selectpicker" data-style="btn-outline-myPR" name="message_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ old('message_type') == 1 ? 'selected' : '' }}>{{ trans('main.text') }}</option>
                                    <option value="2" {{ old('message_type') == 2 ? 'selected' : '' }}>{{ trans('main.photoOrFile') }}</option>
                                    <option value="4" {{ old('message_type') == 4 ? 'selected' : '' }}>{{ trans('main.sound') }}</option>
                                    <option value="5" {{ old('message_type') == 5 ? 'selected' : '' }}>{{ trans('main.link') }}</option>
                                    <option value="6" {{ old('message_type') == 6 ? 'selected' : '' }}>{{ trans('main.whatsappNos') }}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="reply" data-id="1">
                            <div class="form-group row mb-3 hidden">
                                <label class="col-3 col-form-label">{{ trans('main.message_content') }} :</label>
                                <div class="col-9">
                                    <textarea name="messageText" class="form-control" placeholder="{{ trans('main.message_content') }}">{{ old('messageText') }}</textarea>
                                </div>
                            </div> 
                        </div>
                        <div class="reply" data-id="2">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.textWithPhoto') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('message') }}" name="message" placeholder="{{ trans('main.textWithPhoto') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachFile') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="3">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachFile') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                    <div class="d-none" id="uploadPreviewTemplate">
                                        <div class="card mt-1 mb-0 shadow-none border">
                                            <div class="p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <img data-dz-thumbnail="" src="#" class="avatar-sm rounded bg-light" alt="">
                                                    </div>
                                                    <div class="col pl-0">
                                                        <a href="javascript:void(0);" class="text-muted font-weight-bold" data-dz-name=""></a>
                                                        <p class="mb-0" data-dz-size=""></p>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- Button -->
                                                        <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove="">
                                                            <i class="dripicons-cross"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="4">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.url') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('https_url') }}" name="https_url" placeholder="{{ trans('main.url') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.urlTitle') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('url_title') }}" name="url_title" placeholder="{{ trans('main.urlTitle') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.urlDesc') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('url_desc') }}" name="url_desc" placeholder="{{ trans('main.urlDesc') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.urlImage') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="5">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.whatsappNo') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('whatsapp_no') }}" name="whatsapp_no" placeholder="{{ trans('main.whatsappNo') }}">
                                </div>
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                <button name="Submit" type="submit" class="btn btn-primary AddBTN" id="SaveBTN">{{ trans('main.draft') }}</button>
                                <button type="reset" class="btn btn-danger Reset">{{ trans('main.clearAll') }}</button>
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

@section('topScripts')
<script src="{{ asset('libs/summernote/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('components/addMsg.js') }}"></script>
@endsection