{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
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
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/bots/create') }}">
                        @csrf
                        <input type="hidden" name="status">
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.messageType') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="message_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ old('message_type') == 1 ? 'selected' : '' }}>{{ trans('main.equal') }}</option>
                                    <option value="2" {{ old('message_type') == 2 ? 'selected' : '' }}>{{ trans('main.part') }}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.clientMessage') }} :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ old('message') }}" name="message" placeholder="{{ trans('main.clientMessage') }}">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.replyType') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="reply_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ old('reply_type') == 1 ? 'selected' : '' }}>{{ trans('main.text') }}</option>
                                    <option value="2" {{ old('reply_type') == 2 ? 'selected' : '' }}>{{ trans('main.photoOrFile') }}</option>
                                    <option value="3" {{ old('reply_type') == 3 ? 'selected' : '' }}>{{ trans('main.video') }}</option>
                                    <option value="4" {{ old('reply_type') == 4 ? 'selected' : '' }}>{{ trans('main.sound') }}</option>
                                    <option value="5" {{ old('reply_type') == 5 ? 'selected' : '' }}>{{ trans('main.link') }}</option>
                                    <option value="6" {{ old('reply_type') == 6 ? 'selected' : '' }}>{{ trans('main.whatsappNos') }}</option>
                                    <option value="7" {{ old('reply_type') == 7 ? 'selected' : '' }}>{{ trans('main.mapLocation') }}</option>
                                    <option value="8" {{ old('reply_type') == 8 ? 'selected' : '' }}>{{ trans('main.webhook') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label" for="lang"> {{ trans('main.lang') }}</label>
                            <div class="col-md-9">
                                <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="lang">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="0">{{ trans('main.arabic') }}</option>
                                    <option value="1">{{ trans('main.english') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="reply" data-id="1">
                            <div class="form-group row mb-3 hidden">
                                <label class="col-3 col-form-label">{{ trans('main.messageContent') }} :</label>
                                <div class="col-9">
                                    <textarea name="replyText" class="form-control" placeholder="{{ trans('main.messageContent') }}">{{ old('reply') }}</textarea>
                                </div>
                            </div> 
                        </div>
                        <div class="reply" data-id="2">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.textWithPhoto') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('reply') }}" name="reply" placeholder="{{ trans('main.textWithPhoto') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachFile') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 si si-cloud-upload"></i>
                                            <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="3">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachFile') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                    <div class="d-none" id="uploadPreviewTemplate">
                                        <div class="card mt-1 mb-0 shadow-none border">
                                            <div class="p-2">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <img data-dz-thumbnail="" src="#" class="avatar-sm rounded bg-light" alt="">
                                                    </div>
                                                    <div class="col pl-0">
                                                        <a href="javascript:void(0);" class="text-muted font-weight-bold" data-dz-name=""></a>
                                                        <p class="mb-0" data-dz-size=""></p>
                                                    </div>
                                                    <div class="col-auto">
                                                        <!-- Button -->
                                                        <a href="" class="btn btn-link btn-lg text-muted" data-dz-remove="">
                                                            <i class="dripicons-cross"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="4">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.attachFile') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="5">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.url') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('https_url') }}" name="https_url" placeholder="{{ trans('main.url') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.urlTitle') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('url_title') }}" name="url_title" placeholder="{{ trans('main.urlTitle') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.urlDesc') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('url_desc') }}" name="url_desc" placeholder="{{ trans('main.urlDesc') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.urlImage') }} :</label>
                                <div class="col-9">
                                    <div class="dropzone kt_dropzone_1">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="6">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.whatsappNo') }} :</label>
                                <div class="col-9">
                                    <input type="tel" class="form-control teles" value="{{ old('whatsapp_no') }}" name="whatsapp_no" placeholder="{{ trans('main.whatsappNo') }}">
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="7">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.lat') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('lat') }}" name="lat" placeholder="{{ trans('main.lat') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.lng') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('lng') }}" name="lng" placeholder="{{ trans('main.lng') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.location') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('address') }}" name="address" placeholder="{{ trans('main.location') }}">
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="8">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.webhookURL') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('webhook_url') }}" name="webhook_url" placeholder="{{ trans('main.webhookURL') }}">
                                </div>
                            </div>
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.sentTemplates') }} :</label>
                                <div class="col-9">
                                    <div class="row mb-2 ml-2 mr-2">
                                        @foreach($data->templates as $template)
                                        <div class="checkbox checkbox-blue checkbox-single float-left">
                                            <input type="checkbox" name="templates[]" value="{{ $template->id }}">
                                            <label></label>
                                            {{ $template->title }}
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <button type="button" class="btn label label-light-info SelectAllCheckBox ml-2 mr-2">{{ trans('main.selectAll') }}</button>
                                            <button type="button" class="btn label label-light-danger UnSelectAllCheckBox">{{ trans('main.deselectAll') }}</button>
                                        </div>            
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9 text-right">
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset float-left">{{ trans('main.back') }}</a>
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
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
                            <div class="conversation-container clearfix">    
                                @foreach($data->bots as $bot)                                    
                                <div class="message received">
                                    {{ $bot->message }}
                                    <span class="metadata">
                                        <span class="time">{{ trans('main.now') }}</span>
                                    </span>
                                    @if(\Helper::checkRules('edit-bot'))
                                    <a href="{{ URL::to('/bots/edit/'.$bot->id) }}" class="btn btn-xs btn-primary btn-inline">{{ trans('main.edit') }}</a>
                                    @endif
                                    @if(\Helper::checkRules('copy-bot'))
                                    <a href="{{ URL::to('/bots/copy/'.$bot->id) }}" class="btn btn-xs btn-warning btn-inline">{{ trans('main.repeat') }}</a>
                                    @endif
                                </div>
                                <div class="message sent" style="white-space: pre-line;text-align:right;">
                                    @if($bot->reply_type == 1)
                                    {!! rtrim($bot->reply2) !!}
                                    @elseif($bot->reply_type == 2)
                                        @if($bot->file_type == 'image')
                                        <img class="mb-2" src="{{ $bot->file }}" alt="">
                                        {{ $bot->reply }}
                                        @else

                                        @endif
                                    @elseif($bot->reply_type == 3)
                                    <video style="width:250px;" controls="">
                                        <source src="{{ $bot->file }}" type="video/mp4">
                                    </video>
                                    @elseif($bot->reply_type == 4)
                                    <audio controls="">
                                        <source src="{{ $bot->file }}" type="audio/ogg">
                                    </audio>
                                    @elseif($bot->reply_type == 5)
                                        @if($bot->photo != "")
                                            <img class="mb-2" src="{{ $bot->photo }}" alt="">
                                        @endif
                                        {{ $bot->https_url }}
                                    @elseif($bot->reply_type == 6)
                                        {{ $bot->whatsapp_no }}
                                    @elseif($bot->reply_type == 7)
                                        <iframe class="mb-2" src = "https://maps.google.com/maps?q={{ $bot->lat }},{{ $bot->lng }}&hl=es;z=14&amp;output=embed" width="300" height="250"></iframe>
                                        {{ $bot->address }}
                                    @elseif($bot->reply_type == 8)
                                        {{ $bot->webhook_url }}
                                    @endif
                                    <span class="metadata mb-2">
                                        <span class="time">{{ trans('main.now') }} <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" id="msg-dblcheck-ack" x="2063" y="2076"><path d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z" fill="#4fc3f7"></path></svg></span>
                                    </span>
                                </div>
                                @endforeach
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
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('topScripts')
<script src="{{ asset('components/phone.js') }}"></script>
<script src="{{ asset('components/addBot.js') }}"></script>
@endsection