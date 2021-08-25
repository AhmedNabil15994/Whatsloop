{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .check-title{
        margin-left: 25px;
        margin-right: 25px;
        margin-top: 15px;
    }
    .cont-card{
        padding: .6rem;
    }
    .cards{
        max-height: 500px;
        /*overflow-y: scroll;*/
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
 
    <div class="row mb-5">
        <div class="col-xl-3 col-md-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <i class="fas fa-comments project bg-primary-transparent mx-auto text-primary "></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.msgs_no') }}</h6>
                        <h2 class="mb-2">{{ $data->data->messages_count * $data->data->contacts_count }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <i class="fas fa-address-book project bg-warning-transparent mx-auto text-warning "></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.contacts_count') }}</h6>
                        <h2 class="mb-2">{{ $data->data->contacts_count }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <i class="fe fe-user-check project bg-success-transparent mx-auto text-success "></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.sent_msgs') }}</h6>
                        <h2 class="mb-2">{{ $data->data->sent_msgs }}</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="feature widget-2 text-center mt-0 mb-3">
                        <i class="fe fe-user-x  project bg-danger-transparent mx-auto text-danger"></i>
                        <h6 class="text-drak text-uppercase mt-2">{{ trans('main.unsent_msgs') }}</h6>
                        <h2 class="mb-2">{{ $data->data->unsent_msgs }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>   
    <!-- end page title --> 

    <div class="row">
        <div class="col-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row mains">
                        <label class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                        <div class="col-9">
                            <input class="form-control" disabled name="name_ar" value="{{ $data->data->sent_type }}">
                        </div>
                    </div> 
                    <div class="form-group row mains">
                        <label class="col-3 col-form-label">{{ trans('main.sender') }} :</label>
                        <div class="col-9">
                            <input class="form-control" disabled name="name_ar" value="{{ $data->data->creator }}">
                        </div>
                    </div> 
                    <div class="form-group row mains">
                        <label class="col-3 col-form-label">{{ trans('main.sentDate') }} :</label>
                        <div class="col-9">
                            <input class="form-control" disabled name="name_ar" value="{{ $data->data->publish_at2 }}">
                        </div>
                    </div>
                    <div class="form-group row mains">
                        <label class="col-3 col-form-label">{{ trans('main.message_content') }} :</label>
                        <div class="col-9">
                            <input class="form-control" disabled name="name_ar" value="{{ $data->data->message }}">
                        </div>
                    </div> 
                    <div class="form-group row mains">
                        <label class="col-3 col-form-label">{{ trans('main.recipients') }} :</label>
                        <div class="col-9 cards" style="overflow-y: scroll;">
                            <div class="row mb-2">
                                <div class="col-8">{{ trans('main.phone') }}</div>      
                                <div class="col-4">{{ trans('main.status') }}</div>      
                            </div>
                            @foreach($data->contacts as $contact)
                            <div class="card mb-1">
                                <div class="card-body cont-card">
                                    <div class="row">
                                        <div class="col-8" dir="ltr">{{ $contact->phone }}</div>
                                        <div class="col-4"><span class="badge badge-{{ $contact->reportStatus[0] }}">{{ $contact->reportStatus[1] }}</span></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div> 
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
        <div class="col-4">
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