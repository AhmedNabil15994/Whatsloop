{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css" media="screen">
    .card{
        background: #FFF;
    }
    .card-box{
        border-radius: 1rem;
        position: relative;
        min-height: 350px;
    }
    /*.accordion>.card{
        background: #FFF;
    }*/
  /*  .accordion>.card>.card-header{
        border: 1px solid #CCC;
        border-radius: 5px;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 0;
        background: transparent;
    }*/
    .divide-x{
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        border-top: 1px solid #e2e8f0;
    }
    .row.data{
        padding: 10px;
        background: #F1F5F9;
        padding-top: 25px;
        margin: 0;
    }
    .divide-x a{
        display: inline-block;
        width: 50%;
        padding-top: 1rem;
        padding-bottom: 1rem;
        color: #000;
        font-size: 16px;
    }
    .divide-x a:hover{
        background: rgba(148,163,184,0.12);
    }
    .divide-x a i{
        color: #94A3B8;
    }
    html[dir="ltr"] .divide-x a:first-of-type{
        border-right: 1px solid #e2e8f0;
    }
    html[dir="rtl"] .divide-x a:first-of-type{
        border-left: 1px solid #e2e8f0;
    }
    .user-email{
        margin-top: 12px;
    }
    .user-email{
        display: block;
        width: 100%;
    }
    .user-email p {
        display: inline-block;
        margin-bottom: 0;
    }
    .user-email p.info-title{
        font-size: 14px;
        font-weight: 600;
        color: #3b3f5c;
    }
    .user-email p.usr-email-addr{
        color: #888ea8;
        font-size: 13px;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item">{{ ucfirst($data->designElems['mainData']['service']) }}</li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>
    </div>     
    

    <input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                        <div class="col-6 text-right">
                            @if(count(Request::all()))
                            <a href="{{ URL::current() }}" class="btn ml-1 m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill btn-outline-success" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.back') }}">
                                <i class=" fas fa-undo-alt"></i>
                            </a>
                            @endif
                            <a href="#" class="btn ml-1 btn-outline-danger search-mode m-btn m-btn--icon m-btn--icon-only m-btn--custom m-btn--pill " data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.advancedSearchTip') }}">
                                <i class="fa fa-question"></i>
                            </a>
                            @if($data->dis != 1)
                            <a href="{{ URL::current().'?refresh=refresh' }}" class="btn mr-2 ml-2 btn-md btn-rounded btn-outline-dark" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.refresh') }}">
                                <i class=" fas fa-database"></i>
                                {{ trans('main.refresh') }}
                            </a>
                            @endif
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
                                <form class="card-body m-form--fit" method="get" action="{{ URL::current() }}">
                                    <div class="row">
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
                                            <select class="selectpicker" data-style="btn-outline-myPR" name="{{ $searchKey }}">
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
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row data">
                        @foreach($data->data as $customer)
                        <div class="col-lg-3">
                            <div class="text-center card-box">
                                <div class="pt-2 pb-2">
                                    <img src="{{ $customer->image }}" class="rounded-circle img-thumbnail avatar-xl" alt="profile-image">

                                    <h4 class="mt-2 mb-3"><a class="text-dark">{{ $customer->name }}</a></h4>
                                    <div class="user-email">
                                        <p class="info-title float-left">{{ trans('auth.phone') }}: </p>
                                        <p class="usr-email-addr float-right">{{ $customer->phone }}</p>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="user-email">
                                        <p class="info-title float-left">{{ trans('main.email') }}: </p>
                                        <p class="usr-email-addr float-right">{{ $customer->email }}</p>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="user-email">
                                        <p class="info-title float-left">{{ trans('main.address') }}: </p>
                                        <p class="usr-email-addr float-right">{{ $customer->country . ($customer->city != '' && $customer->country != '' ? " | "  : '') . $customer->city }}</p>
                                        <div class="clearfix"></div>
                                    </div>
                                    <!-- end row-->
                                    <div class="divide-x">
                                        <a class="float-left" href="mailto:{{ $customer->email }}">
                                            <i class="fa fa-envelope"></i>
                                            <span class="ml-2">{{ trans('main.emailV') }}</span>
                                        </a>
                                        <a class="float-right" href="tel:{{ $customer->phone }}">
                                            <i class="fa fa-phone"></i>
                                            <span class="ml-2">{{ trans('main.call') }}</span>
                                        </a>
                                    </div>
                                </div> <!-- end .padding -->
                            </div> <!-- end card-box-->
                        </div> <!-- end col -->
                        @endforeach
                    </div>
                    <!-- end row -->

                    @include('tenant.Partials.pagination')
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

@endsection
