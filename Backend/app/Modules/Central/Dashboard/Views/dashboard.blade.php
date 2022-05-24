@extends('central.Layouts.master')
@section('title','الصفحة الرئيسية')
@section('styles')
<style>
    .card-box{
        display: block;
        width: 100%;
        margin-top: 250px;
    }
    .text-da{
        color: #333;
    }
</style>
@endsection


{{-- Content --}}
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row text-center">
        <div class="card-box">
            <img class="mb-3" src="{{asset('tenancy/assets/V5/images/logo.png')}}" alt="">
            <h5 class="mt-0 text-da font-16">مرحبا بك مجددا.. نتمنى لك يوما سعيدا</h5>
        </div>
    </div>
    <!-- end row -->
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('tenancy/assets/js/pages/dashboard-3.init.js') }}"></script>
@endsection
