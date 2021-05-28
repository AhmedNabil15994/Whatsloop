{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['title'])

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
                        <li class="breadcrumb-item active">{{ $data->designElems['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['title'] }}</h3>
            </div>
        </div>

        <div class="col-1 text-right">
            <div class="btn-group dropleft mb-3 mt-2">
                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog"></i>
                </button>
                <div class="dropdown-menu">
                    @if(\Helper::checkRules('add-'.$data->designElems['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['url'].'/add') }}"><i class="fa fa-plus"></i> {{ trans('main.add') }}</a>
                    @endif
                    @if(\Helper::checkRules('sort-'.$data->designElems['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['url'].'/arrange') }}"><i class="fa fa-sort-numeric-up"></i> {{ trans('main.sort') }}</a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['url'].'/charts') }}"><i class="fas fa-chart-bar"></i> {{ trans('main.charts') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>  

    <div class="row">
        <div class="col-12">
            <!--begin::Card-->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="fas fa-sort-amount-up-alt"></i> {{ trans('main.sort').' '.$data->designElems['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="tab-content">
                        <div class="tab-pane active" id="AddTabs" role="tabpanel">
                            <div class="forms">  
                                <!--begin::Card-->
                                <div class="card card-custom gutter-b">
                                    <div class="card-body">
                                        <ul class="sortable-list tasklist list-unstyled" id="upcoming">
                                            @foreach($data->data as $item)
                                            <li id="task1" class="border-0 bg-primary"  data-id="{{ $item->id }}">
                                                <h5 class="mt-0"><a href="javascript: void(0);">{{ $item->sort }} - {{ $item->{ $data->designElems['sortName'] } }}</a></h5>
                                                <div class="clearfix"></div>
                                                <div class="row"></div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                <!--end::Card-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Card-->
        </div>
    </div>   
</div>
@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="{{ asset('tenancy/assets/libs/sortablejs/Sortable.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/components/sorting.js') }}"></script>
@endsection
