@extends('tenant.Layouts.master')
@section('title',trans('main.dashboard'))
@section('styles')

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
                                    <span class="number">1</span> 
                                    <span class="title">{{ trans('main.channelConfig') }}</span>
                                </a>
                            </li>
                            @if(!in_array(1,$data->data) && !in_array(2,$data->data) && ! $data->dis)
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-1" href="#wizard1-h-1" aria-controls="wizard1-p-1">
                                    <span class="number">2</span> 
                                    <span class="title">{{ $data->data[0] == 4 ? trans('main.zid_info') : trans('main.salla_info') }}</span>
                                </a>
                            </li>
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-2" href="#wizard1-h-2" aria-controls="wizard1-p-2">
                                    <span class="number">3</span> 
                                    <span class="title">{{ trans('main.templatesSettings') }}</span>
                                </a>
                            </li>
                            @endif
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-3" href="#wizard1-h-3" aria-controls="wizard1-p-3">
                                    <span class="number">{{ !in_array(1,$data->data) && !in_array(2,$data->data) && !$data->dis ? 4 : 2 }}</span> 
                                    <span class="title">{{ trans('main.qrScan') }}</span>
                                </a>
                            </li>
                            <li role="tab" class="disabled last">
                                <a id="wizard1-t-2" href="#wizard1-h-4" aria-controls="wizard1-p-4">
                                    <span class="number">{{ !in_array(1,$data->data) && !in_array(2,$data->data) && !$data->dis ? 5 : 3 }}</span> 
                                    <span class="title">{{ trans('main.congratulations') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="content clearfix">
                        <h3 id="wizard1-h-0" tabindex="-1" class="title mb-5 current">{{ trans('main.channelConfig') }}</h3>
                        <section id="wizard1-p-0" role="tabpanel" aria-labelledby="wizard1-h-0" class="body current" aria-hidden="false">
                            <div class="control-group form-group">
                                <label class="form-label">{{ trans('main.channelName') }}</label>
                                <input type="text" class="form-control" name="channelName" placeholder="{{ trans('main.channelName') }}" value="{{ $data->channelName }}">
                            </div>
                        </section>
                        @if(!in_array(1,$data->data) && !in_array(2,$data->data) && !$data->dis)
                        <h3 id="wizard1-h-1" tabindex="-1" class="title mb-5">{{ $data->data[0] == 4 ? trans('main.zid_info') : trans('main.salla_info') }}</h3>
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
                            <div class="form-group row mains">
                                <label class="col-3 col-form-label">{{ trans('main.merchant_token') }} :</label>
                                <div class="col-9">
                                    <textarea class="form-control" name="merchant_token" placeholder="{{ trans('main.merchant_token') }}">{{ \App\Models\Variable::getVar('ZidMerchantToken') }}</textarea>
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
                        <h3 id="wizard1-h-2" tabindex="-1" class="title mb-5">{{ trans('main.templatesSettings') }}</h3>
                        <section id="wizard1-p-2" role="tabpanel" aria-labelledby="wizard1-h-2" class="body" aria-hidden="true" style="display: none;">
                            @foreach($data->templates as $template)
                            <div class="row d-block">
                                <div class="form-group row mb-3">
                                    <label class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                                    <div class="col-8">
                                        <select class="form-control select2" readonly name="statusText">
                                            <option value="{{ $template->statusText }}" selected>{{ $template->statusText }}</option>
                                        </select>
                                    </div>
                                    <div class="col-1">
                                        <div class="main-toggle-group-demo">
                                            <div class="main-toggle {{ $template->status == 1 ? 'on' : '' }}" data-area="{{ $template->id }}">
                                                <span></span>
                                            </div>
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
                        <h3 id="wizard1-h-3" tabindex="-1" class="title mb-5">{{ trans('main.qrScan') }}</h3>
                        <section id="wizard1-p-3" role="tabpanel" aria-labelledby="wizard1-h-3" class="body text-center" aria-hidden="true" style="display: none;">
                            <img class="qrImage mb-3 mt-3" src="#" alt="">    
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-8 text-left">
                                    <h3 class="main-content-label mb-4">{{ trans('main.instructions') }}</h3>
                                    <ul class="list-unstyled">
                                        <li>{{ trans('main.tip1') }}</li>
                                        <li>{{ trans('main.tip2') }}</li>
                                        <li>{{ trans('main.tip3') }}</li>
                                        <li>{{ trans('main.tip4') }}</li>
                                    </ul>
                                    <p>{{ trans('main.tip5') }}</p>
                                </div>
                                <div class="col-2"></div>
                            </div>  
                        </section>
                        <h3 id="wizard1-h-4" tabindex="-1" class="title mb-5">{{ trans('main.congratulations') }}</h3>
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
                    <div class="actions clearfix">
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

{{-- Scripts Section --}}

@section('topScripts')
<script src="{{ asset('components/steps.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/form-elements.js') }}" type="text/javascript"></script>
@endsection
