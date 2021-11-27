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

<div class="row">
    @foreach($data->data as $key => $order)
    @if($key % 3 == 0)
    </div><div class="row"> 
    @endif
    <div class="col-md-4">
        <div class="abCart">
            <h2 class="titleCart clearfix">{{ trans('main.order').': ' }} <span>{{ $order->order_id }}</span></h2>
            <span class="orderTitle">{{ trans('main.orderItems') }}</span>
            <ul class="list">
                @if(is_array($order->products))
                    @foreach($order->products as $key=> $item)
                    <li>{{ $key+1 .'- '}} {{ $item['name'] }} <span class="total">{{ trans('main.quantity').': '. $item['quantity'] }}</span></li>
                    @endforeach
                @endif
            </ul>
            <span class="orderTitle">
                {{ trans('main.total') }}
                <span class="date">{{ $order->total . ' ' . $order->products[0]['currency'] }}</span>
            </span>
            <span class="orderTitle">
                {{ trans('main.date') }}
                <span class="date">{{ $order->created_at }}</span>
            </span>
            <span class="orderTitle">{{ trans('main.client') }}</span>
            <ul class="userDetails">
                <li><i class="flaticon-user-1"></i> <span>{{ str_replace('+','',$order->client->name) }}</span></li>
                <li><i class="flaticon-phone-call"></i> <span>{{ str_replace('+','',$order->client->phone) }}</span></li>
                <li><i class="flaticon-map"></i> <span>{{ $order->client->country }}</span></li>
            </ul>
            <div class="details">
                @if(\Helper::checkRules('whatsapp-orders-sendLink'))
                <a href="{{ $order->status == 1  ? URL::to('/whatsappOrders/orders/'.$order->id.'/sendLink') : URL::to('/orders/'.$order->order_id.'/invoice') }}" class="btnStyle">{{ $order->status == 1 ? trans('main.sendPaymentLink') : trans('main.invoiceLink') }}</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>     
@include('tenant.Partials.pagination')
@endsection