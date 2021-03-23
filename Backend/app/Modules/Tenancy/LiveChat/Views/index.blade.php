{{-- Extends layout --}}
@extends('tenant.Layouts.master')
@section('title',trans('main.livechat'))

@section('extra-metas')
    <meta name="userID" content="{{ USER_ID }}">
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
@endsection

@section('content')
<div id="app">
    <chat-application></chat-application>
</div>
@endsection

{{-- Scripts Section --}}

@section('scripts')
    <script src="{{mix('js/app.js')}}"></script>
@endsection
