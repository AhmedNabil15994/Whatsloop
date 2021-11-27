{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])

@section('styles')
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
<style type="text/css" media="screen">
    .formPayment img.designStyle{
        width: 100%;
        height: 300px;
        border-radius: 5px;
        cursor: pointer;
        transition: all ease-in-out 0.25s;
        -webkit-transition: all ease-in-out 0.25s;
        -moz-transition: all ease-in-out 0.25s;
        -o-transition: all ease-in-out 0.25s;
    }
    .formPayment img.designStyle:hover{
        transform: scale(1.1);
    }
    .formPayment img.designStyle.selected{
        border: 2px solid #ccc;
    }
</style>
@endsection

@section('content')


<input type="hidden" name="designElems" value="{{ json_encode($data->designElems) }}">


<form action="{{ URL::current() }}" method="post" accept-charset="utf-8">
    @csrf
    <div class="row">
        <div class="form">
            <div class="col-xs-12">
                <h2 class="title">{{ trans('main.selectDesign') }}</h2>
            </div>
            <div class="formPayment">
                <div class="row mt-3">
                    <div class="col-md-4" data-area="1">
                        <img class="designStyle {{ $data->design == 1 ? 'selected' : '' }}" src="{{ asset('V5/images/design1.png') }}" alt="">
                    </div>
                    <div class="col-md-4" data-area="2">
                        <img class="designStyle {{ $data->design == 2 ? 'selected' : '' }}" src="{{ asset('V5/images/design2.png') }}" alt="">
                    </div>
                    <div class="col-md-4" data-area="3">
                        <img class="designStyle {{ $data->design == 3 ? 'selected' : '' }}" src="{{ asset('V5/images/design3.png') }}" alt="">
                    </div>
                    <input type="hidden" name="design">
                </div>  
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form">
            <div class="col-xs-12">
                <h2 class="title">{{ trans('main.selectInvoice') }}</h2>
            </div>
            <div class="formPayment">
                <div class="row mt-3">
                    <div class="col-md-3" data-area="1">
                        <img class="designStyle {{ $data->invoice == 1 ? 'selected' : '' }}" src="{{ asset('V5/images/invoice1.png') }}" alt="">
                    </div>
                    <div class="col-md-3" data-area="2">
                        <img class="designStyle {{ $data->invoice == 2 ? 'selected' : '' }}" src="{{ asset('V5/images/invoice2.png') }}" alt="">
                    </div>
                    <div class="col-md-3" data-area="3">
                        <img class="designStyle {{ $data->invoice == 3 ? 'selected' : '' }}" src="{{ asset('V5/images/invoice3.png') }}" alt="">
                    </div>
                    <div class="col-md-3" data-area="4">
                        <img class="designStyle {{ $data->invoice == 4 ? 'selected' : '' }}" src="{{ asset('V5/images/invoice4.png') }}" alt="">
                    </div>
                    <input type="hidden" name="invoice">
                </div>  
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="nextPrev clearfix ">
                <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.apply') }}</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</form>

@include('tenant.Partials.pagination')
@endsection
