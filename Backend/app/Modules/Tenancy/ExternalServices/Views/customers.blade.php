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
        background: #f4f5fd;
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
                            <a href="{{ URL::current() }}" class="btn btn-success mt-4 mr-2" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.back') }}">
                                <i class=" fas fa-undo-alt"></i>
                                {{ trans('main.back') }}
                            </a>
                            @endif
                            @if($data->dis != 1)
                            <a href="{{ URL::current().'?refresh=refresh' }}" class="btn btn-secondary mt-4" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.refresh') }}">
                                <i class=" fas fa-database"></i>
                                {{ trans('main.refresh') }}
                            </a>
                            @endif
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
                                        <div class="col-lg-12 text-left">
                                            <button class="btn btn-primary loginBut" id="m_search" dir="ltr" data-style="expand-right">
                                                <span class="ladda-label"><i class="fa fa-search"></i> {{ trans('main.search') }}</span>
                                                <span class="ladda-spinner"></span>
                                                <div class="ladda-progress" style="width: 75px;"></div>
                                            </button>
                                            <a href="{{ URL::current() }}" class="btn btn-light" id="m_reset">
                                                <span>
                                                    <i class="fa fa-times"></i>
                                                    <span>{{ trans('main.cancel') }}</span>
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row mb-4 extraSearch">
                        <div class="col-6 text-left">
                            <div class="row">
                                <div class="col-1 pt-2">
                                    <label class="text-muted">{{ trans('main.showing') }}</label>
                                </div>
                                <div class="col-9">
                                <select class="form-control float-left" name="records">
                                        <option value="15">15</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                        <option value="1000">1000</option>
                                        <option value="all">{{ trans('main.all') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <div class="row">
                                <div class="col-9 pt-2">
                                    <label class="text-muted">{{ trans('main.keyword') }}</label>
                                </div>
                                <div class="col-3">
                                <input class="form-control float-left" type="text" name="keyword" placeholder="{{ trans('main.search') }}" name="keyword">
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="row data">
                        @foreach($data->data as $customerKey => $customer)
                        <div class="col-sm-12 col-lg-4"> 
                            <div class="card custom-card"> 
                                <div class="card-body text-center"> 
                                    <div class="user-lock text-center"> 
                                        @php 
                                            $colorsArr = ['primary','secondary','info','success','warning','danger','pink'];
                                            $fullName = $customer->name;
                                            $names = explode(' ',$customer->name,2);
                                            $fName = ucfirst($names[0]);
                                            $lName = isset($names[1]) ?  ucfirst($names[1]) : ucfirst($names[0]);

                                            $abbreviation = mb_substr($fName,0,1,'utf-8') .mb_substr($lName,0,1,'utf-8');;
                                        @endphp


                                        <div class="avatar avatar-lg d-none d-sm-flex bg-{{ $colorsArr[$customerKey%7] }} rounded-circle">{{ $abbreviation }}</div>
                                    </div> 
                                    <h5 class=" mb-1 mt-3 card-title">{{ $customer->name }}</h5> 
                                    <div class="mt-2 user-info btn-list"> 
                                        <a class="btn btn-outline-light btn-block text-right" href="mail:to{{ $customer->email }}">
                                            <i class="typcn typcn-mail mr-2 tx-22 lh-1 float-left"></i>
                                            <span>{{ $customer->email }}</span>
                                        </a> 
                                        <a class="btn btn-outline-light btn-block text-right" href="tel:{{ $customer->phone }}">
                                            <i class="typcn typcn-phone mr-2 tx-22 lh-1 float-left"></i>
                                            <span>{{ $customer->phone }}</span>
                                        </a> 
                                        <a class="btn btn-outline-light btn-block text-right" href="#">
                                            <i class="typcn typcn-map mr-2 tx-22 lh-1 float-left"></i>
                                            <span>{{ $customer->country . ($customer->city != '' && $customer->country != '' ? " | "  : '') . $customer->city }}</span>
                                        </a> 
                                    </div> 
                                </div> 
                            </div> 
                        </div>
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
<script src="{{ asset('components/ajaxSearch.js') }}" type="text/javascript"></script>
@endsection
