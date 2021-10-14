{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<style type="text/css">
    i{
        border: 0 !important;
    }
</style>
@endsection

@section('content')
<!-- Start Content-->
<div class="container-fluid">

    @if( !Request::has('type'))
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ trans('main.availServices') }}</h4>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <div class="card myCard" data-toggle=".first">
                                <img class="card-img-top img-fluid" src="{{ asset('images/salla.svg') }}" alt="Card image cap">
                                <div class="card-body">
                                    <h3 class="card-title">Salla</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card myCard" data-toggle=".second">
                                <img class="card-img-top img-fluid" src="{{ asset('images/zid_logo.png') }}" alt="Card image cap">
                                <div class="card-body">
                                    <h3 class="card-title">Zid</h3>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    @endif

    @if(\Helper::checkRules('updateSalla'))
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10 service first" {{ Request::has('type') && Request::get('type') == 'salla' ? 'style=display:block' : '' }}>
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="header-title">{{ trans('main.salla_info') }}</h4>
                    </div>
                    <form action="{{ URL::to('/profile/updateSalla') }}" method="post">
                        @csrf
                        <div class="form-group row mains">
                            <label class="col-3 col-form-label">{{ trans('main.store_token') }} :</label>
                            <div class="col-9">
                                <input class="form-control" name="store_token" value="{{ \App\Models\Variable::getVar('SallaStoreToken') }}" placeholder="{{ trans('main.store_token') }}">
                            </div>
                        </div> 
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTN">{{ trans('main.edit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if(\Helper::checkRules('updateZid'))
    <div class="row">
        <div class="col-1"></div>
        <div class="col-10 service second" {{ Request::has('type') && Request::get('type') == 'zid' ? 'style=display:block' : '' }}>
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h4 class="header-title">{{ trans('main.zid_info') }}</h4>
                    </div>
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
                        <hr class="mt-5">
                        <div class="form-group justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTN">{{ trans('main.edit') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}

@section('scripts')
<script src="{{ asset('components/profile_services.js') }}" type="text/javascript"></script>
@endsection
