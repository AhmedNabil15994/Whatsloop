{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .user-langs{
        background-color: unset;
    }
    .mb-1{
        margin-bottom: 5px;
    }
    p.label-darks{
        padding: 20px !important;
        display: block;
        background: #F6CD02;
        color: #000;
    }
</style>
@endsection
@section('content')
<select name="bots" class="hidden">
    @foreach($data->bots as $bot)
    <option value="{{ $bot->id }}" data-type="1">{{ trans('main.clientMessage') . ' ( ' .$bot->message . ' ) ==== ' . trans('main.bot') }}</option>
    @endforeach
    @foreach($data->botPlus as $plusBot)
    <option value="{{ $plusBot->id }}" data-type="2">{{ trans('main.clientMessage') . ' ( ' .$plusBot->message . ' ) ==== ' . trans('main.botPlus') }}</option>
    @endforeach
</select>
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                            <p class="label label-darks text-left">{{ trans('main.botPlusNote') }}</p>
                        </div>
                    </div>
                    <form class="formPayment" method="POST" action="{{ URL::to('/botPlus/create') }}">
                        @csrf
                        <input type="hidden" name="status">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.messageType') }} :</label>                            
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="message_type">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="1" {{ old('message_type') == 1 ? 'selected' : '' }}>{{ trans('main.equal') }}</option>
                                        <option value="2" {{ old('message_type') == 2 ? 'selected' : '' }}>{{ trans('main.part') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.clientMessage') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('message') }}" name="message" placeholder="{{ trans('main.clientMessage') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.title') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('title') }}" name="title" placeholder="{{ trans('main.title') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.body') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <textarea name="body" placeholder="{{ trans('main.body') }}">{{ old('body') }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.footer') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('footer') }}" name="footer" placeholder="{{ trans('main.footer') }}">
                            </div>
                        </div>
                        <div class="row">
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
                                        <option value="{{ $label->id }}" {{ $label->id == old('category_id') ? 'selected' : '' }}>{{ $label->title }}</option>
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
                                        <option value="{{ $mod->id }}" {{ $mod->id == old('moderator_id') ? 'selected' : '' }}>{{ $mod->name }}</option>
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
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
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
                            <div class="conversation-container clearfix">    
                                @foreach($data->botPlus as $bot)                                    
                                <div class="message received">
                                    {{ $bot->message }}
                                    @if(\Helper::checkRules('edit-bot-plus'))
                                    <a href="{{ URL::to('/botPlus/edit/'.$bot->id) }}" class="btn btn-xs btn-primary btn-block">{{ trans('main.edit') }}</a>
                                    @endif
                                    @if(\Helper::checkRules('copy-bot-plus'))
                                    <a href="{{ URL::to('/botPlus/copy/'.$bot->id) }}" class="btn btn-xs btn-warning btn-block">{{ trans('main.repeat') }}</a>
                                    @endif
                                </div>
                                <div class="message sent" style="white-space: pre-line;text-align:right;">
                                    <p>{!! rtrim($bot->title) !!}</p>
                                    <p>{!! rtrim($bot->body) !!}</p>
                                    <p>{!! rtrim($bot->footer) !!}</p>
                                    @foreach($bot->buttonsData as $oneItem)
                                    <a href="#" class="btn mb-1 btn-xs btn-primary btn-inline">{{ $oneItem['text'] }}</a>
                                    @endforeach
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
<script src="{{ asset('V5/components/phone.js') }}"></script>
<script src="{{ asset('V5/components/addBotPlus.js') }}"></script>
@endsection