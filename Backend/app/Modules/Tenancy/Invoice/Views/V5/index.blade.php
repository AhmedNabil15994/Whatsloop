{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('V5/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('V5/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('V5/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('V5/libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
@endsection

@section('content')
<!-- Start Content-->

    <!-- end page title --> 
    @if(!isset($data->dis) || $data->dis != true)
    <input type="hidden" name="data-area" value="{{ \Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) }}">
    <input type="hidden" name="data-cols" value="{{ \Helper::checkRules('delete-'.$data->designElems['mainData']['nameOne']) }}">
    @endif
    <input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">

    @if($data->designElems['mainData']['url'] == 'bots')
    <input type="hidden" name="data-tabs" value="{{ \Helper::checkRules('copy-'.$data->designElems['mainData']['nameOne']) }}">
    @endif

    @if($data->designElems['mainData']['url'] == 'groupMsgs' || $data->designElems['mainData']['url'] == 'tickets' || $data->designElems['mainData']['url'] == 'invoices')
    <input type="hidden" name="data-tab" value="{{ \Helper::checkRules('view-'.$data->designElems['mainData']['nameOne']) }}">
    @endif

    @if($data->designElems['mainData']['url'] == 'groupNumbers')
    <input type="hidden" name="data-tests" value="{{ \Helper::checkRules('export-contacts') }}">
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="transmitters bill">
                <div class="content">
                    <h2 class="titleBills">{{ trans('main.currentPackage') }}</h2>
                    <div class="contentBill">
                        <h2 class="title">{{ trans('main.currentPackage') }}</h2>
                        <div class="desc">{{ $data->subscription->package_name }}</div>
                        <h3 class="stat">{{ trans('main.substatus') }} <span>{{ $data->subscription->channelStatus == 1 ? trans('main.active') : trans('main.notActive') }}</span></h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="transmitters bill">
                <div class="content">
                    <h2 class="titleBills">{{ trans('main.nextMillestone') }}</h2>
                    <div class="dateFixed">
                        <span>{{ $data->subscription->end_date }}</span>
                        <div class="days">{{ $data->subscription->leftDays }} {{ trans('main.leftDays') }}</div>
                    </div>
                    <div class="contentBill clearfix">
                        <div class="dateStart">
                            {{ trans('main.substartDate') }}
                            <span>{{ $data->subscription->start_date }}</span>
                        </div>
                        <div class="dateStart">
                            {{ trans('main.subendDate') }}
                            <span>{{ $data->subscription->end_date }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="transmitters bill">
        <div class="content">
            <h2 class="titleBills">{{ trans('main.invoices') }}</h2>
            <form class="searchForm"  method="get" action="{{ URL::current() }}">
                <input type="text" placeholder=" {{ trans('main.advancedSearch') }} " />
                <button class="fa fa-search"></button>
            </form>

            <div class="overflowTable">
                <table class="tableBills table table-striped  dt-responsive nowrap w-100" id="kt_datatable">
                    <thead>
                        <tr>
                            @foreach($data->designElems['tableData'] as $one)
                            <th>{{ $one['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('modals')
@include('tenant.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('V5/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script>
<script src="{{ asset('V5/libs/datatables.net-select/js/dataTables.select.min.js') }}"></script>
<script src="{{ asset('V5/libs/pdfmake/build/pdfmake.min.js') }}"></script>
<script src="{{ asset('V5/libs/pdfmake/build/vfs_fonts.js') }}"></script>
<script src="{{ asset('V5/js/colvis.min.js') }}"></script>
<script src="{{ asset('V5/components/datatables.js')}}"></script>           
@endsection
