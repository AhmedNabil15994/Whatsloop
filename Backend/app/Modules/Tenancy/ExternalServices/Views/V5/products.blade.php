{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
<style type="text/css" media="screen">
    .icon{
        float: unset;
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
                    <input type="text" name="name" value="{{ Request::get('name') }}" placeholder="{{ trans('main.search') }}" />
                    <button type="submit">{{ trans('main.search') }}</button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="apiGuide">
                <h2 class="title">{{ trans('main.price') }}</h2>
                <div class="details priceForm formSearch clearfix">
                    <input type="text" name="price" value="{{ Request::get('price') }}" placeholder="{{ trans('main.price') }}" />
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
                <img src="{{ $product->images != null ? $product->images : asset('images/not-available.jpg') }}" alt="" />
            </div>
            <div class="details">
                <span class="available">{{ $product->status == 1 ? trans('main.avail') : trans('main.unAvail') }}</span>
                <div class="clearfix">
                    <a href="#" class="titleOrder">{{ $product->{'name_'.LANGUAGE_PREF} }}</a>
                    <span class="price">{{ $product->price }}</span>
                </div>
                <a href="#" class="btnOrder">{{ trans('main.quantity') }} : {{ $product->quantity }} {{ trans('main.piece') }}</a>
            </div>
        </div>
    </div>
    @endforeach
</div>

@include('tenant.Partials.pagination')
@endsection
