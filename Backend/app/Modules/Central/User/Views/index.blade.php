{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('tenancy/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('tenancy/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('tenancy/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('tenancy/assets/libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-{{ !isset($data->dis) || $data->dis != true ? '11' : '12' }}">
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
        @if(!isset($data->dis) || $data->dis != true)
        <div class="col-1 text-right">
            <div class="btn-group dropleft mb-3 mt-2">
                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog"></i>
                </button>
                <div class="dropdown-menu">
                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}">
                        @if($data->designElems['mainData']['url'] == 'groupMsgs')
                        <i class="mdi mdi-send"></i> {{ trans('main.send') }}
                        @else
                        <i class="fa fa-plus"></i> {{ trans('main.add') }}
                        @endif
                    </a>
                    @endif
                    @if(\Helper::checkRules('sort-'.$data->designElems['mainData']['nameOne']) && $data->designElems['mainData']['url'] != 'groupMsgs')
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/arrange') }}"><i class="fa fa-sort-numeric-up"></i> {{ trans('main.sort') }}</a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/charts') }}"><i class="fas fa-chart-bar"></i> {{ trans('main.charts') }}</a>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>     
    <!-- end page title --> 
    @if(!isset($data->dis) || $data->dis != true)
    <input type="hidden" name="data-area" value="{{ \Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) }}">
    <input type="hidden" name="data-cols" value="{{ \Helper::checkRules('delete-'.$data->designElems['mainData']['nameOne']) }}">
    @endif
    <input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">

    @if($data->designElems['mainData']['url'] == 'bots')
    <input type="hidden" name="data-tabs" value="{{ \Helper::checkRules('copy-'.$data->designElems['mainData']['nameOne']) }}">
    @endif

    @if($data->designElems['mainData']['url'] == 'groupMsgs' || $data->designElems['mainData']['url'] == 'tickets' || $data->designElems['mainData']['url'] == 'clients')
    <input type="hidden" name="data-tab" value="{{ \Helper::checkRules('view-'.$data->designElems['mainData']['nameOne']) }}">
    @endif

    @if($data->designElems['mainData']['url'] == 'groupNumbers')
    <input type="hidden" name="data-tests" value="{{ \Helper::checkRules('export-contacts') }}">
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                        @if(!isset($data->dis) || $data->dis != true)
                        <div class="col-6 text-right">
                            @if(\Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) && $data->designElems['mainData']['url'] != 'groupMsgs')
                            <a href="#" class="btn btn-outline-success quickEdit m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill " data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.fastEdit') }}">
                                <i class="fa fa-pencil-alt"></i>
                            </a>
                            @endif
                            <a href="#" class="btn ml-1 btn-outline-danger search-mode m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill " data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.advancedSearchTip') }}">
                                <i class="fa fa-question"></i>
                            </a>
                            <div class="btn-group ml-1 dropleft">
                                <button type="button" style="border-radius: 25px;" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ trans('main.actions') }} <i class="mdi mdi-chevron-down"></i></button>
                                <div class="dropdown-menu" style="">
                                    <h6 class="dropdown-header">{{ trans('main.exportOpts') }}</h6>
                                    <a class="dropdown-item print-but" href="#">Print</a>
                                    <a class="dropdown-item copy-but" href="#">Copy</a>
                                    <a class="dropdown-item excel-but" href="#">Excel</a>
                                    <a class="dropdown-item csv-but" href="#">CSV</a>
                                    <a class="dropdown-item pdf-but" href="#">PDF</a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <hr>
                    @if(!empty($data->designElems['searchData']))
                    <div class="accordion custom-accordion" id="custom-accordion-one">
                        <div class="card mb-3">
                            <div class="card-header" id="headingFive">
                                <h5 class="m-0 position-relative">
                                    <a class="custom-accordion-title text-reset collapsed d-block" data-toggle="collapse" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        <i class="fa fa-search"></i>
                                        {{ trans('main.advancedSearch') }} 
                                        <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#custom-accordion-one">
                                <div class="card-body m-form--fit">
                                    <div class="row">
                                        @foreach($data->designElems['searchData'] as $searchKey => $searchItem)
                                        @if(in_array($searchItem['type'],['email','text','number','password']))
                                        @if($searchKey == 'from' || $searchKey == 'to')
                                        <div class="col">
                                            <label class="col-form-label">{{ $searchItem['label'] }}:</label>
                                            <input type="{{ $searchItem['type'] }}" data-date-format="dd-mm-yyyy" data-date-autoclose="true" class="{{ $searchItem['class'] }}" name="{{ $searchKey }}" data-col-index="{{ $searchItem['index'] }}" id="{{ $searchItem['id'] }}" placeholder="{{ $searchItem['label'] }}">
                                        </div>
                                        @else
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <label class="col-form-label">{{ $searchItem['label'] }}:</label>
                                            <input type="{{ $searchItem['type'] }}" data-date-format="dd-mm-yyyy" data-date-autoclose="true" class="{{ $searchItem['class'] }}" placeholder="{{ $searchItem['label'] }}" name="{{ $searchKey }}" data-col-index="{{ $searchItem['index'] }}">
                                            <br>
                                        </div>
                                        @endif
                                        @endif
                                        @if($searchItem['type'] == 'select')
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <label class="col-form-label">{{ $searchItem['label'] }}:</label>
                                            <select class="selectpicker" data-style="btn-outline-myPR" name="{{ $searchKey }}">
                                                <option value=" ">{{ trans('main.choose') }}</option>
                                                @foreach($searchItem['options'] as $group)
                                                @php $group = (object) $group; @endphp
                                                <option value="{{ $group->id }}">{{ $group->title != '' ? $group->title : $group->name }}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    <div class="m-separator"></div>
                                    <div class="row">
                                        <div class="col-lg-12 text-right">
                                            <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" style="margin-top: 3px;" class="btn btn-outline-secondary" id="m_reset">
                                                <span>
                                                    <i class="fa fa-times"></i>
                                                    <span>{{ trans('main.cancel') }}</span>
                                                </span>
                                            </a>
                                            <div class="mb-0 text-center" style="display: inline-block;">
                                                <button class="ladda-button btn btn-info btn-block loginBut" id="m_search" dir="ltr" data-style="expand-right">
                                                    <span class="ladda-label"><i class="fa fa-search"></i> {{ trans('main.search') }}</span>
                                                    <span class="ladda-spinner"></span>
                                                    <div class="ladda-progress" style="width: 75px;"></div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!--begin: Datatable-->
                    <table class="table dt-responsive nowrap w-100" id="kt_datatable">
                        <thead>
                            <tr>
                                @foreach($data->designElems['tableData'] as $one)
                                <th>{{ $one['label'] }}</th>
                                @endforeach
                            </tr>
                        </thead>
                    </table>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('modals')
@include('central.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('tenancy/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('tenancy/assets/js/pages/crud/datatables/advanced/colvis.min.js') }}"></script>
<script src="{{ asset('tenancy/assets/components/datatables.js')}}"></script>           
@endsection
