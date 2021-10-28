@extends('tenant.Layouts.V5.master')
@section('title',trans('main.bundles'))
@section('styles')
<style type="text/css" media="screen">
    .features{
        /*min-height: 260px;*/
    }
    .features.unlim{
        min-height: 395px;
    }
    .features.unlim .card-body.text-center.border-top{
        padding-bottom: 3.5rem;
        border-bottom: 1px solid #edeef7 !important;
    }
</style>
@endsection

@if(!isset($data->msg) || empty($data->msg))
    @section('ExtraBreadCrumb')
    <div class="container">
        <div class="breadcrumb-content">
            <h5>{{ trans('main.packages_h') }}</h5>
            <p>{{ trans('main.packages_p') }}</p>
            <ul class="link clearfix btnsTabs" id="tabs1">
                <li id="tab1" class="active">{{ trans('main.monthly') }}</li>
                <li id="tab2">{{ trans('main.yearly') }}</li>
            </ul>
        </div>
    </div>
    @endsection
@endif
{{-- Content --}}
@section('content')
    

    <!-- row -->
    @if(!isset($data->msg) || empty($data->msg))
    <div class="qutas-ards">
        <div class="container">
            <div class="tabs tabs1">
                <div class="tab tab1">
                    <div class="row">
                        @foreach($data->bundles as $key => $bundle)

                        @php
                            $imageName = ''; 
                            if($key == 0){
                                $imageName = "api.png";
                            }elseif($key == 1){
                                $imageName = "comments (1).png";
                            }elseif($key == 2){
                                $imageName = "chat-group (1).png";
                            }elseif($key == 3){
                                $imageName = "Page-1w.png";
                            }elseif($key == 4){
                                $imageName = "zid_full_logo2.png";
                            }elseif($key == 5){
                                $imageName = "Layer 22.png";
                            }
                        @endphp

                        @if($key != 6)
                        <div class="col-sm-6 col-md-6 col-lg-4">
                            <div class="mainquta-card">
                                <div class="card-inner">
                                    <div class="card-img">
                                        <img src="{{ asset('V5/images/'.$imageName) }}" alt="">
                                    </div>
                                    <h5 class="card-title">{{ $bundle->title }}</h5>
                                    <div class="card-text">
                                        <h5>{{ $bundle->monthly_after_vat }}</h5>
                                        <span>{{ trans('main.sar2') }}</span>
                                    </div>
                                    <p class="card-slag">{{ trans('main.packageP') }}</p>
                                    <a class="btnStyle card-link">
                                        {{ trans('main.subscribe') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                          <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                            <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                            <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                          </g>
                                        </svg>
                                    </a>
                                </div>
                                <div class="card-back ">
                                    <div class="card-img">
                                        <img src="{{ asset('V5/images/'.$imageName) }}" alt="">
                                    </div>
                                    <h5 class="card-title">{{ $bundle->title }}</h5>
                                    <div class="card-text">{!! $bundle->description !!}</div>
                                    <p class="card-slag">{{ trans('main.packageP') }}</p>
                                    <a class="btnStyle card-link" href="{{ URL::to('/postBundle/'.$bundle->id) }}">
                                        {{ trans('main.subscribe') }} {{ $bundle->monthly_after_vat }} {{ trans('main.sar2') }}
                                        <span class="swipe">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                              <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                                <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#000" stroke-width="2"/>
                                                <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#000" stroke-width="2" opacity="0.6"/>
                                              </g>
                                            </svg>
                                        </span>
                                        <span class="pay">{{ trans('main.pay') }} {{ $bundle->monthly_after_vat }} {{ trans('main.sar2') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-sm-12 col-lg-12">
                            <div class="offer-card">
                                <img class="offerImg commment1" src="{{ asset('V5/images/commment-1.png') }}" alt="">
                                <img class="offerImg commment2" src="{{ asset('V5/images/comment-2.png') }}" alt="">
                                <img class="offerImg commment3" src="{{ asset('V5/images/comment-3.png') }}" alt="">
                                <img class="offerImg robot" src="{{ asset('V5/images/robot.png') }}" alt="">
                                <h5 class="card-title">يمكنك انشاء باقتك المخصصة بمميزات متعددة وأسعار مناسبة</h5>
                                <p class="card-text">
                                    <span>ابتدا من</span>
                                    <span class="price">{{ $bundle->monthly_after_vat }} <span>{{ trans('main.sar2') }}</span></span>
                                </p>
                                <p class="card-slag">{{ trans('main.packageP') }}</p>
                                <a href="{{ URL::to('/checkout?membership_id=1') }}" class="btnStyle card-link">
                                    {{ trans('main.subscribe') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                      <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                        <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                        <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                      </g>
                                    </svg>
                                </a>
                          </div>
                        </div>
                        @endif

                        @endforeach
                    </div>
                </div>
                <div class="tab tab2">
                    @foreach($data->bundles as $key => $bundle)
                        
                        @php
                            $imageName = ''; 
                            if($key == 0){
                                $imageName = "api.png";
                            }elseif($key == 1){
                                $imageName = "comments (1).png";
                            }elseif($key == 2){
                                $imageName = "chat-group (1).png";
                            }elseif($key == 3){
                                $imageName = "Page-1w.png";
                            }elseif($key == 4){
                                $imageName = "zid_full_logo2.png";
                            }elseif($key == 5){
                                $imageName = "Layer 22.png";
                            }
                        @endphp

                        @if($key != 6)
                        <div class="col-sm-6 col-md-6 col-lg-4">
                            <div class="mainquta-card">
                                <div class="card-inner">
                                    <div class="card-img">
                                        <img src="{{ asset('V5/images/'.$imageName) }}" alt="">
                                    </div>
                                    <h5 class="card-title">{{ $bundle->title }}</h5>
                                    <div class="card-text">
                                        <h5>{{ $bundle->annual_after_vat }}</h5>
                                        <span>{{ trans('main.sar2') }}</span>
                                    </div>
                                    <p class="card-slag">{{ trans('main.packageP') }}</p>
                                    <a class="btnStyle card-link">
                                        {{ trans('main.subscribe') }}
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                          <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                            <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                            <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                          </g>
                                        </svg>
                                    </a>
                                </div>
                                <div class="card-back ">
                                    <div class="card-img">
                                        <img src="{{ asset('V5/images/'.$imageName) }}" alt="">
                                    </div>
                                    <h5 class="card-title">{{ $bundle->title }}</h5>
                                    <div class="card-text">{!! $bundle->description !!}</div>
                                    <p class="card-slag">{{ trans('main.packageP') }}</p>
                                    <a class="btnStyle card-link" href="{{ URL::to('/postBundle/'.$bundle->id) }}">
                                        {{ trans('main.subscribe') }} {{ $bundle->annual_after_vat }} {{ trans('main.sar2') }}
                                        <span class="swipe">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                              <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                                <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#000" stroke-width="2"/>
                                                <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#000" stroke-width="2" opacity="0.6"/>
                                              </g>
                                            </svg>
                                        </span>
                                        <span class="pay">{{ trans('main.pay') }} {{ $bundle->annual_after_vat }} {{ trans('main.sar2') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="col-sm-12 col-lg-12">
                            <div class="offer-card">
                                <img class="offerImg commment1" src="{{ asset('V5/images/commment-1.png') }}" alt="">
                                <img class="offerImg commment2" src="{{ asset('V5/images/comment-2.png') }}" alt="">
                                <img class="offerImg commment3" src="{{ asset('V5/images/comment-3.png') }}" alt="">
                                <img class="offerImg robot" src="{{ asset('V5/images/robot.png') }}" alt="">
                                <h5 class="card-title">{{ trans('main.specsMemb') }}</h5>
                                <p class="card-text">
                                    <span>{{ trans('main.startsFrom') }}</span>
                                    <span class="price">{{ $bundle->annual_after_vat }} <span>{{ trans('main.sar2') }}</span></span>
                                </p>
                                <p class="card-slag">{{ trans('main.packageP') }}</p>
                                <a href="{{ URL::to('/checkout?membership_id=1') }}" class="btnStyle card-link">
                                    {{ trans('main.subscribe') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                      <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                        <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                        <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                      </g>
                                    </svg>
                                </a>
                          </div>
                        </div>
                        @endif

                        @endforeach
                </div>
            </div>
        </div>
    </div>

    
    @else
    <div class="row text-center mg-t-100 mg-b-20 d-block">
        {{-- <div class="col-3"></div> --}}
        <div class="col-12 w-auto m-auto d-block">
            <div class="card">
                <div class="card-body">
                    <img src="{{ asset('images/waiting.svg') }}" class="transferSVG" alt="">
                    <h2 class="header-title h2 tx-bold mg-b-40">{{ $data->msg }}</h2>
                    <p class="h3 mg-b-50 text-muted tx-bold">{{ trans('main.yourOrderNo') }} : <span class="tx-black">{{ $data->transfer->order_no }}</span></p>
                    <p class="h3 text-muted tx-bold">{{ trans('main.contactBSCustomer') }} : <span class="tx-black" dir="ltr">{{ $data->phone }}</span></p>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- end row -->
@endsection

{{-- Scripts Section --}}
@section('scripts')

@endsection
