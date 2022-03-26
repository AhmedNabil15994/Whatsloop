{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .user-langs{
        background-color: unset;
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
    <h2 class="title">{{ trans('main.templatesNotify') }}</h2>
    <a href="#" class="btnAdd" style="visibility: hidden;"></a>
</div> 

<select name="bots" class="hidden">
    @if($data->checkAvailBot == 1)
    @foreach($data->bots as $bot)
    <option value="{{ $bot->id }}" data-type="1">{{ trans('main.clientMessage') . ' ( ' .$bot->message . ' ) ==== ' . trans('main.bot') }}</option>
    @endforeach
    @endif

    @foreach($data->botPluss as $plusBot)
    <option value="{{ $plusBot->id }}" data-type="2">{{ trans('main.clientMessage') . ' ( ' .$plusBot->message . ' ) ==== ' . trans('main.botPlus') }}</option>
    @endforeach
</select>

<select name="statuses" class="hidden">
    @foreach($data->statuses as $status)
    <option value="{{ $status['id'] }}">{{ $status['name'] }}</option>
    @endforeach
</select>
@endif

<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <form class="formPayment grpmsg" method="POST" action="{{ URL::to('/services/'.$data->designElems['mainData']['service'].'/templates/update/'.$data->data->id) }}">
                        @csrf 
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.status') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" readonly name="statusText">
                                        <option value="{{ $data->data->statusText }}" selected>{{ $data->data->statusText }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.type') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" readonly name="status">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="0" {{ $data->data->status == 0 ? 'selected' : '' }}>{{ trans('main.notActive') }}</option>
                                        <option value="1" {{ $data->data->status == 1 ? 'selected' : '' }}>{{ trans('main.active') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        @if($data->data->statusText == 'مسترجع' && $data->data->mod_id == 1)
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.shipmentPolicy') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" readonly name="shipment_policy">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="0" {{ $data->data->shipment_policy == 0 || $data->data->shipment_policy == null ? 'selected' : '' }}>{{ trans('main.notActive') }}</option>
                                        <option value="1" {{ $data->data->shipment_policy == 1 ? 'selected' : '' }}>{{ trans('main.active') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        @endif
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.extra_type') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="type">
                                        <option value="1" {{ $data->data->type == 1 ? 'selected' : '' }}>{{ trans('main.text') }}</option>
                                        @if($data->checkAvailBotPlus == 1)
                                        <option value="2" {{ $data->data->type >  1 ? 'selected' : '' }}>{{ trans('main.botPlus') }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="form1 {{ $data->data->type == 1 ? '' : 'hidden' }}">
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.message_content') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="content_ar" placeholder="{{ trans('main.content_ar') }}">{{ $data->data->content_ar }}</textarea>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.content_en') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="content_en" placeholder="{{ trans('main.content_en') }}">{{ $data->data->content_en }}</textarea>
                                </div>
                            </div> 
                        </div>
                        <div class="form2 {{$data->data->type > 1 ? '' : 'hidden'}}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="inputPassword3" class="titleLabel">{{ trans('main.title') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" value="{{ isset($data->botPlus->title) ? $data->botPlus->title : '' }}" name="title" placeholder="{{ trans('main.title') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="inputPassword3" class="titleLabel">{{ trans('main.body') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="body" placeholder="{{ trans('main.body') }}">{{ isset($data->botPlus->body) ? $data->botPlus->body : '' }}</textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="inputPassword3" class="titleLabel">{{ trans('main.footer') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" value="{{ isset($data->botPlus->footer) ? $data->botPlus->footer : '' }}" name="footer" placeholder="{{ trans('main.footer') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="inputPassword3" class="titleLabel">{{ trans('main.buttons') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="buttons">
                                        <option value="1" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 1 ? 'selected' : '' : '' }}>1</option>
                                        <option value="2" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 2 ? 'selected' : '' : '' }}>2</option>
                                        <option value="3" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 3 ? 'selected' : '' : '' }}>3</option>
                                        <option value="4" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 4 ? 'selected' : '' : '' }}>4</option>
                                        <option value="5" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 5 ? 'selected' : '' : '' }}>5</option>
                                        <option value="6" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 6 ? 'selected' : '' : '' }}>6</option>
                                        <option value="7" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 7 ? 'selected' : '' : '' }}>7</option>
                                        <option value="8" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 8 ? 'selected' : '' : '' }}>8</option>
                                        <option value="9" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 9 ? 'selected' : '' : '' }}>9</option>
                                        <option value="10" {{ isset($data->botPlus->buttons) ? $data->botPlus->buttons == 10 ? 'selected' : '' : '' }}>10</option>
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                                <div class="buts">
                                    @if(isset($data->botPlus->buttonsData) && $data->data->type > 1 && $data->checkAvailBotPlus == 1)
                                    @foreach($data->botPlus->buttonsData as $oneItem)
                                    <div class='row mains'>
                                        <div class='col-md-3'>
                                            <label class='titleLabel'>{{ trans('main.btnData',['button'=>$oneItem['id']]) }} :</label>
                                        </div>
                                        <div class='col-md-9'>
                                            <div class='row'>
                                                <div class='col-md-4'>
                                                    <input type='text' name='btn_text_{{ $oneItem['id'] }}' value="{{ $oneItem['text'] }}" placeholder='{{ trans('main.text') }}'>
                                                </div>
                                                <div class='col-md-4'>
                                                    <select data-toggle='select2' class='reply_types' name='btn_reply_type_{{ $oneItem['id'] }}'>
                                                        <option value='1' {{ $oneItem['reply_type'] == 1 ? 'selected' : '' }}>{{ trans('main.newReply') }}</option>
                                                        <option value='2' {{ $oneItem['reply_type'] == 2 ? 'selected' : '' }}>{{ trans('main.botMsg') }}</option>
                                                        <option value='3' {{ $oneItem['reply_type'] == 3 ? 'selected' : '' }}>{{ trans('main.changeOrderStatusTo') }}</option>
                                                    </select>
                                                </div>
                                                <div class='col-md-4 repy'>
                                                    <textarea class="{{ $oneItem['msg_type'] == 0 && $oneItem['reply_type'] != 3 ? '' : 'hidden'  }}" name='btn_reply_{{ $oneItem['id'] }}' placeholder='{{ trans('main.messageContent') }}'>{{ $oneItem['msg_type'] == 0 && $oneItem['reply_type'] != 3 ? $oneItem['msg'] : ''  }}</textarea>
                                                    <select data-toggle="{{ in_array($oneItem['msg_type'],[1,2]) ? 'select2' : ''  }}" class='dets select1s {{ in_array($oneItem['msg_type'],[1,2]) ? '' : 'hidden'  }}' name='btn_msg_{{ $oneItem['id'] }}'>
                                                        <option value='' selected>{{ trans('main.choose') }}</optin>
                                                        @foreach($data->bots as $bot)
                                                        <option value="{{ $bot->id }}" data-type="1" {{ $oneItem['msg_type'] == 1 && isset($oneItem['msg']) && $oneItem['msg'] == $bot->id ? 'selected' : '' }}>{{ trans('main.clientMessage') . ' ( ' .$bot->message . ' ) ==== ' . trans('main.bot') }}</option>
                                                        @endforeach
                                                        @foreach($data->botPluss as $plusBot)
                                                        @if($plusBot->id != $data->data->id)
                                                        <option value="{{ $plusBot->id }}" data-type="2" {{ $oneItem['msg_type'] == 2 && isset($oneItem['msg'])  && $oneItem['msg'] == $plusBot->id ? 'selected' : '' }}>{{ trans('main.clientMessage') . ' ( ' .$plusBot->message . ' ) ==== ' . trans('main.botPlus') }}</option>
                                                        @endif
                                                        @endforeach
                                                    </select>

                                                    <select data-toggle="{{ $oneItem['reply_type'] == 3 ? 'select2' : ''  }}" class='dets select2s {{ $oneItem['reply_type'] == 3 ? '' : 'hidden'  }}' name='btn_msgs_{{ $oneItem['id'] }}'>
                                                        <option value='' {{ !isset($oneItem['msg']) ? 'selected' : '' }}>{{ trans('main.choose') }}</optin>
                                                        @foreach($data->statuses as $status)
                                                        <option value="{{ $status['id'] }}" data-type="3" {{ $oneItem['reply_type'] == 3 && isset($oneItem['msg']) && $oneItem['msg'] == $status['id'] ? 'selected' : '' }}>{{ $status['name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type='hidden' name='btn_msg_type_{{ $oneItem['id'] }}' value='{{ $oneItem['msg_type'] }}'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <h4 class="title"> {{ trans('main.actions') }}</h4>
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.assignLabel') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="category_id">
                                        <option value="" >{{ trans('main.categories') }}</option>
                                        @foreach($data->labels as $label)
                                        <option value="{{ $label->id }}" {{ $label->id == $data->data->category_id ? 'selected' : '' }}>{{ $label->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.assignMod') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="moderator_id">
                                        <option value="" >{{ trans('main.mods') }}</option>
                                        @foreach($data->mods as $mod)
                                        <option value="{{ $mod->id }}" {{ $mod->id == $data->data->moderator_id ? 'selected' : '' }}>{{ $mod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <hr class="mt-5">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
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
                                @if(isset($data->botPlus->buttonsData) && $data->data->type > 1 && $data->checkAvailBotPlus == 1)
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
                                <div class="message received pre-space break-space" style="margin-top: 70px;">{{ $data->data->content }}</div>
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
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('topScripts')
<script src="{{ asset('V5/components/phone.js') }}"></script>
<script src="{{ asset('V5/components/templatesEdit.js') }}"></script>
@endsection