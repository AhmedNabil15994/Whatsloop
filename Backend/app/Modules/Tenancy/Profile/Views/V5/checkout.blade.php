@extends('tenant.Layouts.V5.master')
@section('title',trans('main.checkout'))
@section('styles')
<!---Internal Fileupload css-->
<link href="{{ asset('V5/plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css"/>

<!---Internal Fancy uploader css-->
<link href="{{ asset('V5/plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
<style type="text/css" media="screen">
    .select2-container .select2-selection--single{
        margin-bottom: 25px;
    }
</style>
@endsection


{{-- Content --}}
@section('content')
<!-- Start Content-->
<div class="payment">
    <div class="steps">
        <div class="step active" data-target="#step1">
            <i class="icon flaticon-shopping-cart"></i>
            {{ trans('main.myCart') }}
        </div>
        <div class="step" data-target="#step2">
            <i class="icon flaticon-invoice-1"></i>
            {{ trans('main.financial_setting') }}
        </div>
        <div class="step" data-target="#step3">
            <i class="icon flaticon-credit-card"></i>
            {{ trans('main.payment_setting') }}
        </div>
    </div>
    
    <div id="step1" class="paySteps active ">
        <div class="overflowTable">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>{{ trans('main.product') }}</th>
                        <th>{{ trans('main.quantity') }}</th>
                        <th>{{ trans('main.price') }}</th>
                        <th>{{ trans('main.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data->data as $oneItem)
                    <tr class="tableRow" data-cols="{{ $oneItem[0] }}" data-type="{{ $oneItem[1] }}" data-dur="{{ $oneItem[3] }}">
                        @php 
                            $itemImage = '';
                            $key = $oneItem[0] - 1;
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
                            ];
                            $extraQuotasArr = [
                                ['color' => '#449DE6','image' => 'text-message.png'],
                                ['color' => '#FEE45A','image' => 'portfolio.png'],
                                ['color' => '#AEEEFF','image' => 'server.png'],
                            ];
                            if($oneItem[1] == 'membership'){
                                $itemImage = asset('V5/images/sell.png');
                            }elseif($oneItem[1] == 'addon'){
                                $itemImage = asset('V5/images/'.$addonsArr[$key]['image']);
                            }elseif($oneItem[1] == 'extra_quota'){
                                $itemImage = asset('V5/images/'.$extraQuotasArr[$key]['image']);
                            }
                        @endphp
                        <td>
                            <div class="details">
                                <img src="{{ $itemImage }}" alt="" />
                                <h2 class="title">{{ $oneItem[2] }}</h2>
                                <p class="type">{{ trans('main.extra_type') }} : <span>{{ trans('main.'.$oneItem[1]) }}</span></p>
                                <p class="type">{{ trans('main.subscription') }} : <span>{{ $oneItem[4] }} <b>-</b> {{ $oneItem[5] }}</span></p>
                            </div>
                        </td>
                        <td>1</td>
                        <td class="prices"><span class="price">{{ $oneItem[1] == 'extra_quota' ?  number_format((float)$oneItem[6] * $oneItem[7], 2, '.', '') : number_format((float)$oneItem[6], 2, '.', '') }}</span> {{ trans('main.sar') }}</td>
                        <td>
                            @if($oneItem[1] != 'membership')
                            <a class="remove rmv"><i class="flaticon-trash"></i></a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <ul class="listTotal">
            <li class="clearfix">{{ trans('main.subTotal') }} <span><b class="grandTotal">{{ $data->totals[0] }}</b> {{ trans('main.sar') }}</span></li>
            <li class="clearfix d-hidden">{{ trans('main.discount') }} <span><b class="discount">{{ number_format((float)$data->totals[1], 2, '.', '') }}</b> {{ trans('main.sar') }}</span></li>
            <li class="clearfix">{{ trans('main.estimatedTax') }} <span><b class="estimatedTax">{{ $data->totals[2] }}</b> {{ trans('main.sar') }}</span></li>
        </ul>

        <div class="totalConfirm">
            <h2 class="title clearfix">{{ trans('main.total') }} <span><b class="total">{{ number_format((float)$data->totals[3], 2, '.', '') }}</b> {{ trans('main.sar') }}</span></h2>
            <div class="clearfix">
                <form class="coupon">
                    <input type="number" placeholder="{{ trans('main.couponCode') }}" />
                    <button>{{ trans('main.apply') }}</button>
                </form>
                <center>
                    <div class="nextPrev clearfix">
                        <button class="btnNext btnPrev" disabled>{{ trans('main.prev') }}</button>
                        <button class="btnNext">{{ trans('main.next') }}</button>
                    </div>
                </center>
            </div>
        </div>
    </div>

    <div id="step2" class="paySteps">
        <form class="formPayment completeOrder" action="{{ Request::segment(1) == 'invoices' ? URL::current() : URL::to('/completeOrder') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <label>{{ trans('main.name2') }}</label>
                    <input type="text" name="name" value="{{ $data->user->name }}" placeholder="{{ trans('main.name2') }}"/>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.company_name') }}</label>
                    <input type="text" name="company_name" value="{{ $data->user->company }}" placeholder="{{ trans('main.company_name') }}"/>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.address') }}</label>
                    <input type="text" name="address" value="{{ isset($data->payment) ? $data->payment->address : '' }}" placeholder="{{ trans('main.address') }}"/>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.address') }} 2</label>
                    <input type="text" name="address2" value="{{ isset($data->payment) ? $data->payment->address2 : '' }}" placeholder="{{ trans('main.address') }} 2"/>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.country') }}</label>
                    <select name="country" data-toggle="select2">
                        <option value="">{{ trans('main.choose') }}</option>
                        @foreach($data->countries as $key => $country)
                        @if($key != 'il')
                        <option value="{{ $key }}" {{ isset($data->payment) ? ($data->payment->country == $key ? 'selected' : '') : '' }} >{{ LANGUAGE_PREF == 'ar' ? $country['native_official_name'] : $country['official_name'] }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.region') }}</label>
                    <select name="region" data-toggle="select2">
                        <option value="">{{ trans('main.choose') }}</option>
                        @foreach($data->regions as $key => $region)
                        <option value="{{ $key }}" {{ isset($data->payment) ? ($data->payment->region == $key ? 'selected' : '') : '' }} >{{ $region['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.city') }}</label>
                    <input type="text" name="city" value="{{ isset($data->payment) ? $data->payment->city : '' }}" placeholder="{{ trans('main.city') }}"/>
                </div>
                <div class="col-md-6">
                    <label>{{ trans('main.postal_code') }}</label>
                    <input type="text" name="postal_code" value="{{ isset($data->payment) ? $data->payment->postal_code : '' }}" placeholder="{{ trans('main.postal_code') }}"/>
                </div>
                <div class="col-md-12">
                    <label>{{ trans('main.tax_id') }}</label>
                    <input type="text" name="tax_id" value="{{ isset($data->payment) ? $data->payment->tax_id : '' }}" placeholder="{{ trans('main.tax_id') }}"/>
                </div>
            </div>
            <input type="hidden" name="payType" value="">
            <input type="hidden" name="totals" value="{{ json_encode($data->totals) }}">
            <input type="hidden" name="data" value="{{ json_encode($data->data) }}">
            <div class="totalConfirm">
                <center>
                    <div class="nextPrev clearfix">
                        <button class="btnNext btnPrev">{{ trans('main.prev') }}</button>
                        <button class="btnNext">{{ trans('main.next') }}</button>
                    </div>
                </center>
            </div>
        </form>
    </div>    

    <div id="step3" class="paySteps">
        <form class="selectPayment">
            <h2 class="title">{{ trans('main.payment_setting') }}</h2>
            <div class="row">
                <div class="col-md-6">
                    <div class="paymentStyle" data-area="2">
                        <h2 class="titleSelect">{{ trans('main.ePayment') }}</h2>
                        <ul class="listPayment clearfix">
                            <li><a href="#"><img src="{{ asset('V5/images/payment1.png') }}" alt="mada" /></a></li>
                            <li><a href="#"><img src="{{ asset('V5/images/payment2.png') }}" alt="visa" /></a></li>
                            <li><a href="#"><img src="{{ asset('V5/images/payment3.png') }}" alt="mastercard" /></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="paymentStyle" data-area="1">
                        <h2 class="titleSelect">{{ trans('main.bankTransfer') }}</h2>
                        <label class="labelUpload">
                            <span>{{ trans('main.attachTransferImage') }}</span>
                            <input type="file" />
                            <i class="flaticon-upload"></i>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="totalConfirm clearfix">
                <center>
                    <div class="nextPrev clearfix">
                        <button class="btnNext btnPrev">{{ trans('main.prev') }}</button>
                    </div>
                </center>
            </div>
        </form>
    </div>    

</div>

@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('V5/components/checkout.js') }}" type="text/javascript"></script>
@endsection