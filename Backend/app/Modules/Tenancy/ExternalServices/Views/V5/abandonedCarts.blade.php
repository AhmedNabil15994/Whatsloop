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
    .updateBtn{
        width: 100%;
        margin-bottom: 5px;
    }
    .updateBtn:last-of-type{
        margin-bottom: 15px;
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
    input[name="date"],input[name="price"],input[name="phone"]{
        width: 100%;
    }
    .formSearch a.refresh{
        width: 45px;
        height: 45px;
        font-size: 14px;
        border-radius: 50%;
        border: none;
        margin-right: 10px;
        margin-left: 10px;
        background-color: #00BFB5;
        color: #fff;
        float: left;
        display: inline-block;
    }
    .formSearch a.refresh i{
        width: 100%;
        height: 100%;
        font-size: 20px;
        padding: 0;
        display: block;
        margin: 12px;
    }
    .formSearch a.extraAction{
        width: 150px;
        height: 45px;
        font-family: "Tajawal-Bold";
        font-size: 14px;
        margin-right: 10px;
        margin-left: 10px;
        border-radius: 10px;
        border: none;
        background-color: #00BFB5;
        color: #fff;
        float: left;
        display: inline-block;
        text-align: center;
        padding: 11px;
    }
    #resendModal .select2-container,.select2-selection.select2-selection--multiple{
        height: 100px !important;
    }
    #resendModal .select2-selection__rendered{
        height: 85px !important;
    }
    .mt-3{
        margin-top: 30px;
    }
    .eventItem{
        border-bottom: 1px solid #F8F8F8;
    }
    .eventItem .row{
        padding: 15px;
    }
    .eventItem h5{
        font-weight: bold;
        margin-bottom: 0;
        padding-top: 10px;
    }
    .eventItem a{
        color: inherit;
    }
    .eventItem .row .toggleBut{
        display: block;
        position: relative;
        height: 25px;
        width: 45px;
        border-radius: 15px;
        border: none;
        background: #E8E8E8;
        float: left;
        -webkit-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }
    .eventItem .row .toggleBut:after{
        content: "";
        position: absolute;
        top: 2.8px;
        height: 18px;
        width: 18px;
        background-color: #fff;
        border-radius: 50%;
        -webkit-transition: all 1s ease-in-out;
        transition: all 1s ease-in-out;
    }
    .eventItem .row .toggleBut.active {
        border-color: #151B29;
        background: #E8E8E8;
    }
    .eventItem .row .toggleBut.active:after{
        background-color: #00BFB5;
    }
    html[dir="ltr"] .eventItem .row .toggleBut{
        float: right;
    }
    html[dir="rtl"] .eventItem .row .toggleBut.active:after{
        left: 22px;
    }
    html[dir="ltr"] .eventItem .row .toggleBut.active:after{
        right: 22px;
    }
    .eventItem .row .editEvent{
        display: inline-block;
        margin-top: 3px;
        margin-left: 5px;
        margin-right: 5px;
    }
    .pb-0{
        padding-bottom: 0;
    }
    .carts{
        display: block;
        width:  100%;
    }
    html[dir="rtl"] .abCart,
    html[dir="rtl"] .pagin .col-md-6.mt-1.d-none.d-md-block{
        text-align: right;
    }
    html[dir="ltr"] .abCart,
    html[dir="ltr"] .pagin .col-md-6.mt-1.d-none.d-md-block{
        text-align: left;
    }
</style>
@endsection

@section('content')
@php $hasSearch = 0; @endphp

@if($data->checkAvailBotPlus == 1)
<select name="bots" class="hidden">
    @if($data->checkAvailBot == 1)
    @foreach($data->bots as $bot)
    <option value="{{ $bot->id }}" data-type="1">{{ trans('main.clientMessage') . ' ( ' .$bot->message . ' ) ==== ' . trans('main.bot') }}</option>
    @endforeach
    @endif

    @foreach($data->botPlus as $plusBot)
    <option value="{{ $plusBot->id }}" data-type="2">{{ trans('main.clientMessage') . ' ( ' .$plusBot->message . ' ) ==== ' . trans('main.botPlus') }}</option>
    @endforeach
</select>
@endif

<input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">

