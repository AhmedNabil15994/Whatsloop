{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('libs/datatables.net-select-bs4/css//select.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">

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
        <div class="panel panel-primary tabs-style-2">
            <div class="panel-body tabs-menu-body main-content-body-right border">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab4">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mainCol">
                                                    <h3 class="card-title mb-7"> {{ trans('main.currentPackage') }}</h3> 
                                                    <div class="row">
                                                        <div class="col-6 text-left text-gray">{{ trans('main.packageName') }}</div>
                                                        <div class="col-6 text-right text-gray">{{ trans('main.substatus') }}</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-6 text-left">
                                                            <span class="btn btn-outline-primary">{{ $data->subscription->package_name }}</span> 
                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <span class="btn btn-{{ $data->subscription->channelStatus == 1 ? 'success' : 'danger' }}">{{ $data->subscription->channelStatus == 1 ? trans('main.active') : trans('main.notActive') }}</span> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mainCol">
                                                    <h3 class="card-title mb-4"> {{ trans('main.nextMillestone') }}</h3> 
                                                    <div class="row mb-4">
                                                        <div class="col-4 info">
                                                            <span class="text-gray">{{ $data->subscription->end_date }}</span>
                                                        </div>
                                                        <div class="col-3 noPadd">
                                                            <span class="btn btn-outline-primary">{{ $data->subscription->leftDays }} {{ trans('main.leftDays') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4 info">
                                                            <span class="text-gray">{{ trans('main.substartDate') }}</span>
                                                        </div>
                                                        <div class="col-2">
                                                            <span class="btn btn-primary">{{ $data->subscription->start_date }}</span>
                                                        </div>
                                                        <div class="col-4 info">
                                                            <span class="text-gray">{{ trans('main.subendDate') }}</span>
                                                        </div>
                                                        <div class="col-2">
                                                            <span class="btn btn-primary">{{ $data->subscription->end_date }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header pb-0 pd-t-25"> 
                                        <div class="row"> 
                                            <div class="col-6">
                                                <h3 class="card-title mb-0"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ trans('main.invoices') }}</h3> 
                                            </div>
                                            <div class="col-6 text-right">
                                                @if(!isset($data->dis) || $data->dis != true)
                                                <div class="card-options ml-auto"> 
                                                    {{-- <div class="btn-group ml-5 mb-0"> 
                                                        <a class="btn-link option-dots" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" href="#">
                                                            <i class="fe fe-more-vertical tx-gray-500"></i>
                                                        </a> 
                                                        <div class="dropdown-menu shadow" style=""> 
                                                            @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                                                            <a class="dropdown-item text-left" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}">
                                                                @if($data->designElems['mainData']['url'] == 'groupMsgs')
                                                                <i class="fa fa-send mr-2 ml-2"></i> {{ trans('main.send') }}
                                                                @else
                                                                <i class="fe fe-plus mr-2 ml-2"></i> {{ trans('main.add') }}
                                                                @endif
                                                            </a>
                                                            @endif

                                                            @if(\Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) && $data->designElems['mainData']['url'] != 'groupMsgs')
                                                                <a href="#" class="dropdown-item text-left quickEdit">
                                                                    <i class="fe fe-edit mr-2 ml-2"></i> {{ trans('main.fastEdit') }}
                                                                </a>
                                                            @endif

                                                            <a href="#" class="dropdown-item text-left search-mode">
                                                                <i class="fa fa-question mr-2 ml-2"></i> {{ trans('main.advancedSearchTip') }}
                                                            </a>
                                                        </div> 
                                                    </div>  --}}
                                                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                                                    <a class="btn btn-primary btn-icon" data-toggle="tooltip" data-original-title=" {{ $data->designElems['mainData']['url'] == 'groupMsgs' ? trans('main.send') : trans('main.add')  }}" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}">
                                                        @if($data->designElems['mainData']['url'] == 'groupMsgs')
                                                        <i class="typcn typcn-location-arrow"></i>
                                                        @else
                                                        <i class="typcn typcn-document-add"></i>
                                                        @endif
                                                    </a>
                                                    @endif

                                                    @if(\Helper::checkRules('edit-'.$data->designElems['mainData']['nameOne']) && $data->designElems['mainData']['url'] != 'groupMsgs')
                                                        <a href="#" class="btn btn-success btn-icon quickEdit" data-toggle="tooltip" data-original-title="{{ trans('main.fastEdit') }}">
                                                            <i class="typcn typcn-edit"></i>
                                                        </a>
                                                    @endif

                                                    <a href="#" class="btn label label-light-warning btn-icon search-mode" data-toggle="tooltip" data-original-title="{{ trans('main.advancedSearchTip') }}">
                                                        <i class="si si-info pd-t-10"></i>
                                                    </a>
                                                </div> 
                                                @endif
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="card-body">
                                      
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
                                                                    <option value="{{ $group->id }}">{{ $group->title }}</option>
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
                                        <table class="table table-striped  dt-responsive nowrap w-100" id="kt_datatable">
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
                    </div>
                </div>
            </div>
        </div>
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
