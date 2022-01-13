{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
<style type="text/css" media="screen">
    .icon{
        float: unset;
    }
    .abCart .list li span{
        float: unset;
    }
    .updateBtn,
    .updateBtn:hover{
        display: inline-block;
    }
    .formPayment{
        border-top: 0;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple,
    .select2-container--default .select2-selection--multiple{
        height: 50px !important;
    }
    .modal textarea{
        min-height:  230px;
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
<a class="updateBtn" data-effect="effect-sign" data-toggle="modal" data-target="#resendModal" data-backdrop="static">{{ trans('main.resend') }}</a>
@endif
<div class="row">
    @foreach($data->data as $key => $order)
    @if($key % 3 == 0)
    </div><div class="row"> 
    @endif
    <div class="col-md-4">
        <div class="abCart">
            <h2 class="titleCart clearfix">{{ trans('main.cartno').': ' }} <span>{{ $order->id .' | ' . $order->created_at}}</span></h2>
            <span class="orderTitle">{{ trans('main.orderItems') }}  {!! $order->sent_count > 0 ? '<span class="float-right label label-success">'.trans('main.sentBefore').'</span>' : '' !!} </span>
            <ul class="list">
                @if(is_array($order->items))
                    @foreach($order->items as $key=> $item)
                    <li>{{ $key+1 .'- '}} {{ $item['name'] }} <span class="total">{{ trans('main.quantity').': '. $item['quantity'] }}</span></li>
                    @endforeach
                @else
                    <li><span class="total">{{ $order->items }}</span></li>
                @endif
            </ul>
            <span class="orderTitle">{{ trans('main.client') }}</span>
            <ul class="userDetails">
                <li><i class="flaticon-user-1"></i> <span>{{ $order->customer['name'] }}</span></li>
                <li><i class="flaticon-phone-call"></i> <span>{{ $order->customer['mobile'] }}</span></li>
                <li><i class="flaticon-map"></i> <span>{{ $order->customer['country'] }}</span></li>
            </ul>
            <div class="details">
                <a href="{{ isset($order->order_url) ? $order->order_url : 'https://web.zid.sa/login' }}" class="btnStyle">{{ trans('main.info') }}</a>
            </div>
        </div>
    </div>
    @endforeach
</div>     

@section('modals')
<div class="modal fade" id="resendModal">
    <div class="modal-dialog modal-lg formNumbers" role="document">
        <div class="modal-content form">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('main.resend') }}</h5>
            </div>
            <div class="modal-body">
                <form class="formPayment" method="POST" action="">
                    @csrf
                    <input type="hidden" name="status">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="titleLabel">{{ trans('main.clients') }} :</label>                            
                        </div>
                        <div class="col-md-9">
                            <div class="selectStyle">
                                <select data-toggle="select2" data-style="btn-outline-myPR" name="clients" multiple>
                                    <option class="di" value="">{{ trans('main.choose') }}</option>
                                    <option class="di" value="@">{{ trans('main.selectAll') }}</option>
                                    @foreach($data->customers as $customer)
                                    <option value="{{ $customer['order_id'] }}" data-name="{{ $customer['name'] }}" data-mobile="{{ $customer['mobile'] }}" data-total="{{ $customer['total'] }}" data-url="{{ $customer['url'] }}">{{ $customer['order_id'] . ' - ' . $customer['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class="row">
                        <div class="col-md-3">
                            <label for="inputPassword3" class="titleLabel">{{ trans('main.body') }} :</label>
                        </div>
                        <div class="col-md-9">
                            <textarea name="body" placeholder="{{ trans('main.body') }}">{{ $data->template->description_ar }}</textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="titleLabel">{{ trans('main.sending_date') }} :</label>
                        </div>
                        <div class="col-md-9">
                            <div class="radio radio-blue mb-1 float-left">
                                <input type="radio" class="first" id="radio" value="radio" checked="true" name="sending">
                                <label for="radio"></label>
                            </div>
                            <p class="check-title">{{ trans('main.now') }}</p>
                            <div class="clearfix"></div>
                            <div class="radio radio-blue  float-left">
                                <input type="radio" class="second" id="radio2" value="radio2" name="sending">
                                <label for="radio2"></label>
                            </div>
                            <p class="check-title">{{ trans('main.send_at') }}</p>
                            <div class="clearfix"></div>
                            <input type="text" placeholder="YYYY-MM-DD H:i" name="date" class="hidden mt-2" id="datetimepicker">
                        </div>
                    </div> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger font-weight-bold" data-dismiss="modal">{{trans('main.back')}}</button>
                <button type="button" class="btn btn-success font-weight-bold resendCarts">{{trans('main.resend')}}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@include('tenant.Partials.pagination')
@endsection

@section('scripts')
<script src="{{ asset('V5/components/abandonedCarts.js') }}"></script>
@endsection