{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
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
                            <a href="{{ URL::current().'/refresh' }}" class="btn mr-2 ml-2 btn-md btn-rounded btn-outline-dark" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('main.refresh') }}">
                                <i class=" fas fa-database"></i>
                                {{ trans('main.refresh') }}
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
                                                <option value="">{{ trans('main.choose') }}</option>
                                                @foreach($searchItem['options'] as $group)
                                                @php $group = (object) $group; @endphp
                                                <option value="{{ $group->id }}" {{ Request::get($searchKey) != null && Request::get($searchKey) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
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
                                            <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" style="margin-top: 3px;" class="btn btn-danger" id="m_reset">
                                                <span>
                                                    <i class="la la-close"></i>
                                                    <span>{{ trans('main.cancel') }}</span>
                                                </span>
                                            </a>
                                            <div class="mb-0 text-center" style="display: inline-block;">
                                                <button class="ladda-button btn btn-primary btn-block loginBut" id="m_search" dir="ltr" data-style="expand-right">
                                                    <span class="ladda-label"><i class="la la-search"></i> {{ trans('main.search') }}</span>
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
                    <div class="row">
                        @foreach($data->data as $product)
                        <div class="col-md-6 col-xl-3">
                            <div class="card-box product-box">
                                <div class="product-action">
                                    @if($product->status == 1)
                                    <a class="btn btn-success text-light btn-xs waves-effect waves-light">{{ trans('main.avail') }}</a>
                                    @else
                                    <a class="btn btn-danger text-light btn-xs waves-effect waves-light">{{ trans('main.unAvail') }}</a>
                                    @endif
                                </div>
                                <div class="bg-light">
                                    <a href="{{ $product->url }}" target="_blank">
                                        <img src="{{ $product->images }}" alt="product-pic" class="img-fluid" />
                                    </a>
                                </div>
                                <div class="product-info">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <h5 class="font-16 mt-0 sp-line-1"><a href="ecommerce-product-detail.html" class="text-dark">{{ $product->{'name_'.LANGUAGE_PREF} }}</a> </h5>
                                            <div class="text-light mb-2 font-13">
                                                @foreach($product->{'categories_'.LANGUAGE_PREF} as $category)
                                                <span class="badge pd-1 bg-dark">{{ $category }}</span>
                                                @endforeach
                                            </div>
                                            <h5 class="m-0"> <span class="text-muted"> {{ trans('main.quantity') }} : {{ $product->quantity }} {{ trans('main.piece') }}</span></h5>
                                        </div>
                                        <div class="col-auto">
                                            <div class="product-price-tag">
                                                {{ $product->price }}
                                            </div>
                                        </div>
                                    </div> <!-- end row -->
                                </div> <!-- end product info-->
                            </div> <!-- end card-box-->
                        </div> <!-- end col-->
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
