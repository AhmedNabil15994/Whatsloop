{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')

@endsection

@section('content')

    @if(\Helper::checkRules('updateSalla') && (Request::has('type') && Request::get('type') == 'salla') )
    <div class="row">
        <div class="col-xs-12 service first" {{ Request::has('type') && Request::get('type') == 'salla' ? 'style=display:block' : '' }}>
            <div class="form">
                <div class="card-body">
                    <form action="{{ URL::to('/profile/updateSalla') }}" class="formPayment" method="post">
                        @csrf
                        <div class="form-group mains">
                            <label class="col-3 titleLabel">{{ trans('main.store_token') }} :</label>
                            <div class="col-9">
                                <input name="store_token" value="{{ \App\Models\Variable::getVar('SallaStoreToken') }}" placeholder="{{ trans('main.store_token') }}">
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
