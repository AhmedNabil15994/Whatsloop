{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
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
                        <input type="hidden" name="status">
                        @foreach($data->designElems['modelData'] as $propKey => $propValue)
                        @if(in_array($propValue['type'], ['email','text','number','password']))
                        <div class="form-group row mb-3">
                            <label for="" class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <input class="{{ $propValue['class'] }}" {{ $propValue['specialAttr'] }} type="{{ $propValue['type'] }}" name="{{ $propKey }}" value="{{ old($propKey) }}" placeholder="{{ $propValue['label'] }}">
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
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <select class="selectpicker" data-style="btn-outline-myPR" name="{{ $propKey }}">
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

                        @if($data->designElems['mainData']['url'] == 'users')
                        <div class="form-group row mb-3">
                            <label for="inputPassword5" class="col-3 col-form-label">{{ trans('main.extraPermissions') }} :</label>
                            <div class="col-9">
                                <div class="sortable-list tasklist list-unstyled">
                                    <div class="row">
                                        @php $i =0; @endphp
                                        @foreach($data->permissions as $key => $permission)
                                            @if($i % 3 == 0)
                                            </div><div class="row">
                                            @endif
                                        <div class="col-xs-12 col-md-4 border-0 mb-3">
                                            <li>
                                                @foreach($permission as $one => $onePerm)
                                                <div class="checkbox checkbox-blue checkbox-single float-left">
                                                    <input type="checkbox" name="permission{{ $onePerm['perm_name'] }}">
                                                    <label></label>
                                                </div>
                                                <p>{{ $onePerm['perm_title'] }}</p>
                                                <div class="clearfix"></div>
                                                @if(count($permission) > 1)
                                                <hr>
                                                @endif
                                                @endforeach
                                            </li>
                                        </div>
                                        @php $i++; @endphp
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($propValue['type'] == 'image' && \Helper::checkRules('uploadImage-'.$data->designElems['mainData']['nameOne']))
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ $propValue['label'] }} :</label>
                            <div class="col-9">
                                <div class="dropzone" id="kt_dropzone_1">
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
                        @endif

                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['nameOne']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
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
</div>

@endsection