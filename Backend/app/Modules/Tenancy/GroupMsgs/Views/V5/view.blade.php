{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .check-title{
        margin-left: 25px;
        margin-right: 25px;
        margin-top: 15px;
    }
    .supportForm{
        padding: 20px;
    }
    .cards{
        max-height: 500px;
    }
    .nextPrev .btnNext.resend{
        width: auto;
    }
    .mainRow{
        border-bottom: 1px solid #F6F6F6;
    }
    html[dir="rtl"] .res{
        margin-top: 10px;
        padding-left: 30px;
    }
    html[dir="ltr"] .res{
        margin-top: 10px;
        padding-right: 30px;
    }
    .mt-5{
        margin-top: 50px;
    }
    .pagin{
        padding: 15px;
    }
    .received .btn-dark{
        width: 100%;
        margin-bottom: 5px;
    }
    .received b{
        display: block;
    }
</style>
@endsection
@section('content')

@if($data->checkAvailBotPlus == 1)
<div class="Additions">
    <h2 class="title">{{ trans('main.groupMsgNotify') }}</h2>
    <a href="#" class="btnAdd" style="visibility: hidden;"></a>
</div> 
@endif

<!-- Start Content-->
<div class="container-fluid">
    <div class="row stats">    
        <div class="col-md-3">
            <div class="itemStats color2">
                <h2 class="title">{{ trans('main.contacts_count') }}</h2>
                <span class="numb">{{ $data->msg->contacts_count }}</span>
                <i class="icon flaticon-users"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="itemStats color3">
                <h2 class="title">{{ trans('main.sent_msgs') }}</h2>
                <span class="numb">{{ $data->msg->sent_msgs }}</span>
                <i class="icon flaticon-paper-plane"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="itemStats color4">
                <h2 class="title">{{ trans('main.unsent_msgs') }}</h2>
                <span class="numb">{{ $data->msg->unsent_msgs }}</span>
                <i class="icon flaticon-reply"></i>
            </div>
        </div>
         <div class="col-md-3">
            <div class="itemStats color1">
                <h2 class="title">{{ trans('main.viewed_msgs') }}</h2>
                <span class="numb">{{ $data->msg->viewed_msgs }}</span>
                <i class="icon fa fa-eye"></i>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form">
                <div class="row">
                    <div class="col-xs-12">
                        <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="supportForm">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.status') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled name="name_ar" value="{{ $data->msg->sent_msgs > 0 ? trans('main.sent') : $data->msg->sent_type }}">
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.sender') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled name="name_ar" value="{{ $data->phone }}">
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.sentDate') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled name="name_ar" value="{{ $data->msg->publish_at2 }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.message_content') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea disabled name="name_ar">{{ $data->msg->message }}</textarea>
                            </div>
                        </div> 
                        <hr class="mt-5">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    @if(\Helper::checkRules('add-group-message'))
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/resend/'.$data->msg->id.'/1') }}" class="btn resend btnNext">{{ trans('main.resend') }}</a>
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
        <div class="col-md-4">
            <section class="iphoneMock">
              <div class="container">
                <div class="iphone initAnimation">
                  <div class="bordeColor">
                    <div class="botones">
                      <div class="switch"></div>
                      <div class="vol up"></div>
                      <div class="vol down"></div>
                      <div class="touchID"></div>
                    </div>
                    <div class="backSide">
                      <div class="camaras">
                        <div class="cam">
                          <div class="lente"></div>
                        </div>
                        <div class="cam">
                          <div class="lente"></div>
                        </div>
                        <div class="cam">
                          <div class="lente"></div>
                        </div>
                        <div class="flash"></div>
                        <div class="sensor"></div>
                      </div>
                      <div class="logo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                          <path d="M48.334 33.875c-.093-7.593 6.169-11.249 6.45-11.436a13.669 13.669 0 0 0-10.936-5.906c-4.674-.469-9.067 2.718-11.5 2.718-2.337 0-5.982-2.718-9.908-2.625a14.765 14.765 0 0 0-12.339 7.5C4.868 33.313 8.794 47 13.935 54.4c2.524 3.656 5.515 7.78 9.441 7.593 3.832-.187 5.235-2.437 9.815-2.437S39.08 62 43.1 61.9c4.113-.094 6.637-3.75 9.16-7.405a29.782 29.782 0 0 0 4.113-8.53 13.082 13.082 0 0 1-8.039-12.09z"></path>
                          <path d="M40.762 11.565A13.423 13.423 0 0 0 43.847 2a13.194 13.194 0 0 0-8.787 4.5c-1.963 2.25-3.645 5.812-3.178 9.28 3.365.284 6.824-1.68 8.88-4.215z"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="bordeNegro">
                      <div class="notch">
                        <div class="bocina"></div>
                        <div class="camara"></div>
                      </div>
                      <div class="logo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
                          <path d="M48.334 33.875c-.093-7.593 6.169-11.249 6.45-11.436a13.669 13.669 0 0 0-10.936-5.906c-4.674-.469-9.067 2.718-11.5 2.718-2.337 0-5.982-2.718-9.908-2.625a14.765 14.765 0 0 0-12.339 7.5C4.868 33.313 8.794 47 13.935 54.4c2.524 3.656 5.515 7.78 9.441 7.593 3.832-.187 5.235-2.437 9.815-2.437S39.08 62 43.1 61.9c4.113-.094 6.637-3.75 9.16-7.405a29.782 29.782 0 0 0 4.113-8.53 13.082 13.082 0 0 1-8.039-12.09z"></path>
                          <path d="M40.762 11.565A13.423 13.423 0 0 0 43.847 2a13.194 13.194 0 0 0-8.787 4.5c-1.963 2.25-3.645 5.812-3.178 9.28 3.365.284 6.824-1.68 8.88-4.215z"></path>
                        </svg>
                      </div>
                      <div class="mainScreen bloqueado">
                        <div class="statusBar">
                          <div class="leftSide">
                            <div class="operador">Telcel</div>
                            <div class="hora hidden"></div>
                            <div class="widgetPlus"></div>
                          </div>
                          <div class="rightSide">
                            <div class="signal mid"><i class="bar"></i></div>
                            <div class="datos">5G</div>
                            <div class="bateria mid"></div>
                            <div class="exitShake">Listo</div>
                          </div>
                        </div>
                        <div class="conversation">
                            <div class="conversation-container overflowY clearfix">    
                                @if(isset($data->botPlus->buttonsData) && $data->msg->bot_plus_id > 1 && $data->checkAvailBotPlus == 1)
                                <div class="message received" style="white-space: pre-line;text-align:right;">
                                    <b>{!! rtrim($data->botPlus->title) !!}</b>
                                    <p>{!! rtrim($data->botPlus->body) !!}</p>
                                    <b>{!! rtrim($data->botPlus->footer) !!}</b>
                                    @foreach($data->botPlus->buttonsData as $oneItem)
                                    <a href="#" class="btn btn-xs btn-dark">{{ $oneItem['text'] }}</a>
                                    @endforeach
                                    <span class="metadata mb-2">
                                        <span class="time">{{ trans('main.now') }} <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"></path></svg></span>
                                    </span>
                                </div>
                                @else
                                <div class="message received pre-space break-space" style="margin-top: 70px;">{{ $data->msg->message }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="phone-footer">
                            <img src="{{ asset('images/bg-tg-bot-campaign-bottom.png') }}" alt="">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
        </div> 
    </div>
    <div class="mt-5"></div>
    <div class="row">
        <div class="col transmitters bill">
            <div class="form">
                <div class="row mainRow">
                    <div class="col-xs-6">
                        <h2 class="title">{{ trans('main.recipients') }}</h2>                
                    </div>
                    <div class="col-xs-6 text-right res">
                        <div class="nextPrev clearfix ">
                            <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/resend/'.$data->msg->id.'/2') }}" class="btn mt-2 resend btnNext">{{ trans('main.resendUnsent') }}</a>   
                            <a href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/refresh/'.$data->msg->id) }}" class="btn mt-2 resend btnNext">{{ trans('main.refresh2') }}</a>   
                        </div>         
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="overflowTable">
                        <table class="tableBills table table-striped  dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>{{ trans('main.id') }}</th>
                                    <th>{{ trans('main.phone') }}</th>
                                    <th>{{ trans('main.status') }}</th>
                                    <th>{{ trans('main.date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data->data as $key => $contact)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{$contact->phone}}</td>
                                    <td>
                                        <span class="badge badge-{{ $contact->reportStatus[0] }}">{{ $contact->reportStatus[1] }}</span>
                                    </td>
                                    <td>{{ $contact->reportStatus[2] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('tenant.Partials.pagination')
            </div>
        </div>
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('topScripts')
<script src="{{ asset('components/phone.js') }}"></script>
<script src="{{ asset('components/addMsg.js') }}"></script>
@endsection