{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')

@endsection

@section('content')
    @php
    $store_url = '';
    @endphp
    @if((Request::has('type') && Request::get('type') == 'salla'))
    @php
    $store_url = '';
    $oauthDataObj = \App\Models\OAuthData::where('user_id',ROOT_ID)->where('type','salla')->first();
    $token = isset($oauthDataObj) && $oauthDataObj->access_token != null ? $oauthDataObj->access_token : \App\Models\Variable::getVar('SallaStoreToken');
    $initRequest = \Http::withToken($token)->get('https://accounts.salla.sa/oauth2/user/info');
    $result = $initRequest->json();
    if($result['success'] == true && isset($result['data']['store'])){
        $store_url = $result['data']['store']['domain'];
    }
    @endphp 
    
    @endif
    @if($store_url != '')
    <div class="row">
        <div class="col-xs-12 service first" {{ Request::has('type') && Request::get('type') == 'salla' ? 'style=display:block' : '' }}>
            <div class="form">
                <div class="card-body">
                    <div class="formPayment">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <i class="fa fa-check-circle" style="display: block;width: auto;margin: auto;font-size: 64px;color: #00BFB5;"></i>
                            <h2 class="title" style="border: 0;width: 310px;margin:auto;line-height: 1.5;">{{ trans('main.sallaNotify',['store_url'=> $store_url]) }}</h2>
                            <a href="#" class="btnAdd" style="visibility: hidden;"></a>
                        </div> 
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif(\Helper::checkRules('updateSalla') && (Request::has('type') && Request::get('type') == 'salla') )
    <div class="row">
        <div class="col-xs-12 service first" {{ Request::has('type') && Request::get('type') == 'salla' ? 'style=display:block' : '' }}>
            <div class="form">
                <div class="card-body">
                    <form action="{{ URL::to('/profile/updateSalla') }}" class="formPayment" method="post">
                        @csrf
                        <div class="form-group mains">
                            <label class="col-3 titleLabel">{{ trans('main.store_token') }} :</label>
                            <div class="col-9">
                                <input name="store_token" value="{{ $token }}" placeholder="{{ trans('main.store_token') }}">
                            </div>
                        </div> 
                        <hr class="mt-5">
                        <div class="form-group">
                            <div class="col-9">
                                <div class="nextPrev clearfix ">
                                    <button class="btnNext AddBTN">{{ trans('main.edit') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(\Helper::checkRules('updateZid') && (Request::has('type') && Request::get('type') == 'zid'))
    <div class="row">
        <div class="col-xs-12 service second" {{ Request::has('type') && Request::get('type') == 'zid' ? 'style=display:block' : '' }}>
            <div class="form">
                <div class="card-body">
                    <form action="{{ URL::to('/services/zid/settings') }}" class="formPayment" method="post">
                        @csrf
                        <div class="form-group mains">
                            <label class="col-3 col-form-label">{{ trans('main.store_token') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="store_token" value="{{ \App\Models\Variable::getVar('ZidStoreToken') }}" placeholder="{{ trans('main.store_token') }}">
                            </div>
                        </div>
                        <div class="form-group mains">
                            <label class="col-3 col-form-label">{{ trans('main.store_id') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="store_id" value="{{ \App\Models\Variable::getVar('ZidStoreID') }}" placeholder="{{ trans('main.store_id') }}">
                            </div>
                        </div>  
                        <hr class="mt-5">
                        <div class="form-group">
                            <div class="col-9">
                                <div class="nextPrev clearfix ">
                                    <button class="btnNext AddBTN">{{ trans('main.edit') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('components/profile_services.js') }}" type="text/javascript"></script>
@endsection
