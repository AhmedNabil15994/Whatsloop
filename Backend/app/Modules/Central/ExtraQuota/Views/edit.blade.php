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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.extra_count') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->extra_count }}" name="extra_count" id="inputEmail3" placeholder="{{ trans('main.extra_count') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.extra_type') }} :</label>
                            <div class="col-9">
                                <select class="form-control select2" name="extra_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->data->extra_type == 1 ? 'selected' : '' }}>{{ trans('main.message') }}</option>
                                    <option value="2" {{ $data->data->extra_type == 2 ? 'selected' : '' }}>{{ trans('main.employee') }}</option>
                                    <option value="3" {{ $data->data->extra_type == 3 ? 'selected' : '' }}>{{ trans('main.gigaB') }}</option>
                                </select>
                                <input type="hidden" name="status" value="{{ $data->data->status }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.monthly_price') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->monthly_price != 0 ? $data->data->monthly_price : 0 }}" name="monthly_price" id="inputPassword3" placeholder="{{ trans('main.monthly_price') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.monthly_after_vat') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->monthly_after_vat != 0 ? $data->data->monthly_after_vat : 0 }}" name="monthly_after_vat" id="inputPassword3" placeholder="{{ trans('main.monthly_after_vat') }}">
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