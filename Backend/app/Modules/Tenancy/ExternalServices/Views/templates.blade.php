{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css" media="screen">
    .badge.text-muted{
        background-color: #1d2128;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    
    <input type="hidden" name="data-area" value="{{ \Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) }}">
    <input type="hidden" name="data-cols" value="{{ \Helper::checkRules('delete-'.$data->designElems['mainData']['nameOne']) }}">
    <input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">

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
                    @if(!empty($data->designElems['searchData']))
                    <div class="accordion custom-accordion" id="custom-accordion-one">
                        <div class="card mb-3">
                            <div class="card-header" id="headingFive">
                                <h5 class="m-0 position-relative">
                                    <a class="custom-accordion-title text-reset collapsed d-block" data-toggle="collapse" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        <i class="fa fa-search"></i>
                                        {{ trans('main.advancedSearch') }} 
                                        <i class="mdi mdi-chevron-down accordion-arrow float-right"></i>
                                    </a>
                                </h5>
                            </div>
                            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#custom-accordion-one">
                                <form class="card-body m-form--fit" method="get" action="{{ URL::current() }}">
                                    <div class="row pt-2">
                                        @foreach($data->designElems['searchData'] as $searchKey => $searchItem)
                                        @if(in_array($searchItem['type'],['email','text','number','password']))
                                        @if($searchKey == 'from' || $searchKey == 'to')
                                        <div class="col">
                                            <label class="col-form-label">{{ $searchItem['label'] }}:</label>
                                            <input type="{{ $searchItem['type'] }}" data-date-format="dd-mm-yyyy" data-date-autoclose="true" class="{{ $searchItem['class'] }}" value="{{ Request::get($searchKey) }}" name="{{ $searchKey }}" id="{{ $searchItem['id'] }}" placeholder="{{ $searchItem['label'] }}">
                                        </div>
                                        @else
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <label class="col-form-label">{{ $searchItem['label'] }}:</label>
                                            <input type="{{ $searchItem['type'] }}" data-date-format="dd-mm-yyyy" data-date-autoclose="true" class="{{ $searchItem['class'] }}" value="{{ Request::get($searchKey) }}" placeholder="{{ $searchItem['label'] }}" name="{{ $searchKey }}">
                                            <br>
                                        </div>
                                        @endif
                                        @endif
                                        @if($searchItem['type'] == 'select')
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <label class="col-form-label">{{ $searchItem['label'] }}:</label>
                                            <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="{{ $searchKey }}">
                                                <option value=" ">{{ trans('main.choose') }}</option>
                                                @foreach($searchItem['options'] as $group)
                                                @php $group = (object) $group; @endphp
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                @endforeach
                                            </select>
                                            <br>
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    <div class="m-separator"></div>
                                    <div class="row mt-4">
                                        <div class="col-lg-12 text-right">
                                            <a href="{{ URL::current() }}" class="btn btn-light" id="m_reset">
                                                <span>
                                                    <i class="fa fa-times"></i>
                                                    <span>{{ trans('main.cancel') }}</span>
                                                </span>
                                            </a>
                                            <button class="btn btn-primary loginBut" id="m_search" dir="ltr" data-style="expand-right">
                                                <span class="ladda-label"><i class="fa fa-search"></i> {{ trans('main.search') }}</span>
                                                <span class="ladda-spinner"></span>
                                                <div class="ladda-progress" style="width: 75px;"></div>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!--begin: Datatable-->
                    <table class="table dt-responsive nowrap w-100" id="kt_datatable">
                        <thead>
                            <tr>
                                @foreach($data->designElems['tableData'] as $one)
                                <th class="text-center {{ $one['className'] }}" >{{ $one['label'] }}</th>
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
@include('tenant.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('js/pages/crud/datatables/advanced/colvis.min.js') }}"></script>
<script src="{{ asset('components/datatables.js')}}"></script>           
@endsection
