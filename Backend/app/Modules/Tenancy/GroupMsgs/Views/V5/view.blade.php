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
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row stats">    
        <div class="col-md-3">
            <div class="itemStats color1">
                <h2 class="title">{{ trans('main.msgs_no') }}</h2>
                <span class="numb">{{ $data->data->messages_count }}</span>
                <i class="icon flaticon-email-1"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="itemStats color2">
                <h2 class="title">{{ trans('main.contacts_count') }}</h2>
                <span class="numb">{{ $data->data->contacts_count }}</span>
                <i class="icon flaticon-users"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="itemStats color3">
                <h2 class="title">{{ trans('main.sent_msgs') }}</h2>
                <span class="numb">{{ $data->data->sent_msgs }}</span>
                <i class="icon flaticon-paper-plane"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="itemStats color4">
                <h2 class="title">{{ trans('main.unsent_msgs') }}</h2>
                <span class="numb">{{ $data->data->unsent_msgs }}</span>
                <i class="icon flaticon-reply"></i>
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
                                <input disabled name="name_ar" value="{{ $data->data->sent_msgs > 0 ? trans('main.sent') : $data->data->sent_type }}">
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.sender') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled name="name_ar" value="{{ $data->data->creator }}">
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.sentDate') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled name="name_ar" value="{{ $data->data->publish_at2 }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.message_content') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea disabled name="name_ar">{{ $data->data->message }}</textarea>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.recipients') }} :</label>
                            </div>
                            <div class="col-md-9 cards" style="overflow-y: scroll;">
                                <div class="row mb-1">
                                    <div class="col-md-8">{{ trans('main.phone') }}</div>      
                                    <div class="col-md-4">{{ trans('main.status') }}</div>      
                                </div>
                                @foreach($data->contacts as $contact)
                                <div class="card mb-1">
                                    <div class="card-body cont-card">
                                        <div class="row">
                                            <div class="col-md-8 text-left" dir="ltr">{{ $contact->phone }}</div>
                                            <div class="col-md-4"><span class="badge badge-{{ $contact->reportStatus[0] }}">{{ $contact->reportStatus[1] }}</span></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
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
                                <div class="message received" style="margin-top: 70px;">{{ $data->data->message }}</div>
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
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('topScripts')
<script src="{{ asset('components/phone.js') }}"></script>
<script src="{{ asset('components/addMsg.js') }}"></script>
@endsection