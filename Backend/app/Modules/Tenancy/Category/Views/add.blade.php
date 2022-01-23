{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
    .select2-container--default .select2-results__option[aria-selected=true],
    .select2-container--default .select2-results__option--highlighted[aria-selected]{
        background-color: unset;
    }
    .select2-results ul li{
        color: #FFF !important;
    }
    .select2-results ul li:nth-child(1){
      background-color: #ddd !important;
    }
    .select2-results ul li:nth-child(2){
      background-color: #ff9dff !important;
    }

    .select2-results ul li:nth-child(3){
      background-color: #d3a91d !important;
    }

    .select2-results ul li:nth-child(4){
      background-color: #6d7cce !important;
    }

    .select2-results ul li:nth-child(5){
      background-color: #d7e752 !important;
    }

    .select2-results ul li:nth-child(6){
      background-color: #00d0e2 !important;
    }

    .select2-results ul li:nth-child(7){
      background-color: #ffc5c7 !important;
    }

    .select2-results ul li:nth-child(8){
      background-color: #93ceac !important;
    }

    .select2-results ul li:nth-child(9){
      background-color: #f74848 !important;
    }

    .select2-results ul li:nth-child(10){
      background-color: #00a0f2 !important;
    }

    .select2-results ul li:nth-child(11){
      background-color: #83e422 !important;
    }

    .select2-results ul li:nth-child(12){
      background-color: #ffaf04 !important;
    }

    .select2-results ul li:nth-child(13){
      background-color: #b5ebff !important;
    }

    .select2-results ul li:nth-child(14){
      background-color: #9ba6ff !important;
    }

    .select2-results ul li:nth-child(15){
      background-color: #ff9485 !important;
    }

    .select2-results ul li:nth-child(16){
      background-color: #64c4ff !important;
    }

    .select2-results ul li:nth-child(17){
      background-color: #ffd429 !important;
    }

    .select2-results ul li:nth-child(18){
      background-color: #dfaef0 !important;
    }

    .select2-results ul li:nth-child(19){
      background-color: #99b6c1 !important;
    }

    .select2-results ul li:nth-child(20){
      background-color: #55ccb3 !important;
    }
</style>
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="form">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <form class="formPayment" method="POST" action="{{ URL::to('/categories/create') }}">
                        @csrf
                        <input type="hidden" name="status">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="titleLabel">{{ trans('main.color') }} :</label>                            
                            </div>
                            <div class="col-md-9">
                                <div class="selectStyle colors">
                                    <select data-toggle="select2" data-style="btn-outline-myPR" name="color_id">
                                        <option value="">{{ trans('main.choose') }}</option>
                                        @foreach($data->colors as $color)
                                        <option value="{{ $color['id'] }}" style="background: {{ $color['color'] }}">{{ $color['title'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div> 
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.titleAr') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('name_ar') }}" name="name_ar" placeholder="{{ trans('main.titleAr') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="inputPassword3" class="titleLabel">{{ trans('main.titleEn') }} :</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" value="{{ old('name_en') }}" name="name_en" placeholder="{{ trans('main.titleEn') }}">
                            </div>
                        </div>
                        <hr class="mt-5">
                        <div class="row">
                            <div class="col-xs-12 text-right">
                                <div class="nextPrev clearfix ">
                                    <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btnNext Reset">{{ trans('main.back') }}</a>
                                    <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.add') }}</button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('topScripts')
<script src="{{ asset('V5/components/phone.js') }}"></script>
<script src="{{ asset('V5/components/addBot.js') }}"></script>
@endsection