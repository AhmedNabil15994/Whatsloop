@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.dashboard'))
@section('styles')
<style type="text/css" media="screen">
    .timer .nextPrev .btnNext{
        padding: 10px;
    }
    .timer.times{
        margin:30px auto;
        border-radius: 10px;
        background-color: #00bfb5;
        text-align: center;
        overflow: hidden;
        max-width:360px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        min-height: 60px;
        color: #fff;
        margin-top: 0;
    }
    .timer.times i{
        margin-left:5px;
        position: relative;
        top: 1px;
    }
    .timer.times .titleTimer{
        margin-bottom: 30px;
        width: 100%;
        color: #ffffff;
    }
</style>
@endsection

{{-- Content --}}
@section('content')
<div class="stats">
    @if(App\Models\Variable::getVar('SYNCING') == 1 || App\Models\Variable::getVar('QRSYNCING') == 1)
    <div class="col-xs-12" style="padding: 0;">
        <div class="timer times" style="max-width: 100%;">
            <h2 class="titleTimer"><i class="fa fa-refresh fa-spin"></i> {{ trans('main.syncInProgress') }}</h2>
            <div class="desc"></div>
        </div>
    </div>
    @endif

    @if(Session::has('hasJob') && Session::get('hasJob') == 1)
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="timer">
                <img src="{{ asset('V5/images/checkImg.png') }}" alt="">
                <h2 class="titleTimer">{{ trans('main.inPrgo') }}</h2>
                <span class="time mCounter" dir="ltr">05:00</span>
                <div class="desc">
                    {{ trans('main.preparingAccount') }}
                </div>
                <div class="Attention">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32.001" height="39.205" viewBox="0 0 32.001 39.205">
                      <path id="XMLID_560_" d="M61.928,39.205A13.2,13.2,0,0,1,50.92,33.488L43.514,22.639A3.1,3.1,0,0,1,45.8,17.884a6.285,6.285,0,0,1,5.776,2.656V7.555a3.7,3.7,0,0,1,3.767-3.63,3.888,3.888,0,0,1,1.085.153V3.855a4.008,4.008,0,0,1,8.01,0V4.2a4.032,4.032,0,0,1,1.228-.19,3.844,3.844,0,0,1,3.91,3.765v.685a4.177,4.177,0,0,1,1.371-.23A3.983,3.983,0,0,1,75,12.135V26.791c0,6.845-5.864,12.415-13.073,12.415ZM46.5,20.526a3.555,3.555,0,0,0-.4.022c-.274.031-.561.3-.372.579l7.409,10.853,0,.006a10.519,10.519,0,0,0,8.786,4.537c5.73,0,10.391-4.367,10.391-9.734V12.135a1.38,1.38,0,0,0-2.741,0v8.779a1.341,1.341,0,0,1-2.682,0V7.78a1.237,1.237,0,0,0-2.456,0V20.734a1.341,1.341,0,0,1-2.682,0V3.855a1.332,1.332,0,0,0-2.646,0v16.7a1.341,1.341,0,0,1-2.682,0v-13a1.095,1.095,0,0,0-2.17,0V24.484a1.355,1.355,0,0,1-2.4.817l-2.692-3.5A3.359,3.359,0,0,0,46.5,20.526Z" transform="translate(-43)"></path>
                    </svg>
                    {{ trans('main.dontClose') }}
                </div>
                <form class="completeJob" action="{{ URL::to('/completeJob') }}" method="post">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    @elseif(Session::has('invoice_id') && Session::get('invoice_id') != 0)
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="timer timer2">
                <img src="{{ asset('V5/images/checkImg.png') }}" alt="">
                <div class="desc">
                    {{ trans('main.resubscribe_p') }}
                </div>
                <div class="totalConfirm">
                    <center>
                        <div class="nextPrev clearfix">
                            <a href="{{ URL::to('/invoices/view/'.Session::get('invoice_id')) }}" class="btnNext">{{ trans('main.resubscribe_b1') }}</a>
                            <a href="{{ URL::to('/updateSubscription?type=new') }}" class="btnNext">{{ trans('main.resubscribe_b2') }}</a>
                        </div>
                    </center>
                </div>
            </div> 
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="row">
                <div class="col-md-6">
                    <div class="itemStats">
                        <h2 class="title">{{ trans('main.sendStatus') }}</h2>
                        <div data-size="85" val-circle="{{ $data->sendStatus/100 }}" data-fill="{&quot;color&quot;: &quot;#00c5bb&quot;}" class="circle circle1">
                          <strong>{{ $data->sendStatus }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="itemStats">
                        <h2 class="title">{{ trans('main.serverStatus') }}</h2>
                        <div data-size="85"  data-fill="{&quot;color&quot;: &quot;#00c5bb&quot;}" class="circle circle2">
                          <strong>{{ $data->serverStatus }}</strong>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="itemStats color1">
                        <h2 class="title">{{ trans('main.dialogs') }}</h2>
                        <span class="numb">{{ $data->allDialogs }}</span>
                        <i class="icon flaticon-email-1"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="itemStats color2">
                        <h2 class="title">{{ trans('main.contacts') }}</h2>
                        <span class="numb">{{ $data->contactsCount }}</span>
                        <i class="icon flaticon-users"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="itemStats color3">
                        <h2 class="title">{{ trans('main.sentMessages') }}</h2>
                        <span class="numb">{{ $data->sentMessages }}</span>
                        <i class="icon flaticon-paper-plane"></i>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="itemStats color4">
                        <h2 class="title">{{ trans('main.incomeMessages') }}</h2>
                        <span class="numb">{{ $data->incomingMessages }}</span>
                        <i class="icon flaticon-reply"></i>
                    </div>
                </div>
                
            </div>
        
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="lastNumbers">
                <h2 class="title">{{ trans('main.lastContactsAdded') }}</h2>
                <ul class="listNumbers">
                    @foreach($data->lastContacts as $contact)
                    <li>
                        <i class="icon flaticon-user-3"></i>
                        <h3 class="titleNumb">{{ $contact->name }}</h3>
                        <a href="#" class="numbStyle"><i class="flaticon-phone-call"></i> {{ $contact->phone2 }}</a>
                        @if( $contact->created_at2[0] != '1970-01-01')
                            <span class="date">{{ $contact->created_at2[0] }}</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
    
    <div class="msgArchive">
        <h2 class="title">{{ trans('main.msgsArchive') }}</h2>
        <div class="overflowTable">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>{{ trans('main.dialog') }}</th>
                        <th width="50%">{{ trans('main.messageContent') }}</th>
                        <th>{{ trans('main.status') }}</th>
                        <th>{{ trans('main.extra_type') }}</th>
                        <th>{{ trans('main.sentDate') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->data as $message)
                    <tr>
                        <td><a href="#" class="numbStyle"><i class="flaticon-phone-call"></i> {{ $message->chatId2 }}</a></td>
                        <td style="white-space: pre-line;">
                            @if($message->body != null && (strpos(' https',ltrim($message->body)) !== false || filter_var(trim($message->body), FILTER_VALIDATE_URL)))
                            📷
                            @else
                            @if($message->whatsAppMessageType == 'vcard')
                            <p>{{ $message->contact_name }}</p>
                            <p>{{ $message->contact_number }}</p>
                            @else
                            {{$message->body}}
                            @endif
                            @endif
                        </td>
                        <td>{{ $message->sending_status_text }}</td>
                        <td>
                            @if($message->fromMe)
                                <i class="type flaticon-share"></i>
                            @else
                                <i class="type color1 flaticon-share"></i>
                            @endif 
                        </td>
                        <td class="date" dir="{{ DIRECTION }}">{{ $message->created_at_day }}  {{ $message->created_at_time }} <i class="flaticon-calendar"></i></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

  
    @if(!empty($data->logs))
    <div class="lastNumbers">
        <h2 class="title">{{ trans('main.activityLog') }}</h2>
        <ul class="listNumbers">
            @foreach($data->logs as $log)
            <li>
                {{-- <i class="icon flaticon-user-3"></i> --}}
                <img class="icon" src="{{ $log->userImage }}" alt="">
                <h3 class="titleNumb">{{ $log->user }} <span>{{ $log->typeText }}</span></h3>
                <a href="#" class="numbStyle">
                    <i>
                        <svg id="Group_1360" data-name="Group 1360" xmlns="http://www.w3.org/2000/svg" width="14" height="12.398" viewBox="0 0 14 12.398">
                          <g id="Group_1359" data-name="Group 1359" transform="translate(0 0)">
                            <path id="Path_911" data-name="Path 911" d="M11.926,29.592A7.449,7.449,0,0,0,7,27.8a7.432,7.432,0,0,0-4.926,1.789A5.828,5.828,0,0,0,0,34,5.752,5.752,0,0,0,1.354,37.66a2.047,2.047,0,0,1-.7.8.694.694,0,0,0,.282,1.27,4.367,4.367,0,0,0,2.814-.519.519.519,0,1,0-.533-.89,3.038,3.038,0,0,1-1.325.406,3.258,3.258,0,0,0,.556-.979.522.522,0,0,0-.1-.533A4.668,4.668,0,0,1,1.037,34c0-2.846,2.676-5.165,5.963-5.165S12.963,31.15,12.963,34,10.287,39.161,7,39.161a6.765,6.765,0,0,1-1.412-.147.518.518,0,0,0-.216,1.014A7.863,7.863,0,0,0,7,40.2a7.449,7.449,0,0,0,4.926-1.792,5.72,5.72,0,0,0,0-8.815Z" transform="translate(0 -27.8)" fill="#777"/>
                          </g>
                        </svg>
                    </i> {{ $log->chatId2 }}</a>
                <span class="date">{{ $log->created_at2 }}</span>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @endif            
</div>
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('V5/components/newPackage.js') }}" type="text/javascript"></script>
@if(Session::has('hasJob') && Session::get('hasJob') == 1)
    <script src="{{ asset('components/countDown.js') }}"></script>
@endif
@endsection