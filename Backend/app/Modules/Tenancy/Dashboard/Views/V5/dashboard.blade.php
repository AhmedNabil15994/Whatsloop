@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.dashboard'))
@section('styles')
@endsection

{{-- Content --}}
@section('content')
<div class="stats">
    @if(Session::has('hasJob') && Session::get('hasJob') == 1)
        <div class="row text-center mg-t-100 mg-b-20 d-block">
            {{-- <div class="col-3"></div> --}}
            <div class="col-12 w-auto m-auto d-block">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('images/waiting.svg') }}" class="transferSVG" alt="">
                        <h2 class="header-title h2 tx-bold mg-b-40">{{ trans('main.inPrgo') }} <span class="mCounter" dir="ltr">05:00</span></h2>
                        <p class="h3 mg-b-50 text-muted tx-bold"> <span class="tx-black">{{ trans('main.preparingAccount') }}</span></p>
                        <p class="h3 text-muted tx-bold"><span class="tx-black" dir="ltr">{{ trans('main.dontClose') }}</span></p>
                    </div>
                    <form class="completeJob" action="{{ URL::to('/completeJob') }}" method="post">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    @elseif(Session::has('invoice_id') && Session::get('invoice_id') != 0)
        <div class="row text-center mg-t-100 mg-b-20 d-block">
            {{-- <div class="col-3"></div> --}}
            <div class="col-12 w-auto m-auto d-block">
                <div class="card">
                    <div class="card-body">
                        <img src="{{ asset('images/waiting.svg') }}" class="transferSVG" alt="">
                        <p class="h3 mg-b-50 text-muted tx-bold"> <span class="tx-black">{{ trans('main.resubscribe_p') }}</span></p>
                        <a href="{{ URL::to('/invoices/view/'.Session::get('invoice_id')) }}" class="btn w-auto mg-auto btn-success tx-white btn-md">{{ trans('main.resubscribe_b1') }}</a>
                        <a href="{{ URL::to('/profile/subscription') }}" class="btn w-auto mg-auto btn-primary tx-white btn-md">{{ trans('main.resubscribe_b2') }}</a>
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
                            <div data-value="0.0" data-size="85" data-value="{{ $data->sendStatus/100 }}" data-fill="{&quot;color&quot;: &quot;#00c5bb&quot;}" class="circle circle1">
                              <strong>{{ $data->sendStatus }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="itemStats">
                            <h2 class="title">{{ trans('main.serverStatus') }}</h2>
                            <div data-size="85" data-value="{{ $data->serverStatus/100 }}" data-fill="{&quot;color&quot;: &quot;#00c5bb&quot;}" class="circle circle2">
                              <strong>{{ $data->serverStatus }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="itemStats color1">
                            <h2 class="title">{{ trans('main.messages') }}</h2>
                            <span class="numb">{{ $data->allMessages }}</span>
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
                            <a href="#" class="numbStyle"><i class="flaticon-phone-call"></i> {{ $contact->phone }}</a>
                            <span class="date">{{ $contact->created_at2[0] }}</span>
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
                            <td style="white-space: pre-line;">{{ $message->body }}</td>
                            <td>{{ $message->sending_status_text }}</td>
                            <td>
                                @if($message->fromMe)
                                    <i class="type flaticon-share"></i>
                                @else
                                    <i class="type color1 flaticon-share"></i>
                                @endif 
                            </td>
                            <td class="date">{{ $message->created_at_day }}  {{ $message->created_at_time }} <i class="flaticon-calendar"></i></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @include('tenant.Partials.pagination')
        </div>
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
</div>
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('V5/components/newPackage.js') }}" type="text/javascript"></script>
@endsection
