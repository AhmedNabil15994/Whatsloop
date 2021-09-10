{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css">
    body{
        overflow-x: hidden;
    }
</style>
@endsection

{{-- Content --}}

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/create') }}">
        @csrf
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
                        
                        <input type="hidden" name="status">
                        @foreach($data->designElems['modelData'] as $propKey => $propValue)
                        @if(in_array($propValue['type'], ['email','text','number','password','tel']))
                        <div class="form-group row mb-3">
                            <label for="" class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <input class="{{ $propValue['class'] }}" {{ $propValue['specialAttr'] }} type="{{ $propValue['type'] }}" name="{{ $propKey }}" value="{{ old($propKey) }}" placeholder="{{ $propValue['label'] }}" {{ $propValue['type'] == 'tel' ? "dir=ltr" : '' }}>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'textarea')
                        <div class="form-group row mb-3">
                            <label for="" class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <textarea {{ $propValue['specialAttr'] }} name="{{ $propKey }}" class="{{ $propValue['class'] }}" placeholder="{{ $propValue['label'] }}">{{ old($propKey) }}</textarea>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'select')
                        {{-- {{ dd($propValue['options']) }} --}}

                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="{{ $propKey }}">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($propValue['options'] as $group)
                                    @php $group = (object) $group; @endphp
                                    <option value="{{ $group->id }}" {{ old($propKey) == $group->id ? 'selected' : '' }} {{ Session::has($propKey) && Session::get($propKey) == $group->id ? 'selected' : '' }}>{{ $group->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        @endif

                        @endforeach

                        @if($propValue['type'] == 'image' && \Helper::checkRules('uploadImage-'.$data->designElems['mainData']['nameOne']))
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <div class="dropzone" id="kt_dropzone_1">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        <h3>{{ trans('main.dropzoneP') }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="form-group justify-content-end row">
                            <div class="col-9 text-right">
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset float-left">{{ trans('main.back') }}</a>
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                        <!--end: Datatable-->
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> 
        @if($data->designElems['mainData']['url'] == 'users')
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <h4 class="header-title"> {{ trans('main.extraPermissions') }}</h4>
                            </div>
                        </div>
                        <hr class="mb-5">
                        <div class="row">
                            <div class="form-group row mb-3">
                                <div class="col-12">
                                    <div class="sortable-list tasklist list-unstyled">
                                        <div class="row">
                                            @foreach($data->permissions as $key => $permission)
                                            <div class="col-12 border-0 mb-3">
                                                <div class="card permission">
                                                    <div class="card-header">
                                                        <label class="ckbox prem">
                                                            <input type="checkbox" name="allPermission">
                                                            <span class="tx-bold">{{ trans('main.'.lcfirst(str_replace('Controllers','',$key))) }} </span>
                                                        </label>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            @foreach($permission as $one => $onePerm)
                                                            <div class="col-2 mb-2">
                                                                <label class="ckbox prem">
                                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}">
                                                                    <span> {{ $onePerm['perm_title'] }}</span>
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
        @endif
    </form>    
</div>

@endsection

@section('scripts')
<script src="{{ asset('components/phone.js') }}"></script>
@endsection
