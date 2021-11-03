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

<input type="hidden" name="data-area" value="{{ \Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) }}">
<input type="hidden" name="data-cols" value="{{ \Helper::checkRules('delete-'.$data->designElems['mainData']['nameOne']) }}">
<input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">

<div class="transmitters bill">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                <h2 class="titleBills">{{ $data->designElems['mainData']['title'] }}</h2>
            </div>
            <div class="col-md-6 text-right">
                @if(!isset($data->dis) || $data->dis != true)
                <div class="left clearfix"> 
                    <a href="#" class="icon iconAdditions btn-icon search-mode" data-toggle="tooltip" data-original-title="{{ trans('main.advancedSearchTip') }}"><i class="flaticon-info"></i></a>

                    @if(\Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) && $data->designElems['mainData']['url'] != 'groupMsgs')
                        <a href="#" class="edit quickEdit" data-toggle="tooltip" data-original-title="{{ trans('main.fastEdit') }}">{{ trans('main.fastEdit') }} <i class="flaticon-pencil"></i></a>
                    @endif

                    {{-- @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="edit" data-toggle="tooltip" data-original-title=" {{  trans('main.add')  }}" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}">
                        <i class="flaticon-add"></i> {{ $data->designElems['mainData']['addOne'] }}  
                    </a>
                    @endif --}}
                </div> 
                @endif
            </div>
        </div>
        <form class="searchForm"  method="get" action="{{ URL::current() }}">
            <input type="text" placeholder=" {{ trans('main.advancedSearch') }} " />
            <button class="fa fa-search"></button>
        </form>
        <!--begin: Datatable-->
        <div class="overflowTable">
            <table class="tableBills table table-striped  dt-responsive nowrap w-100" id="kt_datatable">
                <thead>
                    <tr>
                        @foreach($data->designElems['tableData'] as $one)
                        <th>{{ $one['label'] }}</th>
                        @endforeach
                    </tr>
                </thead>
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
