{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('css/phone.css') }}">
<style type="text/css" media="screen">
    .user-langs{
        background-color: unset;
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <form class="formPayment" method="POST" action="{{ URL::to('/bots/update/'.$data->data->id) }}">
                        @csrf
                        <input type="hidden" name="status" value="{{ $data->data->status }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.messageType') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="message_type">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="1" {{ $data->data->message_type == 1 ? 'selected' : '' }}>{{ trans('main.equal') }}</option>
                                        <option value="2" {{ $data->data->message_type == 2 ? 'selected' : '' }}>{{ trans('main.part') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.clientMessage') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" value="{{ $data->data->message }}" name="message" placeholder="{{ trans('main.clientMessage') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.replyType') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="reply_type">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="1" {{ $data->data->reply_type == 1 ? 'selected' : '' }}>{{ trans('main.text') }}</option>
                                        <option value="2" {{ $data->data->reply_type == 2 ? 'selected' : '' }}>{{ trans('main.photoOrFile') }}</option>
                                        <option value="3" {{ $data->data->reply_type == 3 ? 'selected' : '' }}>{{ trans('main.video') }}</option>
                                        <option value="4" {{ $data->data->reply_type == 4 ? 'selected' : '' }}>{{ trans('main.sound') }}</option>
                                        <option value="5" {{ $data->data->reply_type == 5 ? 'selected' : '' }}>{{ trans('main.link') }}</option>
                                        <option value="6" {{ $data->data->reply_type == 6 ? 'selected' : '' }}>{{ trans('main.whatsappNos') }}</option>
                                        <option value="7" {{ $data->data->reply_type == 7 ? 'selected' : '' }}>{{ trans('main.mapLocation') }}</option>
                                        <option value="8" {{ $data->data->reply_type == 8 ? 'selected' : '' }}>{{ trans('main.webhook') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel" for="lang"> {{ trans('main.lang') }}</label>
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle">
                                    <select class="form-control" data-toggle="select2" data-style="btn-outline-myPR" name="lang">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        <option value="0" {{ $data->data->lang == 0 ? 'selected' : '' }}>{{ trans('main.arabic') }}</option>
                                        <option value="1" {{ $data->data->lang == 1 ? 'selected' : '' }}>{{ trans('main.english') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="1">
                            <div class="row {{ $data->data->reply_type == 1 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.messageContent') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <textarea name="replyText" class="form-control" placeholder="{{ trans('main.messageContent') }}">{{ $data->data->reply_type == 1 ?  $data->data->reply : '' }}</textarea>
                                </div>
                            </div> 
                        </div>
                        <div class="reply" data-id="2">
                            <div class="row {{ $data->data->reply_type == 2 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.textWithPhoto') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 2 ?  $data->data->reply : '' }}" name="reply" placeholder="{{ trans('main.textWithPhoto') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 2 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.attachFile') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="dropzone editDropZone">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 si si-cloud-upload"></i>
                                            <h3 class="text-center">{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                        @if($data->data->file != '' && $data->data->reply_type == 2)
                                        <div class="dz-preview dz-image-preview" id="my-preview">  
                                            <div class="dz-image">
                                                <img alt="image" src="{{ $data->data->file }}">
                                            </div>  
                                            <div class="dz-details">
                                                <div class="dz-size">
                                                    <span><strong>{{ $data->data->file_size }}</strong></span>
                                                </div>
                                                <div class="dz-filename">
                                                    <span data-dz-name="">{{ $data->data->file_name }}</span>
                                                </div>
                                                <div class="PhotoBTNS">
                                                    <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                       <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                            <a href="{{ $data->data->file }}" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                            <img src="{{ $data->data->file }}" itemprop="thumbnail" style="display: none;">
                                                        </figure>
                                                    </div>
                                                    @if(\Helper::checkRules('deleteImage-'.$data->designElems['mainData']['nameOne']))
                                                    <a class="DeletePhotoN" data-type="file_name" data-area="{{ $data->data->id }}"><i class="fa fa-trash" data-name="{{ $data->data->file_name }}" data-clname="Photo"></i> </a>
                                                    @endif                    
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="3">
                            <div class="row {{ $data->data->reply_type == 3 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.attachFile') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="dropzone editDropZone">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 si si-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                        @if($data->data->file != '' && $data->data->reply_type == 3)
                                        <div class="dz-preview dz-image-preview" id="my-preview">  
                                            <div class="dz-image">
                                                <img alt="image" src="{{ $data->data->file }}">
                                            </div>  
                                            <div class="dz-details">
                                                <div class="dz-size">
                                                    <span><strong>{{ $data->data->file_size }}</strong></span>
                                                </div>
                                                <div class="dz-filename">
                                                    <span data-dz-name="">{{ $data->data->file_name }}</span>
                                                </div>
                                                <div class="PhotoBTNS">
                                                    <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                       <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                            <a href="{{ $data->data->file }}" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                            <img src="{{ $data->data->file }}" itemprop="thumbnail" style="display: none;">
                                                        </figure>
                                                    </div>
                                                    @if(\Helper::checkRules('deleteImage-'.$data->designElems['mainData']['nameOne']))
                                                    <a class="DeletePhotoN" data-type="file_name" data-area="{{ $data->data->id }}"><i class="fa fa-trash" data-name="{{ $data->data->file_name }}" data-clname="Photo"></i> </a>
                                                    @endif                    
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="4">
                            <div class="row {{ $data->data->reply_type == 4 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.attachFile') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="dropzone editDropZone">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 si si-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                        @if($data->data->file != '' && $data->data->reply_type == 4)
                                        <div class="dz-preview dz-image-preview" id="my-preview">  
                                            <div class="dz-image">
                                                <img alt="image" src="{{ $data->data->file }}">
                                            </div>  
                                            <div class="dz-details">
                                                <div class="dz-size">
                                                    <span><strong>{{ $data->data->file_size }}</strong></span>
                                                </div>
                                                <div class="dz-filename">
                                                    <span data-dz-name="">{{ $data->data->file_name }}</span>
                                                </div>
                                                <div class="PhotoBTNS">
                                                    <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                       <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                            <a href="{{ $data->data->file }}" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                            <img src="{{ $data->data->file }}" itemprop="thumbnail" style="display: none;">
                                                        </figure>
                                                    </div>
                                                    @if(\Helper::checkRules('deleteImage-'.$data->designElems['mainData']['nameOne']))
                                                    <a class="DeletePhotoN" data-type="file_name" data-area="{{ $data->data->id }}"><i class="fa fa-trash" data-name="{{ $data->data->file_name }}" data-clname="Photo"></i> </a>
                                                    @endif                    
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="5">
                            <div class="row {{ $data->data->reply_type == 5 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.url') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 5 ?  $data->data->https_url : '' }}" name="https_url" placeholder="{{ trans('main.url') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 5 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.urlTitle') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 5 ?  $data->data->url_title : '' }}" name="url_title" placeholder="{{ trans('main.urlTitle') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 5 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.urlDesc') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 5 ?  $data->data->url_desc : '' }}" name="url_desc" placeholder="{{ trans('main.urlDesc') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 5 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.urlImage') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="dropzone editDropZone">
                                        <div class="fallback">
                                            <input name="file" type="file" />
                                        </div>
                                        <div class="dz-message needsclick">
                                            <i class="h1 si si-cloud-upload"></i>
                                            <h3>{{ trans('main.dropzoneP') }}</h3>
                                        </div>
                                    </div>
                                    @if($data->data->photo != '' && $data->data->reply_type == 5)
                                    <div class="dz-preview dz-image-preview" id="my-preview">  
                                        <div class="dz-image">
                                            <img alt="image" src="{{ $data->data->photo }}">
                                        </div>  
                                        <div class="dz-details">
                                            <div class="dz-size">
                                                <span><strong>{{ $data->data->photo_size }}</strong></span>
                                            </div>
                                            <div class="dz-filename">
                                                <span data-dz-name="">{{ $data->data->photo_name }}</span>
                                            </div>
                                            <div class="PhotoBTNS">
                                                <div class="my-gallery" itemscope="" itemtype="" data-pswp-uid="1">
                                                   <figure itemprop="associatedMedia" itemscope="" itemtype="">
                                                        <a href="{{ $data->data->photo }}" itemprop="contentUrl" data-size="555x370"><i class="fa fa-search"></i></a>
                                                        <img src="{{ $data->data->photo }}" itemprop="thumbnail" style="display: none;">
                                                    </figure>
                                                </div>
                                                @if(\Helper::checkRules('deleteImage-'.$data->designElems['mainData']['nameOne']))
                                                <a class="DeletePhotoN" data-type="url_image" data-area="{{ $data->data->id }}"><i class="fa fa-trash" data-name="{{ $data->data->photo_name }}" data-clname="Photo"></i> </a>
                                                @endif                    
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="6">
                            <div class="row {{ $data->data->reply_type == 6 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.whatsappNo') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="tel" class="form-control teles" value="{{ $data->data->reply_type == 6 ?  $data->data->whatsapp_no : '' }}" name="whatsapp_no" placeholder="{{ trans('main.whatsappNo') }}">
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="7">
                            <div class="row {{ $data->data->reply_type == 7 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.lat') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 7 ?  $data->data->lat : '' }}" name="lat" placeholder="{{ trans('main.lat') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 7 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.lng') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 7 ?  $data->data->lng : '' }}" name="lng" placeholder="{{ trans('main.lng') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 7 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.location') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 7 ?  $data->data->address : '' }}" name="address" placeholder="{{ trans('main.location') }}">
                                </div>
                            </div>
                        </div>
                        <div class="reply" data-id="8">
                            <div class="row {{ $data->data->reply_type == 8 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.webhookURL') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" value="{{ $data->data->reply_type == 8 ?  $data->data->webhook_url : '' }}" name="webhook_url" placeholder="{{ trans('main.webhookURL') }}">
                                </div>
                            </div>
                            <div class="row {{ $data->data->reply_type == 8 ? '' : 'hidden' }}">
                                <div class="col-md-3">
                                    <label class="titleLabel">{{ trans('main.sentTemplates') }} :</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="row mb-2 ml-2 mr-2">
                                        @foreach($data->templates as $template)
                                        <div class="checkbox checkbox-blue checkbox-single float-left">
                                            <input type="checkbox" name="templates[]" value="{{ $template->id }}" {{ $data->data->reply_type == 8 && in_array($template->id,$data->data->templates) ? 'checked' : '' }}>
                                            <label>{{ $template->title }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="row mt-4">
                                        <div class="col-6">
                                            <button type="button" class="btn btn-info SelectAllCheckBox ml-2 mr-2">{{ trans('main.selectAll') }}</button>
                                            <button type="button" class="btn btn-danger UnSelectAllCheckBox">{{ trans('main.deselectAll') }}</button>
                                        </div>            
                                    </div>
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
@endsection

@section('modals')
@include('tenant.Partials.photoswipe_modal')
@endsection

@section('scripts')
<script src="{{ asset('V5/js/photoswipe.min.js') }}"></script>
<script src="{{ asset('V5/js/photoswipe-ui-default.min.js') }}"></script>
<script src="{{ asset('V5/components/myPhotoSwipe.js') }}"></script>      
<script src="{{ asset('V5/components/phone.js') }}"></script>
<script src="{{ asset('V5/components/addBot.js') }}"></script>
@endsection
