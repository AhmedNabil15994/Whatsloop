{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['title'].' - الاحصائيات')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-10">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['title'] }}</h3>
            </div>
        </div>

        <div class="col-2 text-right">
            <form action="{{ URL::current() }}" class="chart-form" method="get" >
                <div class='input-group' id='kt_daterangepicker_6'>
                    <span class="my-title"> {{ \Request::has('to') ? date('M d',strtotime(Request::get('to'))).' - '  : 'Today :' }} </span>
                    <input type="hidden" name="from">
                    <input type="hidden" name="to">
                    <input type='text' class="form-control" readonly="readonly" placeholder="Select date range" value="{{ \Request::has('from') ? date('M d',strtotime(Request::get('from')))  : date('M d') }} " />
                    <div class="input-group-append">
                        <span class="input-group-text main">
                            <i class="fa fa-angle-down"></i>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div> 

    <input type="hidden" name="chartData1" value="{{ json_encode($data->chartData1) }}">
    <input type="hidden" name="chartData2" value="{{ json_encode($data->chartData2) }}">
    <input type="hidden" name="chartData3" value="{{ json_encode($data->chartData3) }}">
    <input type="hidden" name="chartData4" value="{{ json_encode($data->chartData4) }}">
    <input type="hidden" name="counts" value="{{ json_encode($data->counts) }}">

    <div class="card card-custom gutter-b">
        <div class="card-body">
            <h3 class="card-label">{{ trans('main.addNo') }} {{ $data->designElems['title'] }}</h3>
            <p class="label-desc">{{ trans('main.hereShowing') }} {{ trans('main.addNo') }} {{ $data->designElems['title'] }} {{ trans('main.inWebsite') }}</p>
            <div id="chart_3"></div>
        </div>
    </div>

    <div class="card card-custom gutter-b">
        <div class="card-body">
            <h3 class="card-label">{{ trans('main.editNo') }} {{ $data->designElems['title'] }}</h3>
            <p class="label-desc">{{ trans('main.hereShowing') }} {{ trans('main.editNo') }} {{ $data->designElems['title'] }} {{ trans('main.inWebsite') }}</p>
            <div id="chart_1"></div>
        </div>
    </div>

    <div class="card card-custom gutter-b">
        <div class="card-body">
            <h3 class="card-label">{{ trans('main.fastEditNo') }} {{ $data->designElems['title'] }}</h3>
            <p class="label-desc">{{ trans('main.hereShowing') }} {{ trans('main.fastEditNo') }} {{ $data->designElems['title'] }} {{ trans('main.inWebsite') }}</p>
            <div id="chart_2"></div>
        </div>
    </div>

    <div class="card card-custom gutter-b">
        <div class="card-body">
            <h3 class="card-label">{{ trans('main.deleteNo') }} {{ $data->designElems['title'] }}</h3>
            <p class="label-desc">{{ trans('main.hereShowing') }} {{ trans('main.deleteNo') }} {{ $data->designElems['title'] }} {{ trans('main.inWebsite') }}</p>
            <div id="chart_5"></div>
        </div>
    </div>

    <div class="card card-custom gutter-b">
        <div class="card-body">
            <h3 class="card-label">{{ trans('main.totalNo') }} {{ $data->designElems['title'] }}</h3>
            <p class="label-desc">{{ trans('main.hereShowing') }} {{ trans('main.totalNo') }} {{ $data->designElems['title'] }} {{ trans('main.inWebsite') }}</p>
            <div id="chart_13"></div>
        </div>
    </div>
<!--end::Cards-->
</div>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{ asset('tenancy/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('tenancy/assets//components/charts.js') }}"></script>
@endsection
