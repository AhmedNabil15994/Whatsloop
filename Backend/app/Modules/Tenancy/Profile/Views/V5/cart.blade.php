@extends('tenant.Layouts.V5.master')
@section('title',trans('main.myCart'))
@section('styles')
<style type="text/css" media="screen">
    .collapse .d-flex.pt-4{
        border: 1px solid #eee;
    }
    .product-card{
        /*background: #f4f5fd !important;*/
        border: 1px solid #eee !important;
    }
</style>
@endsection

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

{{-- Content --}}
@section('content')
<div class="qutas-parent">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-9">
                <div class="tabs tabs1">
                    <div class="tab tab1 tab2">
                        <input type="hidden" name="start_date" value="{{ $data->start_date }}">
                        @if(!empty($data->memberships))
                        <input type="hidden" name="updated" value="1">
                        <input type="hidden" name="oldMembership" value="{{ Session::get('membership') }}">
                        <div class="box-style">
                            <div class="box-header">
                                <h5>{{ trans('main.membership') }}</h5>
                            </div>
                            <div class="box-content">
                                <div class="row">
                                    @foreach($data->memberships as $membership)
                                    @if($membership->id != 4 && $membership->id != $data->membership->id)
                                    <div class="col-sm-6 col-md-4 col-lg-4 item-parent">
                                        <div class="quta-card">
                                            <h5 class="card-title">{{ trans('main.features') }}</h5>
                                            <ul class="list-unstyled quta-info">
                                                <li class="price monthly" data-tabs="{{ $membership->monthly_after_vat }}">{{ number_format((float)$membership->monthly_price, 2, '.', '') }} <span>{{ trans('main.sar2') }}</span></li
                                                >
                                                <li class="price yearly d-hidden" data-tabs="{{ $membership->annual_after_vat }}">{{ number_format((float)$membership->annual_price, 2, '.', '') }} <span>{{ trans('main.sar2') }}</span></li
                                                >
                                                <li class="feature">
                                                    <img src="{{ asset('V5/images/leftCard.png') }}" alt="" class="bg" />
                                                    {{ (int) $membership->featruesArr[0] }} <span>{{ trans('main.message') }}</span>
                                                </li>
                                                <li class="feature emploe">
                                                    <img src="{{ asset('V5/images/rightCard.png') }}" alt="" class="bg" />
                                                    {{ (int) $membership->featruesArr[1] }} <span>{{ trans('main.employee') }}</span>
                                                </li>
                                                <li class="feature giga">
                                                    <img src="{{ asset('V5/images/cardYellow.png') }}" alt="" class="bg" />
                                                    {{ (int) $membership->featruesArr[2] }} <span>{{ trans('main.gigaB') }}</span>
                                                </li>
                                            </ul>
                                            <div class="card-body">
                                                <h5 class="card-title font-weight">{{ $membership->title }}</h5>
                                                <p class="card-text price monthly">{{ trans('main.monthly') }}</p>
                                                <p class="card-text price yearly d-hidden">{{ trans('main.yearly') }}</p>

                                                <span class="type d-hidden">{{ trans('main.membership') }}</span>
                                                <button class="btnStyle cartButton {{ Request::get('membership_id') == $membership->id ? 'added active' : 'add' }}" data-cols="{{ $membership->id }}" data-area="membership">
                                                    @if(Request::get('membership_id') == $membership->id)
                                                        {{ trans('main.addedToCart') }}
                                                    @else
                                                        {{ trans('main.addToCart') }}
                                                    @endif
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                                      <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                                        <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                                        <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                                      </g>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($data->addons))
                        <div class="box-style">
                            <div class="box-header">
                              <h5>{{ trans('main.addons') }}</h5>
                            </div>
                            <div class="box-content">
                                <div class="row">
                                    @php
                                    $addonsArr = [
                                        ['color' => '#252F8D','image' => 'Page-1.png'],
                                        ['color' => '#373FBC','image' => 'chat (1).png'],
                                        ['color' => '#2B78C5','image' => 'chat-group.png'],
                                        ['color' => '#551D74','image' => 'zid_full_logo.png'],
                                        ['color' => '#A2EFE5','image' => 'Layer 2.png'],
                                        ['color' => '#FFBEA4','image' => '1200px-Zapier_logo.svg.png'],
                                        ['color' => '#79B55B','image' => 'shopify-ecommerce-for-sale.png'],
                                        ['color' => '#0E6177','image' => 'logo-design.png'],
                                        ['color' => '#5CCAD2','image' => 'layers.png'],
                                        ['color' => '#373FBC','image' => 'Page-1.png'],
                                    ];
                                    @endphp
                                    @foreach($data->addons as $key => $addon)
                                    <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 item-parent">
                                        <div class="adding-card">
                                            <div class="card-img" style="background-color: {{ $addonsArr[$addon->id - 1]['color'] }};">
                                                <img src="{{ asset('V5/images/'.$addonsArr[$addon->id - 1]['image']) }}" alt="">
                                            </div>
                                            <div class="card-body">
                                                {{-- <span class="tooltip">تساعدك هذه الإضافة في ارسال تنبيهات لعملائك</span> --}}
                                                <div class="cardtop">
                                                    <h5 class="card-title">{{ $addon->title }}</h5>
                                                    <p class="card-text price monthly" data-tabs="{{ $addon->monthly_after_vat }}">{{ number_format((float)$addon->monthly_price, 2, '.', '') }} <span>{{ trans('main.sar2') }}</span></p>
                                                    <p class="card-text price yearly d-hidden" data-tabs="{{ $addon->annual_after_vat }}">{{ number_format((float)$addon->annual_price, 2, '.', '') }} <span>{{ trans('main.sar2') }}</span></p>
                                                </div>
                                                <span class="type d-hidden">{{ trans('main.addon') }}</span>
                                                <button class="btnStyle cartButton add" data-cols="{{ $addon->id }}" data-area="addon">
                                                    {{ trans('main.addToCart') }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                                      <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                                        <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                                        <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                                      </g>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        @if(!empty($data->extraQuotas))
                        <div class="box-style">
                            <div class="box-header">
                                <h5>{{ trans('main.extraQuotas') }}</h5>
                            </div>
                            <div class="box-content">
                                <div class="row">
                                    @php
                                    $extraQuotasArr = [
                                        ['color' => '#449DE6','image' => 'text-message.png'],
                                        ['color' => '#FEE45A','image' => 'portfolio.png'],
                                        ['color' => '#AEEEFF','image' => 'server.png'],
                                    ];
                                    @endphp
                                
                                    @foreach($data->extraQuotas as $key => $extraQuota)
                                    <div class="col-sm-6 col-md-4 col-lg-3 col-xl-3 item-parent">
                                        <div class="adding-card">
                                            <div class="card-img" style="background-color: {{ $extraQuotasArr[$key]['color'] }};">
                                                <img src="{{ asset('V5/images/'.$extraQuotasArr[$key]['image']) }}" alt="">
                                            </div>
                                            <div class="card-body">
                                                <span class="tooltip">{{ $extraQuota->extra_count . ' '.$extraQuota->extraTypeText . ' ' . ($extraQuota->extra_type == 1 ? trans('main.msgPerDay') : '')}}</span>
                                                <div class="cardtop">
                                                    <h5 class="card-title">{{ $extraQuota->extraTypeText }}</h5>
                                                    <p class="card-text price monthly" data-tabs="{{ $extraQuota->monthly_after_vat }}">{{ number_format((float)$extraQuota->monthly_price, 2, '.', '') }} <span>{{ trans('main.sar2') }}</span></p>
                                                    <p class="card-text price yearly d-hidden" data-tabs="{{ $extraQuota->annual_after_vat }}">{{ number_format((float)$extraQuota->annual_price, 2, '.', '') }} <span>{{ trans('main.sar2') }}</span></p>
                                                </div>
                                                <span class="type d-hidden">{{ trans('main.extra_quota') }}</span>
                                                <button class="btnStyle cartButton add" data-cols="{{ $extraQuota->id }}" data-area="extra_quota">
                                                    {{ trans('main.addToCart') }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                                      <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                                        <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                                        <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                                      </g>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-md-12 col-lg-3">
                <div class="cart-sell cart">
                    <div class="box-style">
                        <div class="box-header">
                            <h5>{{ trans('main.myCart') }} ( <span class="cartCount">0</span> )</h5>
                        </div>
                        <div class="box-content">
                            <input type="hidden" name="addon" value="{{ trans('main.addon') }}">
                            <input type="hidden" name="extra_quota" value="{{ trans('main.extra_quota') }}">
                            <div class="select-type">
                                <div class="selectStyle">
                                    <i class="fa fa-angle-down"></i>
                                    <select name="duration_type" class="selectmenu" id="">
                                        <option value="1" selected>{{ trans('main.monthly') }}</option>
                                        <option value="2">{{ trans('main.yearly') }}</option>
                                    </select>
                                </div>
                            </div>
                            <form class="payments" method="post" action="{{ URL::current() }}">
                                @csrf
                                <input type="hidden" name="data" value="">
                                <input type="hidden" name="totals" value="">
                                <input type="hidden" name="background" value="{{ $data->userCredits }}">
                                <input type="hidden" name="type" value="{{ Request::get('type') }}">
                            </form>
                            <div class="row sellCards">
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="box-style">
                        <div class="box-header">
                            <h5>{{ trans('main.order_sum') }}</h5>
                        </div>
                        <ul class="summersize">
                            
                            <li>
                                <span>{{ trans('main.grandTotal') }}</span>
                                <span>
                                    <span class="grandTotal">{{ number_format((float)0, 2, '.', '') }}</span> {{ trans('main.sar') }}
                                </span>
                            </li>
                            <li>
                                <span>{{ trans('main.estimatedTax') }}</span>
                                <span>
                                    <span class="estimatedTax">{{ number_format((float)0, 2, '.', '') }}</span> {{ trans('main.sar') }}
                                </span>
                            </li>
                            <li class="total">
                                <span>{{ trans('main.total') }}</span>
                                <span>
                                    <span class="total">{{ number_format((float)0, 2, '.', '') }}</span> {{ trans('main.sar') }}
                                </span>
                            </li>
                        </ul>
                        <div class="pay">
                            <button href="" class="btnStyle checkout">
                                <span>{{ trans('main.checkout') }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414">
                                  <g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)">
                                    <path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path>
                                    <path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path>
                                  </g>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('V5/components/userCart.js') }}" type="text/javascript"></script>
@endsection
