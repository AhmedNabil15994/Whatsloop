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
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="timer">
                <img src="{{ asset('V5/images/checkImg.png') }}" alt="">
                <h2 class="titleTimer">{{ $data->msg }}</h2>
                <div class="desc">
                    {{ trans('main.yourOrderNo') }}: 
                    {{ $data->transfer->order_no }}
                </div>
                <div class="Attention">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32.001" height="39.205" viewBox="0 0 32.001 39.205">
                      <path id="XMLID_560_" d="M61.928,39.205A13.2,13.2,0,0,1,50.92,33.488L43.514,22.639A3.1,3.1,0,0,1,45.8,17.884a6.285,6.285,0,0,1,5.776,2.656V7.555a3.7,3.7,0,0,1,3.767-3.63,3.888,3.888,0,0,1,1.085.153V3.855a4.008,4.008,0,0,1,8.01,0V4.2a4.032,4.032,0,0,1,1.228-.19,3.844,3.844,0,0,1,3.91,3.765v.685a4.177,4.177,0,0,1,1.371-.23A3.983,3.983,0,0,1,75,12.135V26.791c0,6.845-5.864,12.415-13.073,12.415ZM46.5,20.526a3.555,3.555,0,0,0-.4.022c-.274.031-.561.3-.372.579l7.409,10.853,0,.006a10.519,10.519,0,0,0,8.786,4.537c5.73,0,10.391-4.367,10.391-9.734V12.135a1.38,1.38,0,0,0-2.741,0v8.779a1.341,1.341,0,0,1-2.682,0V7.78a1.237,1.237,0,0,0-2.456,0V20.734a1.341,1.341,0,0,1-2.682,0V3.855a1.332,1.332,0,0,0-2.646,0v16.7a1.341,1.341,0,0,1-2.682,0v-13a1.095,1.095,0,0,0-2.17,0V24.484a1.355,1.355,0,0,1-2.4.817l-2.692-3.5A3.359,3.359,0,0,0,46.5,20.526Z" transform="translate(-43)"></path>
                    </svg>
                    {{ trans('main.contactBSCustomer') }}
                    <br>
                    <span dir="ltr">{{ $data->phone }}</span>
                </div>
                <form class="completeJob" action="{{ URL::to('/completeJob') }}" method="post">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    @livewire('activate-account',['transfer_order_no'=>$data->transfer->order_no])
    @section('scripts')
    <script>
    Livewire.on('activateAccount', postId => {
        $('form.completeJob').submit();
    })
    </script>
    @endsection
    @endif
    <!-- end row -->
@endsection

{{-- Scripts Section --}}
@section('scripts')

@endsection
