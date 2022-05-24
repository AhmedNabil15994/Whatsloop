@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.paymentError'))
@section('styles')
<style type="text/css" media="screen">
    .timer .Attention svg{
        top: 7px;
    }
    .timer .titleTimer{
        margin-top: 20px;
    }
</style>
@endsection

{{-- Content --}}
@section('content')
<div class="stats">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="timer">
                <img src="{{ asset('V5/images/img_8.png') }}" alt="">
                <h2 class="titleTimer">{{ trans('main.paymentFailed') }}</h2>
                <div class="desc">
                    {{ trans('main.paymentFailedP') }}
                </div>
                <div class="Attention">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32.001" height="39.205" viewBox="0 0 32.001 39.205">
                      <path id="XMLID_560_" d="M61.928,39.205A13.2,13.2,0,0,1,50.92,33.488L43.514,22.639A3.1,3.1,0,0,1,45.8,17.884a6.285,6.285,0,0,1,5.776,2.656V7.555a3.7,3.7,0,0,1,3.767-3.63,3.888,3.888,0,0,1,1.085.153V3.855a4.008,4.008,0,0,1,8.01,0V4.2a4.032,4.032,0,0,1,1.228-.19,3.844,3.844,0,0,1,3.91,3.765v.685a4.177,4.177,0,0,1,1.371-.23A3.983,3.983,0,0,1,75,12.135V26.791c0,6.845-5.864,12.415-13.073,12.415ZM46.5,20.526a3.555,3.555,0,0,0-.4.022c-.274.031-.561.3-.372.579l7.409,10.853,0,.006a10.519,10.519,0,0,0,8.786,4.537c5.73,0,10.391-4.367,10.391-9.734V12.135a1.38,1.38,0,0,0-2.741,0v8.779a1.341,1.341,0,0,1-2.682,0V7.78a1.237,1.237,0,0,0-2.456,0V20.734a1.341,1.341,0,0,1-2.682,0V3.855a1.332,1.332,0,0,0-2.646,0v16.7a1.341,1.341,0,0,1-2.682,0v-13a1.095,1.095,0,0,0-2.17,0V24.484a1.355,1.355,0,0,1-2.4.817l-2.692-3.5A3.359,3.359,0,0,0,46.5,20.526Z" transform="translate(-43)"></path>
                    </svg>
                    {{ trans('main.dashboard') }}
                </div>
            </div>
        </div>
    </div>         
</div>
@endsection