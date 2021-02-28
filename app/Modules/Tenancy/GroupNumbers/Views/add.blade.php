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
    </div>     
    <!-- end page title --> 
    <input type="hidden" name="modelProps" value="{{ json_encode($data->modelProps) }}">
    <form class="form-horizontal" method="POST" action="{{ URL::to('/addGroupNumbers/create') }}">
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                            </div>
                        </div>
                        <hr>
                            @csrf
                            <input type="hidden" name="status">
                            <div class="form-group row mb-3">
                                <label class="col-3 col-form-label">{{ trans('main.group') }} :</label>
                                <div class="col-9">
                                    <select class="selectpicker" data-style="btn-outline-myPR" name="group_id">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($data->groups as $group)
                                        <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->channel .' - '.$group->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group row mb-3">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachExcel') }} :</label>
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
                                    <p class="mt-2 example">{{ trans('main.excelExample') }} (<a target="_blank" href="{{ URL::to('/').'/uploads/ImportGroupNumbers.xlsx' }}">{{ trans('main.download') }}</a> )</p>
                                </div>
                            </div>
                            <div class="form-group mb-0 justify-content-end row">
                                <div class="col-9">
                                    <button type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['nameOne']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                                </div>
                            </div>
                        <!--end: Datatable-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="header-title"><i class="fas fa-align-center"></i> {{ trans('main.fileContent') }}</h4>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="sortable-list tasklist list-unstyled col">
                                <div class="row" id="colData">
                                    <p>{{ trans('main.noDataFound') }}</p>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div>
    </form>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.7.7/xlsx.core.min.js"></script>
<script src="{{ asset('components/addNumberToGroup.js') }}" type="text/javascript"></script>
@endsection