{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
<style type="text/css" media="screen">
    .icon{
        float: unset;
    }
    .selectStyle{
        width: 50%;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple{
        height: 50px !important;
    }
</style>
@endsection

@section('content')


<input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">
<div class="row">
    <form action="{{ URL::current() }}" method="get" accept-charset="utf-8">
        <div class="col-md-8">
            <div class="apiGuide">
                <h2 class="title">{{ trans('main.search') }}</h2>
                <div class="details formSearch clearfix">
                    <input type="text" name="name" placeholder="{{ trans('main.search') }}" />
                    <button type="submit">{{ trans('main.search') }}</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="apiGuide">
                <h2 class="title">{{ trans('main.price') }}</h2>
                <div class="details priceForm formSearch clearfix">
                    <input type="text" name="price" placeholder="{{ trans('main.price') }}" />
                </div>
            </div>
        </div>
    </form>
</div>

@if($data->dis != 1)
<a href="{{ URL::current().'?refresh=refresh' }}" class="updateBtn">{{ trans('main.refresh') }}</a>
@endif

<div class="row">
    @foreach($data->data as $product)
    <div class="col-md-4">
        <div class="orderStyle">
            <div class="img">
                <img src="{{ $product->images != null ? $product->images[0] : asset('images/not-available.jpg') }}" alt="" />
            </div>
            <div class="details">
                <span class="available">{{ trans('main.avail') }}</span>
                <div class="clearfix">
                    <a href="#" class="titleOrder">{{ $product->name }}</a>
                    <span class="price">{{ $product->price . ' ' . $product->currency }}</span>
                </div>
                <p>{{ trans('main.quantity') }} : {{ $product->quantity }} </p>
                <div class="clearfix">
                    {{-- <a href="#" class="titleOrder">{{ trans('main.category') }}</a>
                    <div class="selectStyle float-right">
                        <select name="category_id" data-toggle="select2" data-area="{{ $product->id }}" {{ \Helper::checkRules('whatsapp-assignCategory') ? '' : 'disabled' }}>
                            <option value="">{{ trans('main.choose') }}</option>
                            <option value="1" {{ $product->category_id == 1 ? 'selected' : '' }}>{{ trans('main.prod_cat1') }}</option>
                            <option value="2" {{ $product->category_id == 2 ? 'selected' : '' }}>{{ trans('main.prod_cat2') }}</option>
                            <option value="3" {{ $product->category_id == 3 ? 'selected' : '' }}>{{ trans('main.prod_cat3') }}</option>
                        </select>
                    </div>
                    <div class="clearfix"></div> --}}
                </div>
                @if($product->addon_product_id == null && \Helper::checkRules('whatsapp-assignSallaProduct') && !$data->disAssign )
                <a href="#" class="btnOrder"  data-area="{{ $product->id }}" data-toggle="modal" data-target="#modal-salla-products">{{ trans('main.assignSallaProduct') }}</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@include('tenant.Partials.pagination')
@include('tenant.Partials.SallaProducts')
@endsection
