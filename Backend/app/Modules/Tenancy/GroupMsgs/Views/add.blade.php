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
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-11">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>
        <div class="col-1 text-right">
            <div class="btn-group dropleft mb-3 mt-2">
                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog"></i>
                </button>
                <div class="dropdown-menu">
                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}"><i class="fa fa-plus"></i> {{ trans('main.add') }}</a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/charts') }}"><i class="fas fa-chart-bar"></i> {{ trans('main.charts') }}</a>
                    @endif
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
                    <form class="form-horizontal grpmsg" method="POST" action="{{ URL::to('/groupMsgs/create') }}">
                        @csrf
                        <input type="hidden" name="status">
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.group') }} :</label>
                            <div class="col-9">
                                <select class="selectpicker" data-style="btn-outline-myPR" name="group_id">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>{{ $group->channel . ' - '.$group->title }}</option>
                                    @endforeach
                                    <option value="@">{{ trans('main.add') }}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row mains hidden">
                            <label class="col-3 col-form-label">{{ trans('main.name_ar') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="name_ar" placeholder="{{ trans('main.name_ar') }}">
                            </div>
                        </div> 
                        <div class="form-group row mains hidden">
                            <label class="col-3 col-form-label">{{ trans('main.name_en') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="name_en" placeholder="{{ trans('main.name_en') }}">
                            </div>
                        </div> 
                        <div class="form-group row mains mb-3 hidden">
                            <label class="col-3 col-form-label">{{ trans('main.whatsappNos') }} :</label>
                            <div class="col-9">
                                <textarea class="form-control" name="whatsappNos" placeholder="{{ trans('main.whatsappNos2') }}"></textarea>
                            </div>
                        </div> 
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.message_type') }} :</label>
                            <div class="col-9">
                                <select class="selectpicker" data-style="btn-outline-myPR" name="message_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ !Request::has('message_type') || old('message_type') == 1 ? 'selected' : '' }}>{{ trans('main.text') }}</option>
                                    <option value="2" {{ old('message_type') == 2 ? 'selected' : '' }}>{{ trans('main.photoOrFile') }}</option>
                                    <option value="4" {{ old('message_type') == 4 ? 'selected' : '' }}>{{ trans('main.sound') }}</option>
                                    <option value="5" {{ old('message_type') == 5 ? 'selected' : '' }}>{{ trans('main.link') }}</option>
                                    <option value="6" {{ old('message_type') == 6 ? 'selected' : '' }}>{{ trans('main.whatsappNos') }}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group row mb-3">
                            <label class="col-3 col-form-label">{{ trans('main.sending_date') }} :</label>
                            <div class="col-9">
                                <div class="radio radio-blue mb-1 float-left">
                                    <input type="radio" class="first" id="radio" value="radio" checked="true" name="sending">
                                    <label for="radio"></label>
                                </div>
                                <p class="check-title">{{ trans('main.now') }}</p>
                                <div class="clearfix"></div>
                                <div class="radio radio-blue  float-left">
                                    <input type="radio" class="second" id="radio2" value="radio2" name="sending">
                                    <label for="radio2"></label>
                                </div>
                                <p class="check-title">{{ trans('main.send_at') }}</p>
                                <div class="clearfix"></div>
                                <input type="text" placeholder="YYYY-MM-DD H:i" name="date" class="form-control hidden flatpickr mt-2">
                            </div>
                        </div> 
                        <div class="reply" data-id="1">
                            <div class="form-group row mb-3">
                                <label class="col-3 col-form-label">{{ trans('main.message_content') }} :</label>
                                <div class="col-9">
                                    <textarea name="messageText" class="form-control" placeholder="{{ trans('main.message_content') }}">{{ old('messageText') }}</textarea>
                                </div>
                            </div> 
                        </div>
                        <div class="reply" data-id="2">
                            <div class="form-group row mb-3 hidden">
                                <label for="inputPassword3" class="col-3 col-form-label">{{ trans('main.textWithPhoto') }} :</label>
                                <div class="col-9">
                                    <input type="text" class="form-control" value="{{ old('message') }}" name="message" placeholder="{{ trans('main.textWithPhoto') }}">
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
                                            <i class="h1 text-muted dripicons-cloud-upload"></i>
                                            <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
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
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTN">{{ trans('main.add') }}</button>
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
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
                            <div class="conversation-container overflowY clearfix">    
                                <div class="message received" style="margin-top: 70px;"></div>
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