<div class="row">
    <form action="{{ URL::current() }}" method="get" accept-charset="utf-8">
        <div class="col-md-10">
            <div class="apiGuide">
                <h2 class="title">{{ trans('main.searchAbandoned') }}</h2>
                <div class="details formSearch clearfix">
                    <div class="row">
                        <div class="col-md-3">
                            <select class="form-control" data-toggle="select2" name="status">
                                <option value="">{{ trans('main.status') }}</option>
                                <option value="2" {{ Request::has('status') && Request::get('status') == 2 ? 'selected' : '' }}>{{ trans('main.notSent') }}</option>
                                <option value="1" {{ Request::has('status') && Request::get('status') == 1 ? 'selected' : '' }}>{{ trans('main.sentDone') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" data-toggle="select2" name="client">
                                <option value="">{{ trans('main.client') }}</option>
                                @foreach($data->customers as $client)
                                <option value="{{ $client['id'] }}" {{ Request::has('client') && Request::get('client') == $client['id'] ? 'selected' : '' }}>{{ $client['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input class="datepicker" value="{{ Request::has('date') ? Request::get('date') : '' }}" type="text" name="date" placeholder="{{ trans('main.date') }}" />
                        </div>
                        <div class="col-md-3">
                            <input type="text" value="{{ Request::has('phone') ? Request::get('phone') : '' }}" name="phone" placeholder="{{ trans('main.phone') }}" />
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3 mt-3">
                            <input type="text" value="{{ Request::has('price') ? Request::get('price') : '' }}" name="price" placeholder="{{ trans('main.price') }}" />
                        </div>
                        <div class="col-md-3 mt-3">
                            <select class="form-control" data-toggle="select2" name="duration">
                                <option value="">{{ trans('main.duration') }}</option>
                                <option value="1" {{ Request::has('duration') && Request::get('duration') == 1 ? 'selected' : '' }}>{{ trans('main.day') }}</option>
                                <option value="2" {{ Request::has('duration') && Request::get('duration') == 2 ? 'selected' : '' }}>{{ trans('main.week') }}</option>
                                <option value="3" {{ Request::has('duration') && Request::get('duration') == 3 ? 'selected' : '' }}>{{ trans('main.month') }}</option>
                                <option value="4" {{ Request::has('duration') && Request::get('duration') == 4 ? 'selected' : '' }}>{{ trans('main.year') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-3">
                            <button type="button" class="mainSearch">{{ trans('main.send') }}</button>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="apiGuide">
                <h2 class="title">{{ trans('main.actions') }}</h2>
                <div class="details formSearch clearfix">
                    @if($data->dis != 1)
                    <a href="{{ URL::current().'?refresh=refresh' }}" class="updateBtn">{{ trans('main.refresh') }}</a>
                    <a class="updateBtn" data-effect="effect-sign" data-toggle="modal" data-target="#scheduledMsgs" data-backdrop="static">{{ trans('main.scheduledMsgs') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>

<div class="row rowEvents">
    <div class="col-xs-12">
        <div class="apiGuide">
            <h2 class="title"><i class="fa fa-bell"></i> {{ trans('main.events') }}</h2>
            <div class="details formSearch clearfix">
                @foreach($data->events as $event)
                <div class="eventItem">
                    <div class="row">
                        <div class="col-xs-10">
                            <h5>{{trans('main.reminder_in')}} {{ $event->time }} ساعة ({{$event->messageTypeText}})</h5>
                            <span></span>
                        </div>
                        <div class="col-xs-2 text-right">
                            <a href="#" class="editEvent" data-type="{{$event->id}}"><i class="fa fa-pencil-alt"></i></a>
                            <button class="toggleBut {{ $event->status == 1 ? 'active' : '' }}" data-type="{{ $event->id }}" title="Change Mode"></button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="carts">
    <div class="row">
        @foreach($data->data as $key => $order)
        @if($key % 3 == 0 && $key > 0)
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
    @include('tenant.Partials.pagination')
</div>
@endsection

@section('modals')
<div class="modal fade" id="scheduledMsgs">
    <div class="modal-dialog modal-lg formNumbers" role="document">
        <div class="modal-content form">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans('main.scheduledMsgs') }}</h5>
            </div>
            <div class="modal-body">
                <h4 class="modal-title" id="exampleModalLabel">{{ trans('main.scheduledMsgsP') }}</h4>
                <form class="formPayment" method="POST" action="{{ URL::current().'/resendCarts' }}">
                    @csrf
                    <input type="hidden" name="event_id">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="titleLabel">{{ trans('main.replyType') }} :</label>
                        </div>
                        <div class="col-md-9">
                            <div class="selectStyle">
                                <select data-toggle="select2" data-style="btn-outline-myPR" name="message_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1">{{ trans('main.text') }}</option>
                                    <option value="2">{{ trans('main.photoOrFile') }}</option>
                                    @if($data->checkAvailBotPlus == 1)
                                    <option value="3">{{ trans('main.botPlus') }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="titleLabel">{{ trans('main.sending_time') }} :</label>
                        </div>
                        <div class="col-md-9">
                            <input type="number" placeholder="{{trans('main.sending_time')}}" name="time" class="mt-2" value="{{ !empty($data->schedulemsg_data) && isset($data->schedulemsg_data[1]) ? $data->schedulemsg_data[1] : '' }}">
                        </div>
                    </div> 
                    <div class="reply" data-id="1">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.body') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea name="content" placeholder="{{ trans('main.body') }}">{{ $data->schedulemsg != null ? $data->schedulemsg->var_value : $data->template->description_ar }}</textarea>
                            </div>
                        </div> 
                    </div>
                    <div class="reply" data-id="2">
                        <div class="row hidden">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.textWithPhoto') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea name="caption" placeholder="{{ trans('main.textWithPhoto') }}">{{ $data->schedulemsg != null ? $data->schedulemsg->var_value : $data->template->description_ar }}</textarea>
                            </div>
                        </div>
                        <div class="row hidden">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.attachFile') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="dropzone kt_dropzone_1">
                                    <div class="fallback">
                                        <input name="file" type="file" />
                                    </div>
                                    <div class="dz-message needsclick">
                                        <i class="h1 si si-cloud-upload"></i>
                                        <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
                                    </div>
                                    <div class="dz-preview dz-image-preview hidden" id="my-preview">  
                                        <div class="dz-image">
                                            <img alt="image" src="">
                                        </div>  
                                        <div class="dz-details">
                                            <div class="dz-size">
                                                <span><strong></strong></span>
                                            </div>
                                            <div class="dz-filename">
                                                <span data-dz-name=""></span>
                                            </div>
                                            <div class="PhotoBTNS">
                                                <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                   <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                        <a href="" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                        <img src="" itemprop="thumbnail" style="display: none;">
                                                    </figure>
                                                </div>               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5>{{ trans('main.file100kb') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="reply" data-id="3">
                        <div class="row hidden">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.title') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('title') }}" name="title" placeholder="{{ trans('main.title') }}">
                            </div>
                        </div>
                        <div class="row hidden">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.body') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea name="body" placeholder="{{ trans('main.body') }}">{{ $data->schedulemsg != null ? $data->schedulemsg->var_value : $data->template->description_ar }}</textarea>
                            </div>
                        </div>
                        <div class="row hidden">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.footer') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('footer') }}" name="footer" placeholder="{{ trans('main.footer') }}">
                            </div>
                        </div>
                        <div class="row hidden">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.buttons') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <select data-toggle="select2" data-style="btn-outline-myPR" name="buttons">
                                    <option value="1" {{ old('buttons') == 1 ? 'selected' : '' }}>1</option>
                                    <option value="2" {{ old('buttons') == 2 ? 'selected' : '' }}>2</option>
                                    <option value="3" {{ old('buttons') == 3 ? 'selected' : '' }}>3</option>
                                    <option value="4" {{ old('buttons') == 4 ? 'selected' : '' }}>4</option>
                                    <option value="5" {{ old('buttons') == 5 ? 'selected' : '' }}>5</option>
                                    <option value="6" {{ old('buttons') == 6 ? 'selected' : '' }}>6</option>
                                    <option value="7" {{ old('buttons') == 7 ? 'selected' : '' }}>7</option>
                                    <option value="8" {{ old('buttons') == 8 ? 'selected' : '' }}>8</option>
                                    <option value="9" {{ old('buttons') == 9 ? 'selected' : '' }}>9</option>
                                    <option value="10" {{ old('buttons') == 10 ? 'selected' : '' }}>10</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
                            <div class="buts">
                                <div class='row mains'>
                                    <div class='col-md-3'>
                                        <label class='titleLabel'>{{ trans('main.btnData',['button'=>1]) }} :</label>
                                    </div>
                                    <div class='col-md-9'>
                                        <div class='row'>
                                            <div class='col-md-4'>
                                                <input type='text' name='btn_text_1' value="" placeholder='{{ trans('main.text') }}'>
                                            </div>
                                            <div class='col-md-4'>
                                                <select data-toggle='select2' class='reply_types' name='btn_reply_type_1'>
                                                    <option value='1' selected>{{ trans('main.newReply') }}</option>
                                                    <option value='2'>{{ trans('main.botMsg') }}</option>
                                                </select>
                                            </div>
                                            <div class='col-md-4 repy'>
                                                <textarea class="" name='btn_reply_1' placeholder='{{ trans('main.messageContent') }}' maxlength="140"></textarea>
                                                <select data-toggle="" class='dets hidden' name='btn_msg_1'>
                                                    <option value='' selected>{{ trans('main.choose') }}</optin>
                                                    @foreach($data->bots as $bot)
                                                    <option value="{{ $bot->id }}" data-type="1">{{ trans('main.clientMessage') . ' ( ' .$bot->message . ' ) ==== ' . trans('main.bot') }}</option>
                                                    @endforeach
                                                    @foreach($data->botPlus as $plusBot)
                                                    <option value="{{ $plusBot->id }}" data-type="2">{{ trans('main.clientMessage') . ' ( ' .$plusBot->message . ' ) ==== ' . trans('main.botPlus') }}</option>
                                                    @endforeach
                                                </select>
                                                <input type='hidden' name='btn_msg_type_1' value=''>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>                    
                </form>
                <h4 class="modal-title" id="exampleModalLabel">{{ trans('main.scheduledMsgsP2') }}</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger font-weight-bold" data-dismiss="modal">{{trans('main.back')}}</button>
                <button type="button" class="btn btn-success font-weight-bold schedMSG">{{trans('main.save')}}</button>
            </div>
        </div>
    </div>
</div>
@include('tenant.Partials.photoswipe_modal')


@endsection

@section('scripts')
<script src="{{ asset('V5/components/abandonedCarts.js') }}"></script>
<script src="{{ asset('V5/components/addBotPlus.js') }}"></script>
<script src="{{ asset('/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('/components/myPhotoSwipe.js') }}"></script>      
@endsection