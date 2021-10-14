@extends('tenant.Layouts.master')
@section('title',trans('main.prepareAccount'))
@section('styles')
@if(!$data->dis && in_array($data->data[0],[4,5]))
    <style type="text/css" media="screen">
        .wizard>.steps>ul li{
            width: 20%;
        }
        html[dir="rtl"] .wizard>.steps>ul>li:not(.last) a:after{
            left: -50px;
        }

        html[dir="ltr"] .wizard>.steps>ul>li:not(.last) a:after{
            right: -50px;
            transform: rotateZ(180deg);
        }
    </style>
@else
    <style type="text/css" media="screen">
        .wizard>.steps>ul li{
            width: 33%;
        }
        html[dir="ltr"] .wizard>.steps>ul>li:not(.last) a:after{
            right: -150px;
            transform: rotateZ(180deg);
        }
        html[dir="rtl"] .wizard>.steps>ul>li:not(.last) a:after{
            left: -150px
        }
    </style>
@endif
@endsection


{{-- Content --}}
@section('content')

<div class="row">
    <div class="col-lg-12 col-md-12">
        <input type="hidden" name="oldName" value="{{ $data->channelName }}">
        @if(!$data->dis)
        <input type="hidden" name="modID" value="{{ $data->data[0] }}">
        @endif
        <div class="card">
            <div class="card-body">
                <div class="main-content-label mg-b-5">{{ trans('main.prepareAccount') }} @if(!$data->dis)( {{ $data->dataNames[0] }} )@endif</div>
                <p class="mg-b-20 card-sub-title tx-12 text-muted"></p>
                <div id="wizard1" role="application" class="wizard clearfix">
                    <div class="steps clearfix">
                        <ul role="tablist">
                            <li role="tab" class="first current">
                                <a id="wizard1-t-0" href="#wizard1-h-0" aria-controls="wizard1-p-0">
                                    <span class="current-info audible">current step: </span>
                                    <span class="number"><img src="{{ asset('images/channel_setting.svg') }}" alt="channel_setting"></span> 
                                    <span class="title">{{ trans('main.channelConfig') }}</span>
                                </a>
                            </li>
                            @if(!in_array(1,$data->data) && !in_array(2,$data->data) && ! $data->dis)
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-1" href="#wizard1-h-1" aria-controls="wizard1-p-1">
                                    <span class="number"><img src="{{ asset('images/solutions.svg') }}" alt="solutions"></span> 
                                    <span class="title">{{ $data->data[0] == 4 ? trans('main.zid_info') : trans('main.salla_info') }}</span>
                                </a>
                            </li>
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-2" href="#wizard1-h-2" aria-controls="wizard1-p-2">
                                    <span class="number"><img src="{{ asset('images/template_setting.svg') }}" alt="template_setting"></span> 
                                    <span class="title">{{ trans('main.templatesSettings') }}</span>
                                </a>
                            </li>
                            @endif
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-3" href="#wizard1-h-3" aria-controls="wizard1-p-3">
                                    <span class="number"><img src="{{ asset('images/qr-code.svg') }}" alt="qr-code"></span> 
                                    <span class="title">{{ trans('main.qrScan') }}</span>
                                </a>
                            </li>
                            <li role="tab" class="disabled last">
                                <a id="wizard1-t-2" href="#wizard1-h-4" aria-controls="wizard1-p-4">
                                    <span class="number"><img src="{{ asset('images/startup.svg') }}" alt="startup"></span> 
                                    <span class="title">{{ trans('main.congratulations') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="content clearfix qrData">
                        <h3 id="wizard1-h-0" tabindex="-1" class="title mb-5 mt-5 current">{{ trans('main.channelConfig') }}</h3>
                        <section id="wizard1-p-0" role="tabpanel" aria-labelledby="wizard1-h-0" class="body current" aria-hidden="false">
                            <div class="control-group form-group">
                                <label class="form-label">{{ trans('main.channelName') }}</label>
                                <input type="text" class="form-control" name="channelName" placeholder="{{ trans('main.channelName') }}" value="{{ $data->channelName }}">
                            </div>
                        </section>
                        @if(!in_array(1,$data->data) && !in_array(2,$data->data) && !$data->dis)
                        <h3 id="wizard1-h-1" tabindex="-1" class="title mb-5 mt-5">{{ $data->data[0] == 4 ? trans('main.zid_info') : trans('main.salla_info') }}</h3>
                        <section id="wizard1-p-1" role="tabpanel" aria-labelledby="wizard1-h-1" class="body" aria-hidden="true" style="display: none;">
                        @if($data->data[0] == 4)
                        <form action="{{ URL::to('/profile/updateZid') }}" method="post">
                            @csrf
                            <div class="form-group row mains">
                                <label class="col-3 col-form-label">{{ trans('main.store_token') }} :</label>
                                <div class="col-9">
                                    <input class="form-control" name="store_token" value="{{ \App\Models\Variable::getVar('ZidStoreToken') }}" placeholder="{{ trans('main.store_token') }}">
                                </div>
                            </div>
                            <div class="form-group row mains">
                                <label class="col-3 col-form-label">{{ trans('main.store_id') }} :</label>
                                <div class="col-9">
                                    <input class="form-control" name="store_id" value="{{ \App\Models\Variable::getVar('ZidStoreID') }}" placeholder="{{ trans('main.store_id') }}">
                                </div>
                            </div>                                                      
                        </form>
                        @elseif($data->data[0] == 5)
                        <form action="{{ URL::to('/profile/updateSalla') }}" method="post">
                            @csrf
                            <div class="form-group row mains">
                                <label class="col-3 col-form-label">{{ trans('main.store_token') }} :</label>
                                <div class="col-9">
                                    <input class="form-control" name="store_tokens" value="{{ \App\Models\Variable::getVar('SallaStoreToken') }}" placeholder="{{ trans('main.store_token') }}">
                                </div>
                            </div> 
                        </form>
                        @endif
                        </section>
                        <h3 id="wizard1-h-2" tabindex="-1" class="title mb-5 mt-5">{{ trans('main.templatesSettings') }}</h3>
                        <section id="wizard1-p-2" role="tabpanel" aria-labelledby="wizard1-h-2" class="body" aria-hidden="true" style="display: none;">
                            @foreach($data->templates as $template)
                            <div class="row d-block">
                                <div class="form-group row mb-3">
                                    <label class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                                    <div class="col-8">
                                        <label class="col-form-label">{{ $template->statusText }}</label>
                                    </div>
                                    <div class="col-1">
                                        <div class="form-group">
                                            <label class="custom-switch pl-0">
                                                <input type="checkbox" name="custom-switch-checkbox{{ $template->id }}" class="custom-switch-input" {{ $template->status == 1 ? 'checked' : '' }} data-area="{{ $template->id }}">
                                                <span class="custom-switch-indicator"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                            <div class="row d-block">
                                <div class="form-group row mb-3">
                                    <label class="col-3 col-form-label">{{ trans('main.content_'.LANGUAGE_PREF) }} :</label>
                                    <div class="col-9">
                                        <textarea class="form-control" name="title_{{ LANGUAGE_PREF }}" placeholder="{{ trans('main.content_'.LANGUAGE_PREF) }}" disabled>{{ $template->content }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endforeach
                        </section>
                        @endif
                        <h3 id="wizard1-h-3" tabindex="-1" class="title mb-5 mt-5">{{ trans('main.qrScan') }}</h3>
                        <section id="wizard1-p-3" role="tabpanel" aria-labelledby="wizard1-h-3" class="body text-center" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-4">
                                    @livewire('qr-image')
                                    {{-- <img class="qrImage mb-3 mt-3" src="{{ asset('images/qr-load.png') }}" alt="qr" data-area="0">     --}}
                                </div>
                                <div class="col-4">
                                    <div class="alert alert-custom alert-outline-2x alert-outline-light fade show" role="alert">
                                        <div class="alert-icon">
                                            <i class="ti ti-help-alt"></i>
                                        </div>
                                        <div class="alert-text">{{ trans('main.alert1') }}</div>
                                    </div>
                                    <div class="alert alert-light">{{ trans('main.alert2') }}</div>
                                    <div class="alert alert-light">{{ trans('main.alert3') }}</div>
                                    <div class="alert alert-light">{{ trans('main.alert4') }}</div>
                                    <div class="alert alert-light">{{ trans('main.alert5') }}</div>
                                    <div class="alert alert-light">{{ trans('main.alert6') }}</div>
                                    <div class="alert alert-light">{{ trans('main.alert7') }}</div>
                                </div>
                                <div class="col-4">
                                    <img src="{{ asset('images/scanQR.gif') }}" alt="">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-left mt-5 tips">
                                    <h3 class="main-content-label mb-4">{{ trans('main.instructions') }}</h3>
                                    <ul class="list-unstyled">
                                        <li>{{ trans('main.tip11') }}</li>
                                        <li>{{ trans('main.tip21') }}</li>
                                        <li>{{ trans('main.tip31') }}</li>
                                        <li>{{ trans('main.tip41') }}</li>
                                    </ul>
                                    <p>{{ trans('main.tip51') }}</p>
                                    <div class="row text-right d-block">
                                        <a class="btn btn-success mr-1 ml-1 modal-effect text-white" data-effect="effect-sign" data-toggle="modal" data-target="#tipsModal" data-backdrop="static">{{ trans('main.bestTips') }}</a>
                                        <a class="btn btn-success mr-1 ml-1 modal-effect text-white" data-effect="effect-sign" data-toggle="modal" data-target="#termsModal" data-backdrop="static">{{ trans('main.conditions') }}</a>
                                    </div>
                                </div>
                            </div>  
                        </section>
                        <h3 id="wizard1-h-4" tabindex="-1" class="title mb-5 mt-5">{{ trans('main.congratulations') }}</h3>
                        <section id="wizard1-p-4" role="tabpanel" aria-labelledby="wizard1-h-4" class="body" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-3"></div>
                                <div class="col-6">
                                    <div class="card bd-0 mg-b-20">
                                        <div class="card-body">
                                            <div class="main-error-wrapper">
                                                <i class="si si-check mg-b-20 tx-50"></i>
                                                <h4 class="mg-b-10">{{ trans('main.succ1') }}</h4>
                                                <p class="mb-0 tx-12">{{ trans('main.succ2') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3"></div>
                            </div>
                        </section>
                    </div>
                    <div class="actions clearfix qrData">
                        <ul role="menu" aria-label="Pagination">
                            <li class="disabled prev" aria-disabled="true">
                                <a href="#previous" role="menuitem">{{ trans('main.prev') }}</a>
                            </li>
                            <li aria-hidden="false" class="next" aria-disabled="false">
                                <a href="#next" role="menuitem">{{ trans('main.next') }}</a>
                            </li>
                            <li aria-hidden="true" class="finish" style="display: none;">
                                <a href="#finish" role="menuitem">{{ trans('main.finish') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modals')
@include('tenant.Partials.tipsModal')
@include('tenant.Partials.termsModal')
@endsection

{{-- Scripts Section --}}

@section('topScripts')
<script src="{{ asset('components/steps.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-elements.js') }}" type="text/javascript"></script>
@endsection
