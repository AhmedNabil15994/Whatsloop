@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.prepareAccount'))
@section('styles')
<style type="text/css" media="screen">
    textarea{
        min-height: 200px;
    }
    
    textarea.form-control
    {
        padding:20px;
        resize:none;
        background: #f7f7f7!important;
        border: 1px solid #eee!important;
    }
    
    .textLeft{
        text-align:left;
    }
    
    hr
    {
        margin:40px 0;
    }
    
    .boldText
    {
        font-family: 'Tajawal-Bold';
    }
    
</style>
@endsection


{{-- Content --}}
@section('content')
<div class="botStyle">
    <input type="hidden" name="oldName" value="{{ $data->channelName }}">
    @if(!$data->dis)
    <input type="hidden" name="modID" value="{{ $data->data[0] }}">
    @endif

    <h2 class="titleBot">{{ trans('main.prepareAccount') }} @if(!$data->dis)( {{ $data->dataNames[0] }} )@endif</h2>
    <div class="stepsBot">
        <div class="step active" data-target="#step1">
            <i class="flaticon-whatsapp"></i>
            {{ trans('main.channelConfig') }}
        </div>
        @if(!in_array(1,$data->data) && !in_array(2,$data->data) && ! $data->dis)
        <div class="step" data-target="#step4">
            @if($data->data[0] == 4)
            <i>
                <svg xmlns="http://www.w3.org/2000/svg" width="31.158" height="32.769" viewBox="0 0 31.158 32.769">
                    <path id="Path_1275" data-name="Path 1275" d="M119.9,218.925a3.281,3.281,0,0,1,3.8-3.273,19.364,19.364,0,0,1,8.138,3.73,14.581,14.581,0,0,1,4.776,7.6c.52,2.054.873,5.131-1.089,6.572-1.722,1.265-3.318-.131-3.736-1.865-.782-3.242.464-6.3,2.427-8.846a19.086,19.086,0,0,1,7.06-5.744,16.448,16.448,0,0,1,4.79-1.151,2.743,2.743,0,0,1,3,2.738h0a2.743,2.743,0,0,1-3,2.739,16.447,16.447,0,0,1-4.79-1.152,19.075,19.075,0,0,1-7.06-5.744c-1.964-2.548-3.209-5.6-2.427-8.845.418-1.735,2.014-3.13,3.736-1.865,1.962,1.441,1.609,4.518,1.089,6.572a14.581,14.581,0,0,1-4.776,7.6,19.954,19.954,0,0,1-8.341,3.925,3.047,3.047,0,0,1-3.592-2.992Z" transform="translate(-118.903 -202.306)" fill="none" stroke="#9499a4" stroke-miterlimit="10" stroke-width="2"/>
                </svg>
            </i>
            {{ trans('main.zid_info') }}
            @elseif($data->data[0] == 5)
            <i class="flaticon-shopping-bag"></i>
            {{ trans('main.salla_info') }}
            @endif
        </div>
        <div class="step" data-target="#step5">
            <i class="flaticon-layer"></i>
            {{ trans('main.templatesSettings') }}
        </div>
        @endif
        <div class="step" data-target="#step2">
            <i class="flaticon-qr-code"></i>
            {{ trans('main.qrScan') }}
        </div>
        <div class="step" data-target="#step3">
            <i class="flaticon-rocket"></i>
            {{ trans('main.congratulations') }}
        </div>
    </div>

    <div class="setSteps active" id="step1">
        <div class="settings">
            <h2 class="titleSettings">{{ trans('main.channelConfig') }}</h2>
            <div class="channel">
                <h2 class="titleChannel">{{ trans('main.channelName') }}</h2>
                <input type="text" name="channelName" placeholder="{{ trans('main.channelName') }}" value="{{ $data->channelName }}"/>
            </div>
            <div class="nextPrev clearfix">
                <button class="btnNext">{{ trans('main.next') }}</button>
            </div>
        </div>
    </div>

    @if(!in_array(1,$data->data) && !in_array(2,$data->data) && !$data->dis)
    <div class="setSteps" id="step4">
        <div class="settings">
            <h2 class="titleSettings">{{ $data->data[0] == 4 ? trans('main.zid_info') : trans('main.salla_info') }}</h2>
            @if($data->data[0] == 4)
            <form action="{{ URL::to('/profile/updateZid') }}" method="post">
                @csrf
                <div class="channel">
                    <h2 class="titleChannel">{{ trans('main.store_token') }}</h2>
                    <input type="text" name="store_token" placeholder="{{ trans('main.store_token') }}" value="{{ \App\Models\Variable::getVar('ZidStoreToken') }}"/>
                </div>
                <div class="channel">
                    <h2 class="titleChannel">{{ trans('main.store_id') }}</h2>
                    <input type="text" name="store_id" placeholder="{{ trans('main.store_id') }}" value="{{ \App\Models\Variable::getVar('ZidStoreID') }}"/>
                </div>                                                  
            </form>
            @elseif($data->data[0] == 5)
            <form action="{{ URL::to('/profile/updateSalla') }}" method="post">
                @csrf
                <div class="channel">
                    <h2 class="titleChannel">{{ trans('main.store_token') }}</h2>
                    <input type="text" name="store_tokens" placeholder="{{ trans('main.store_token') }}" value="{{ \App\Models\Variable::getVar('SallaStoreToken') }}"/>
                </div> 
            </form>
            @endif
            <div class="nextPrev clearfix">
                <button class="btnNext btnPrev dis">{{ trans('main.prev') }}</button>
                <button class="btnNext">{{ trans('main.next') }}</button>
            </div>
        </div>
    </div>

    <div class="setSteps" id="step5">
        <div class="settings">
            <h2 class="titleSettings">{{ trans('main.templatesSettings') }}</h2>
            @foreach($data->templates as $template)
            <div class="d-block">
                <div class="form-group row mb-0" >
                    <label class="col-md-1 col-xs-3 col-form-label boldText" style="padding-left:0">{{ trans('main.status') }} :</label>
                    <div class="col-md-10 col-xs-7" style="padding-right:0">
                        <label class="col-form-label boldText" style="color:#00bfb5">{{ $template->statusText }}</label>
                    </div>
                    <div class="col-md-1 col-xs-2">
                        <div class="form-group textLeft">
                            <label class="custom-switch pl-0">
                                <input type="checkbox" name="custom-switch-checkbox{{ $template->id }}" class="custom-switch-input" {{ $template->status == 1 ? 'checked' : '' }} data-area="{{ $template->id }}">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="d-block">
                <div class="form-group mb-3">
                    <label class="col-3 col-form-label mb-2">{{ trans('main.content_'.LANGUAGE_PREF) }} :</label>
                    <div class="col-9">
                        <textarea class="form-control" name="title_{{ LANGUAGE_PREF }}" placeholder="{{ trans('main.content_'.LANGUAGE_PREF) }}" disabled>{{ $template->content }}</textarea>
                    </div>
                </div>
            </div>
            <hr>
            @endforeach
            <div class="nextPrev clearfix">
                <button class="btnNext btnPrev dis">{{ trans('main.prev') }}</button>
                <button class="btnNext">{{ trans('main.next') }}</button>
            </div>
        </div>
    </div>
    @endif

    <div class="setSteps" id="step2">
        <div class="settings">
            <h2 class="titleSettings">{{ trans('main.qrScan') }}</h2>
            <div class="attention">
                <i class="icon">?</i>
                {{ trans('main.alert1') }}
                <span> WhatsApp Web.</span>
            </div>
            <div class="qr">
                <div class="row">
                    <div class="col-md-3" style="overflow: hidden;">
                        @livewire('qr-image')
                    </div>
                    <div class="col-md-5">
                        <span class="stepQr">{{ trans('main.alert2') }}</span>
                        <span class="stepQr">{{ trans('main.alert3') }}</span>
                        <span class="stepQr">{{ trans('main.alert4') }}</span>
                        <span class="stepQr">{{ trans('main.alert5') }}</span>
                        <span class="stepQr">{{ trans('main.alert6') }}</span>
                        <span class="stepQr">{{ trans('main.alert7') }}</span>
                    </div>
                    <div class="col-md-4">
                        <img src="{{ asset('images/scanQR.gif') }}" class="imgPhone" alt="" />
                    </div>
                </div>
            </div>
        </div>
        <div class="settings">
            <div class="orangeBg">
                <h2 class="titleSettings">{{ trans('main.instructions') }}</h2>
                <ul class="list">
                    <li>{{ trans('main.tip11') }}</li>
                    <li>{{ trans('main.tip21') }}</li>
                    <li>{{ trans('main.tip31') }}</li>
                    <li>{{ trans('main.tip41') }}</li>
                    <li>{{ trans('main.tip51') }}</li>
                </ul>
            </div>
        </div>
        <div class="settings clearfix">
            <ul class="terms">
                <li><a data-effect="effect-sign" data-toggle="modal" data-target="#tipsModal" data-backdrop="static">{{ trans('main.bestTips') }}</a></li>
                <li><a data-effect="effect-sign" data-toggle="modal" data-target="#termsModal" data-backdrop="static">{{ trans('main.conditions') }}</a></li>
            </ul>
            <div class="nextPrev clearfix">
                <button class="btnNext btnPrev dis">{{ trans('main.prev') }}</button>
                <button class="btnNext">{{ trans('main.next') }}</button>
            </div>
        </div>
    </div>

    <div class="setSteps" id="step3">
        <div class="settings">
            <h2 class="titleSettings">{{ trans('main.congratulations') }}</h2>
            <div class="attention">
                <i class="flaticon-rocket"></i>
                <h4 class="mg-b-10">{{ trans('main.succ1') }}</h4>
                <p class="mb-0 tx-12">{{ trans('main.succ2') }}</p>
            </div>
        </div>
        <div class="settings clearfix">
            <div class="nextPrev clearfix">
                <button class="btnNext btnPrev dis">{{ trans('main.prev') }}</button>
                <button class="btnNext finish">{{ trans('main.finish') }}</button>
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

@section('scripts')
<script src="{{ asset('V5/components/steps.js') }}" type="text/javascript"></script>
<script>
Livewire.on('statusChanged', postId => {
    document.querySelector('#step2 .btnNext:not(.btnPrev):not(.finish)').click();
    window.location.href= '/dashboard';
})
</script>

@endsection
