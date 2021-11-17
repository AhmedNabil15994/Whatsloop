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

@if(!isset($data->dis) || $data->dis != true)
<input type="hidden" name="data-area" value="{{ \Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) }}">
<input type="hidden" name="data-cols" value="{{ \Helper::checkRules('delete-'.$data->designElems['mainData']['nameOne']) }}">
@endif
<input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">

@if($data->designElems['mainData']['url'] == 'bots')
<input type="hidden" name="data-tabs" value="{{ \Helper::checkRules('copy-'.$data->designElems['mainData']['nameOne']) }}">
@endif

@if($data->designElems['mainData']['url'] == 'tickets')
<input type="hidden" name="tenant" value="1">
@endif

@if($data->designElems['mainData']['url'] == 'groupMsgs' || $data->designElems['mainData']['url'] == 'tickets' || $data->designElems['mainData']['url'] == 'invoices')
<input type="hidden" name="data-tab" value="{{ \Helper::checkRules('view-'.$data->designElems['mainData']['nameOne']) }}">
@endif

@if($data->designElems['mainData']['url'] == 'groupNumbers')
<input type="hidden" name="data-tests" value="{{ \Helper::checkRules('export-contacts') }}">
@endif

<div class="transmitters bill">
    <div class="content transmitterHead">
        <div class="row">
            <div class="col-md-6">
                <h2 class="titleBills">{{ $data->designElems['mainData']['title'] }}</h2>
            </div>
            <div class="col-md-6 text-right">
                @if(!isset($data->dis) || $data->dis != true)
                <div class="left clearfix"> 
                    <a href="#" class="icon changeDesign"><i class="flaticon-list"></i></a>

                    <a href="#" class="icon iconAdditions btn-icon search-mode" data-toggle="tooltip" data-original-title="{{ trans('main.advancedSearchTip') }}"><i class="flaticon-info"></i></a>

                    @if(\Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) && $data->designElems['mainData']['url'] != 'groupMsgs')
                        <a href="#" class="edit quickEdit" data-toggle="tooltip" data-original-title="{{ trans('main.fastEdit') }}">{{ trans('main.fastEdit') }} <i class="flaticon-pencil"></i></a>
                    @endif

                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="edit" data-toggle="tooltip" data-original-title=" {{ $data->designElems['mainData']['url'] == 'groupMsgs' ? trans('main.send') : trans('main.add')  }}" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}">
                        @if($data->designElems['mainData']['url'] == 'groupMsgs')
                        <i class="si si-cursor"></i> {{ $data->designElems['mainData']['addOne'] }}  
                        @else
                        <i class="flaticon-add"></i> {{ $data->designElems['mainData']['addOne'] }}  
                        @endif
                    </a>
                    @endif
                </div> 
                @endif
            </div>
        </div>
        <form class="searchForm"  method="get" action="{{ URL::current() }}">
            <input type="text" placeholder=" {{ trans('main.advancedSearch') }} " />
            <button class="fa fa-search"></button>
        </form>
        <div class="sa cl">
            <div class="clearfix">
                <div class="views">
                    <span>{{ trans('main.showing') }}</span>
                    <input type="number" name="records" value="{{ Request::has('recordNumber') ? Request::get('recordNumber') : '15' }}">
                    <span>{{ trans('main.records2') }}</span>
                </div>
                <form class="searchTable">
                    <input type="text" name="keyword" value="{{ Request::has('keyword') ? Request::get('keyword') : '' }}" placeholder="{{ trans('main.search') }}...">
                    <span>{{ trans('main.search') }}</span>
                </form>
            </div>
            <div class="numbers">
                @foreach($data->data as $customerKey => $customer)
                <div class="numbStyl clearfix">
                    <i class="icon color{{ $customerKey % 8 }}">
                        <svg id="_006-user-1" data-name="006-user-1" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                          <g id="Group_1659" data-name="Group 1659">
                            <g id="Group_1658" data-name="Group 1658">
                              <path id="Path_1197" data-name="Path 1197" d="M18,0A18,18,0,1,0,36,18,18.02,18.02,0,0,0,18,0Zm0,33.15A15.15,15.15,0,1,1,33.15,18,15.168,15.168,0,0,1,18,33.15Z" fill="#fff"/>
                            </g>
                          </g>
                          <g id="Group_1661" data-name="Group 1661" transform="translate(4.664 21.194)">
                            <g id="Group_1660" data-name="Group 1660">
                              <path id="Path_1198" data-name="Path 1198" d="M79.666,301.425c-5.062,0-9.8,2.539-13.336,7.147l2.261,1.734c2.986-3.89,6.919-6.032,11.075-6.032s8.09,2.142,11.075,6.032L93,308.572C89.465,303.964,84.728,301.425,79.666,301.425Z" transform="translate(-66.33 -301.425)" fill="#fff"/>
                            </g>
                          </g>
                          <g id="Group_1663" data-name="Group 1663" transform="translate(10.591 6.459)">
                            <g id="Group_1662" data-name="Group 1662">
                              <path id="Path_1199" data-name="Path 1199" d="M158.037,91.863a7.457,7.457,0,1,0,7.409,7.457A7.441,7.441,0,0,0,158.037,91.863Zm0,12.063a4.607,4.607,0,1,1,4.559-4.607A4.588,4.588,0,0,1,158.037,103.926Z" transform="translate(-150.628 -91.863)" fill="#fff"/>
                            </g>
                          </g>
                        </svg>
                    </i>
                    <a class="num" href="#">{{ ucwords($customer->name) }}</a>
                    <ul class="listNumbs">
                        <li>
                            <a href="#"><i class="flaticon-phone-call"></i></a>
                            <div class="adding-card">
                                <div class="card-body">                            
                                    <span class="tooltip"><i class="flaticon-phone-call"></i> {{ $customer->phone }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#"><i class="flaticon-email"></i></a>
                            <div class="adding-card">
                                <div class="card-body">                            
                                    <span class="tooltip"><i class="flaticon-email"></i> {{ $customer->email }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#"><i class="flaticon-users"></i></a>
                            <div class="adding-card">
                                <div class="card-body">                            
                                    <span class="tooltip"><i class="flaticon-users"></i> {{ $customer->name }}</span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="#"><i class="flaticon-map"></i></a>
                            <div class="adding-card">
                                <div class="card-body">                            
                                    <span class="tooltip"><i class="flaticon-map"></i> {{ $customer->country . ($customer->city != '' && $customer->country != '' ? " | "  : '') . $customer->city }}</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                @endforeach
                @include('tenant.Partials.pagination')
            </div>
        </div>
        <div class="sa hidden">
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
<script src="{{ asset('V5/components/ajaxSearch.js') }}" type="text/javascript"></script>
@endsection
