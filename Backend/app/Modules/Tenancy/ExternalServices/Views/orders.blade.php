{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css" media="screen">
    .card-box{
        border: 1px solid #CCC;
    }
    .product-card .col{
        background: #e3e6fa;
    }
    .product-card .col h3.text-center{
        margin-top: 40px;
        margin-bottom: 40px;
    }
</style>
@endsection

@section('content')

<div class="row row-sm">
    <input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">
    <div class="col-xl-3 col-lg-4 mb-3 mb-md-0">
        <form action="{{ URL::current() }}" method="get" accept-charset="utf-8">
            <div class="card overflow-hidden">
                <h5 class="m-0 p-3 card-title bg-white border-bottom">{{ trans('main.search') }}</h5>
                <div class="py-3 px-3">
                    <div class="input-group">
                        <input type="text" name="name" value="{{ Request::get('name') }}" class="form-control" placeholder="{{ trans('main.search') }} ...">
                        <span class="input-group-append">
                            <button class="btn btn-primary" type="submit">{{ trans('main.search') }}</button>
                        </span>
                    </div>
                    @if(count(Request::all()) || $data->dis != 1)
                        @if(count(Request::all()))
                        <h5 class="m-0 p-3 card-title bg-white border-bottom">{{ trans('main.actions') }}</h5>
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
                    @endif
                </div>
            </div>
            <div class="card overflow-hidden">
                <h5 class="m-0 p-3 card-title bg-white border-bottom border-top">{{ trans('main.price') }}</h5>
                <div class="p-3 d-flex align-items-center">
                    <div class="w-100">
                        <input placeholder="{{ trans('main.price') }}" value="{{ Request::get('price') }}" name="price" class="form-control rounded-0" />
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="col-xl-9 col-lg-8">
        <div class="row row-sm">
            @foreach($data->data as $order)
            <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-4">
                <div class="product-card card overflow-hidden">
                    <div class="col" style="padding:0;"> 
                        <h3 class="text-center ">{{ trans('main.order').': '.$order->id }}</h3>
                    </div>
                    <div class="card-body h-100">
                        <h3 class="h6 mb-2 font-weight-bold text-uppercase">
                            {{ $order->status }}
                            <span class="float-right">{{ $order->created_at }}</span>
                        </h3>
                        <div class="d-block" >
                            <h4 class="h5 d-block w-100 font-weight-bold text-danger"> {{ trans('main.orderItems') }}:</h4>
                            @foreach($order->items as $key=> $item)
                            <div class="row">
                                <span class="tx-15 ml-auto">{{ $key+1 .'- '}} {{ $item['name'] }}</span>
                                <span class="tx-15 ml-auto">{{ trans('main.quantity').': '. $item['quantity'] }}</span>
                            </div>
                            @endforeach
                        </div>
                        <a class="btn btn-primary btn-block mb-0 mt-4" target="_blank" href="{{ isset($order->order_url) ? $order->order_url : 'https://s.salla.sa/login' }}">
                            <i class="fe fe-eye mr-1"></i>
                            {{ trans('main.info') }}
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @include('tenant.Partials.pagination')
    </div>
</div>
    <!-- end row-->
@endsection

@section('modals')
@include('tenant.Partials.search_modal')
@endsection

{{-- Scripts Section --}}

@section('scripts')

@endsection
