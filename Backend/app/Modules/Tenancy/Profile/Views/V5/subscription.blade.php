{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css" media="screen">
    .AdditionsSub .title a{
        padding: 0;
    }
    .cartSub{
        padding: 40px 15px 25px;
        width: 100%;
        margin-right: 0;
        margin-left: 0;
    }
    .carts .row{
        margin: 0;
        width: 100%;
    }
    .modal-full-width{
        width: 75%;
    }
    .modal-header .close{
        margin-top: -25px;
    }
    .form-group.MeasuresText{
        margin: auto;
        width: 100%;
    }
    .form-group.MeasuresText label,
    .form-group{
        margin-bottom: 0;
    }
    .AdditionsSub .title a.edit{
        width: 120px;
    }
    .queues .desc2{
        max-height: 350px;
        overflow-y: scroll;
    }
    .queues .desc2 .row div.col-md-2,
    .queues .desc2 .row div.col-md-3,
    .queues .desc2 .row div.col-md-6{
        padding: 10px;
    }
    .row.mains{
        border-bottom: 1px solid #EFEFEF;
    }
    .row.mains .title{
        border: 0;
    }
    .queues .desc2 .row div.col-md-2.phone{
        direction: ltr;
    }   
    .queues .desc2 .row {
        border-bottom: 1px solid #EFEFEF;
    }

</style>
@endsection

@section('content')

<div class="tickets">
                
    <div class="ticketContent Measures text-center">
        <h2 class="title">{{ trans('main.actions') }}</h2>
        <div class="desc">
            <a href="#" class="MeasuresText screen color1">{{ trans('main.screenshot') }}</a>
            <a href="{{ URL::to('/profile/subscription/sync') }}" class="MeasuresText color2">{{ trans('main.sync') }}</a>
            <a href="{{ URL::to('/profile/subscription/syncAll') }}" class="MeasuresText color3">{{ trans('main.syncAll') }}</a>
            <a href="{{ URL::to('/profile/subscription/restoreAccountSettings') }}" class="MeasuresText color4">{{ trans('main.restoreAccountSettings') }}</a>
            <a href="{{ URL::to('/profile/subscription/reconnect') }}" class="MeasuresText color5">{{ trans('main.reestablish') }}</a>
            <a href="{{ URL::to('/profile/subscription/closeConn') }}" class="MeasuresText color6">{{ trans('main.closeConn') }}</a>
            <a href="{{ URL::to('/profile/subscription/read/1') }}" class="MeasuresText color7">{{ trans('main.readAll') }}</a>
            <a href="{{ URL::to('/profile/subscription/read/0') }}" class="MeasuresText color8">{{ trans('main.unreadAll') }}</a>
            <a href="{{ URL::to('/profile/subscription/syncDialogs') }}" class="MeasuresText color0">{{ trans('main.syncDialogs') }}</a>
            <a href="{{ URL::to('/profile/subscription/syncLabels') }}" class="MeasuresText color1">{{ trans('main.syncLabels') }}</a>
            @if(\Helper::checkRules('whatsapp-orders,whatsapp-products'))
            <a href="{{ URL::to('/profile/subscription/syncOrdersProducts') }}" class="MeasuresText color2">{{ trans('main.syncOrdersProducts') }}</a>
            @endif
        </div>
    </div>

    <div class="ticketContent Measures text-center">
        <h2 class="title">{{ trans('main.channel_settings') }}</h2>
        <div class="desc">
            <div class="row">
                @php $i = 0; @endphp
                @foreach($data->channelSettings as $key => $oneSetting)
                @if(in_array($key,['disableGroupsArchive','disableDialogsArchive']))
                <div class="col-xs-3">
                    <div class="form-group row mb-0 MeasuresText color{{$i++%9}}" >
                        <label class="col-md-9 col-xs-6 col-form-label boldText" style="padding-left:0">{{ trans('main.channelSettings_'.$key) }} :</label>
                        <div class="col-md-3 col-xs-6">
                            <div class="form-group textLeft">
                                <label class="custom-switch pl-0">
                                    <input type="checkbox" name="custom-switch-checkbox{{ $key }}" class="custom-switch-input" {{ $oneSetting == true ? 'checked' : '' }} data-area="{{ $key }}">
                                    <span class="custom-switch-indicator"></span>
                                </label>
                            </div>
                        </div>
                    </div> 
                </div>
                @endif
                @endforeach
            </div>
        </div>
    </div>

    @if(isset($data->totalQueueMessages) && $data->totalQueueMessages > 0)
    <div class="ticketContent queues Measures text-center">
        <h2 class="title">{{ trans('main.messagesQueue') }} ({{$data->totalQueueMessages}})</h2>
        <div class="desc">
            <div class="row mains">
                <div class="col-md-9">
                    <h2 class="title">{{ trans('main.first100Msgs') }}</h2>
                </div>
                <div class="col-md-3 text-right">
                    <a class="btn btn-danger btn-md" href="{{ URL::to('/profile/subscription/clearMessagesQueue') }}"> <i class="fa fa-trash"></i> {{ trans('main.delete') }}</a>
                </div>
            </div>
            <div class="desc desc2">
                <div class="row">
                    <div class="col-md-2">{{trans('main.phone')}}</div>
                    <div class="col-md-6">{{trans('main.messageContent')}}</div>
                    <div class="col-md-2">{{trans('main.messageType')}}</div>
                    <div class="col-md-2">{{trans('main.date')}}</div>
                </div>
                @foreach($data->queuedMessages as $msg)
                @php 
                $newMsgData = \Helper::reformMessage($msg);
                @endphp
                <div class="row">
                    <div class="col-md-2 phone">{{$newMsgData->chatId}}</div>
                    <div class="col-md-6">{{$newMsgData->body}}</div>
                    <div class="col-md-2">{{$newMsgData->type}}</div>
                    <div class="col-md-2">{{$newMsgData->last_try}}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    
    <div class="channel channelSub">
        <div class="logoChannel">
            <img src="{{ @$data->me->avatar == null ? asset('images/logoOnly.jpg') : @$data->me->avatar }}" alt="" />
        </div>
        <div class="content">
            <div class="colms">
                <div class="title">
                    {{ trans('main.channel') }}: <span class="text"># {{ $data->channel != null ? $data->channel->instanceId : '' }}</span>
                </div>
                <div class="title">
                    {{ trans('main.phone') }}: <span class="text">{{ str_replace('@c.us', '', @$data->me->id) }}</span>
                </div>
                <div class="title">
                    {{ trans('main.connection_date') }}: <span class="text">{{ $data->status != null ? $data->status->created_at : '' }}</span>
                </div>
            </div>
            <div class="colms">
                <div class="title">
                    {{ trans('main.phone_status') }}: <span class="textColor MeasuresText color9">{{ $data->status != null ? $data->status->statusText : '' }}</span>
                </div>
                <div class="title">
                    {{ trans('main.msgSync') }}: <span class="textColor MeasuresText color9">{{ $data->allMessages > 0 ? trans('main.synced') : trans('main.notSynced') }}</span>
                </div>
                <div class="title">
                    {{ trans('main.contSync') }}: <span class="textColor MeasuresText color9">{{ $data->contactsCount > 0 ? trans('main.synced') : trans('main.notSynced') }}</span>
                </div>
            </div>
            <div class="colms">
                <div class="title">
                    {{ trans("main.phone_battery") }}: <span class="text">{{ @$data->me->battery }}%</span>
                </div>
                <div class="title">
                    {{ trans('main.phone_type') }}: <span class="text">{{ @$data->me->device['manufacturer'] }}</span>
                </div>
                <div class="title">
                    {{ trans('main.phone_model') }}: <span class="text">{{ @$data->me->device['model'] }}</span>
                </div>
                <div class="title">
                    {{ trans('main.os_ver') }}: <span class="text">{{ @$data->me->device['os_version'] }}</span>
                </div>
            </div>
            <div class="colms">
                <div class="title">
                    {{ trans('main.leftDays') }}
                </div>
                <div class="progressSize clearfix">
                    <div class="line" style="width:{{ $data->channel != null ? $data->channel->rate : '' }}%"></div>
                </div>
                <div class="title">
                    <span class="text">{{ $data->channel != null ? $data->channel->leftDays : '' }} {{ trans('main.day') }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="planDetails">
        <div class="row">
            <div class="col-md-6">
                <div class="AdditionsSub">
                    <h2 class="title">{{ trans('main.currentPackage') }}</h2>
                    <div class="footerPlan">
                        <div class="planContent">
                            <h3 class="planName">{{ trans('main.packageName') }}</h3>
                            <span class="planState">{{ $data->subscription->package_name }}</span>
                        </div>
                        <div class="state">
                            <h3 class="stateSub">{{ trans('main.substatus') }}</h3>
                            @if(IS_ADMIN && $data->subscription->package_id != 3 && $data->subscription->package_id != 4)
                            <a href="{{ URL::to('/updateSubscription?type=membership') }}" class="activeStyle activeStyle2">{{ trans('main.upgrade') }}</a>
                            @endif
                            <a href="#" class="activeStyle {{ $data->subscription->channelStatus == 1 ? 'activeStyle1' : 'label label-danger' }}">{{ $data->subscription->channelStatus == 1 ? trans('main.active') : trans('main.notActive') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="AdditionsSub">
                    <h2 class="title">{{ trans('main.nextMillestone') }}</h2>
                    <span class="dateStyle">{{ $data->subscription->end_date }}</span>
                    <div class="footerPlan footerNext">
                        <div class="next">
                            <ul class="dates clearfix">
                                <li>{{ trans('main.substartDate') }}<span>{{ $data->subscription->start_date }}</span></li>
                                <li>{{ trans('main.subendDate') }} <span>{{ $data->subscription->end_date }}</span></li>
                            </ul>
                            
                        </div>
                        <div class="days">
                            <span class="numbrs">{{ $data->subscription->leftDays }} {{ trans('main.leftDays') }}</span>
                            {{-- @if(!in_array(date('d',strtotime($data->subscription->end_date)) , [1,28,29,30,31]) && IS_ADMIN)
                            <a href="{{ URL::to('/profile/subscription/transferPayment') }}" class="nextMonth">{{ trans('main.transferPayment') }}</a>
                            @endif --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="AdditionsSub">
                    <h2 class="title">
                        {{ trans('main.addons') }} 
                        @if(IS_ADMIN && count($data->subscription->addons) < 12)
                        <a href="{{ URL::to('/updateSubscription?type=addon') }}" class="edit">{{ !Session::has('invoice_id') ? trans('main.edit') : trans('main.resubscribe_b2') }}</a>
                        @endif
                    </h2>
                    <div class="clearfix carts">
                        <div class="row">
                            @foreach($data->subscription->addons as $key => $addon)
                            @if($key % 3 == 0)
                            </div><div class="row">
                            @endif
                            <div class="col-md-4">
                                <div class="cartSub">
                                    <h2 class="titleCart">{{ $addon->Addon->title }}</h2>
                                    <span class="date"><span>{{ $addon->start_date }}</span> <span>{{ $addon->end_date }}</span></span>
                                    <span class="activeStyle">{{ $addon->statusText }}</span>
                                    <div class="options">
                                        <i class="openOptions flaticon-menu-1"></i>
                                        <ul class="optionsList">
                                            @if($addon->status == 2)
                                            <li><a href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/4') }}">{{ trans('main.renew') }}</a></li>
                                            <li><a href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/5') }}">{{ trans('main.delete') }}</a></li>
                                            @elseif($addon->status == 1)
                                            <li><a href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/3') }}">{{ trans('main.disable') }}</a></li>
                                            @elseif($addon->status == 3)
                                            <li><a href="{{ URL::to('/updateAddonStatus/'.$addon->id.'/1') }}">{{ trans('main.enable') }}</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div> 
            <div class="col-md-6">
                <div class="AdditionsSub">
                    <h2 class="title">
                        {{ trans('main.extraQuotas') }} 
                        @if(IS_ADMIN)
                        <a href="{{ URL::to('/updateSubscription?type=extra_quota') }}" class="edit">{{ trans('main.edit') }}</a>
                        @endif
                    </h2>
                    <div class="clearfix carts">
                        <div class="row">
                            @foreach($data->subscription->extra_quotas as $extra_quota)
                            <div class="col-md-4">
                                <div class="cartSub">
                                    <h2 class="titleCart">{{ $extra_quota->ExtraQuota->extra_count . ' '.$extra_quota->ExtraQuota->extraTypeText . ' ' . ($extra_quota->ExtraQuota->extra_type == 1 ? trans('main.msgPerDay') : '')}}</h2>
                                    <span class="date"><span>{{ $extra_quota->start_date }}</span> <span>{{ $extra_quota->end_date }}</span></span>
                                    <span class="activeStyle">{{ $extra_quota->statusText }}</span>
                                    @if($extra_quota->status == 2)
                                    <div class="options">
                                        <i class="openOptions flaticon-menu-1"></i>
                                        <ul class="optionsList">
                                            <li><a href="{{ URL::to('/updateQuotaStatus/'.$extra_quota->id.'/4') }}">{{ trans('main.renew') }}</a></li>
                                            <li><a href="{{ URL::to('/updateQuotaStatus/'.$extra_quota->id.'/5') }}">{{ trans('main.delete') }}</a></li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>


<div class="stats">
    <div class="row">
        <div class="col-md-6">
            <div class="itemStats color1">
                <h2 class="title">{{ trans('main.messages') }}</h2>
                <span class="numb">{{ $data->allMessages }}</span>
                <i class="icon flaticon-email-1"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="itemStats color2">
                <h2 class="title">{{ trans('main.sentMessages') }}</h2>
                <span class="numb">{{ $data->sentMessages }}</span>
                <i class="icon flaticon-users"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="itemStats color3">
                <h2 class="title">{{ trans('main.incomeMessages') }}</h2>
                <span class="numb">{{ $data->incomingMessages }}</span>
                <i class="icon flaticon-paper-plane"></i>
            </div>
        </div>
        <div class="col-md-6">
            <div class="itemStats color4">
                <h2 class="title">{{ trans('main.contacts') }}</h2>
                <span class="numb">{{ $data->contactsCount }}</span>
                <i class="icon flaticon-reply"></i>
            </div>
        </div>
        
    </div>
</div>
@section('modals')
@include('tenant.Partials.screen_modal')
@endsection

@endsection


@section('scripts')
<script src="{{ asset('V5/components/subscription.js') }}" type="text/javascript"></script>
@endsection
