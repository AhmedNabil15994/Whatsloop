@extends('tenant.Layouts.master')
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


{{-- Content --}}
@section('content')
{{-- <div class="container-fluid">
    <div class="row">
        <div class="col-12">
            @if(!Request::has('membership_id'))
            <p>You Must Choose A Package At Least</p>
            @else
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-9">
                            <form class="payments" method="post" action="{{ URL::current() }}">
                                @csrf
                                <input type="hidden" name="data" value="">
                                <input type="hidden" name="totals" value="">
                            </form>
                            <div class="accordion custom-accordion" id="custom-accordion-one">
                                <div class="card mb-0">
                                    <div class="card-header" id="headingNine">
                                        <h5 class="m-0 position-relative">
                                            <a class="custom-accordion-title text-reset d-block collapsed" data-toggle="collapse" href="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                                {{ trans('main.addons') }} <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </a>
                                        </h5>
                                    </div>

                                    <div id="collapseNine" class="collapse" aria-labelledby="headingFour" data-parent="#custom-accordion-one" style="">
                                        <div class="card-body">
                                            
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="card mb-0">
                                    <div class="card-header" id="headingTwo">
                                        <h5 class="m-0 position-relative">
                                            <a class="custom-accordion-title text-reset d-block" data-toggle="collapse" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                            {{ trans('main.extraQuotas') }} <i class="mdi mdi-chevron-down accordion-arrow"></i>
                                            </a>
                                        </h5>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#custom-accordion-one" style="">
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($data->extraQuotas as $one)
                                                <div class="col-4">
                                                    <a href="javascript:void(0);" class="extra" data-cols="{{ $one->id }}" data-area={{ $one->monthly_after_vat }}>
                                                        <div class="card myCard" data-toggle=".second">
                                                            <div class="card-body">
                                                                <h3 class="card-title">{{ $one->extraTypeText }}</h3>
                                                                <p class="details">{{ $one->extra_count . ' '.$one->extraTypeText . ' ' . ($one->extra_type == 1 ? trans('main.msgPerDay') : '')}}</p>
                                                                <p>{{ $one->monthly_after_vat . ' ' . trans('main.sar') . ' '.trans('main.monthly') }}</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                @endforeach
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                            </div>                    
                        </div>
                        <!-- end col -->

                        <div class="col-lg-3">
                            <div class="border p-3 mt-4 mt-lg-0 rounded">
                                <h4 class="header-title mb-3">{{ trans('main.order_sum') }}</h4>

                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <tbody>
                                            <tr>
                                                <td>{{ trans('main.grandTotal') }} :</td>
                                                <td><span class="mainPrices">0</span> {{ trans('main.sar') }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ trans('main.discount') }} : </td>
                                                <td><span class="discount">0</span></td>
                                            </tr>
                                            <tr>
                                                <td>{{ trans('main.estimatedTax') }} : </td>
                                                <td><span class="tax">0</span> {{ trans('main.sar') }}</td>
                                            </tr>
                                            <tr>
                                                <th>{{ trans('main.total') }} :</th>
                                                <th><span class="price">0</span>  {{ trans('main.sar') }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table-responsive -->
                            </div>
                            <div class="text-sm-right mt-3">
                                <a href="#" class="btn btn-danger checkout"><i class="mdi mdi-cart-plus mr-1"></i> {{ trans('main.checkout') }} </a>
                            </div>
                        </div> <!-- end col -->

                    </div> <!-- end row -->
                </div> <!-- end card-body-->
            </div> <!-- end card-->
            @endif
        </div> <!-- end col -->
    </div>

    <!-- end row -->
</div> 
 --}}
<div class="row">
    <div class="col-xl-8 col-lg-8">
        <div aria-multiselectable="true" class="accordion mb-3" id="accordion4" role="tablist">
            <div class="card mb-0">
                <div class="card-header headingOnes border-bottom pb-3 pt-3" id="headingOne4" role="tab">
                    <a aria-controls="collapseOne4" aria-expanded="false" data-toggle="collapse" href="#collapseOne4" class="collapsed card-title">{{ trans('main.memberships') }}</a>
                </div>
                <div aria-labelledby="headingOne4" class="collapse show" data-parent="#accordion4" id="collapseOne4" role="tabpanel" style="">
                    <div class="d-flex pt-4">
                        @foreach($data->memberships as $membership)
                        @if($membership->id != 4)
                        <div class="col-4">
                            <div class="product-card card overflow-hidden">
                                <img class="w-100 mt-0" src="{{ asset('img/ecommerce/01.jpg') }}" alt="product-image">
                                <div class="card-body h-100 pt-4">
                                    <div class="d-flex">
                                        <span class="text-muted small mg-b-5">{{ trans('main.membership') }}</span>
                                    </div>
                                    <h3 class="h6 mb-2 font-weight-bold text-uppercase">{{ $membership->title }}</h3>
                                    <div class="d-block">
                                        <h4 class="h5 w-50 font-weight-bold text-danger mb-0 monthly" data-tabs="{{ $membership->monthly_after_vat }}">{{ $membership->monthly_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                        <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $membership->annual_after_vat }}">{{ $membership->annual_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                                    </div>
                                    <button class="btn btn-primary btn-block cartButton mb-0 mt-4 {{ Request::get('membership_id') == $membership->id ? 'added' : 'add' }}" data-cols="{{ $membership->id }}" data-area="membership">
                                        @if(Request::get('membership_id') == $membership->id)
                                            <i class="fe fe-check mr-1"></i>
                                            {{ trans('main.addedToCart') }}
                                        @else
                                            <i class="fe fe-shopping-cart mr-1"></i>
                                            {{ trans('main.addToCart') }}
                                        @endif
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div aria-multiselectable="true" class="accordion" id="accordion" role="tablist">
            <div class="card mb-0">
                <div class="card-header headingOnes border-bottom pb-3 pt-3" id="headingOne" role="tab">
                    <a aria-controls="collapseOne" aria-expanded="false" data-toggle="collapse" href="#collapseOne" class="collapsed card-title">{{ trans('main.addons') }}</a>
                </div>
                <div aria-labelledby="headingOne" class="collapse" data-parent="#accordion" id="collapseOne" role="tabpanel" style="">
                    <div class="d-flex pt-4">
                        @foreach($data->addons as $key => $addon)
                        @if($key % 3 == 0 )
                        </div>
                        <div class="d-flex pt-4">
                        @endif
                        <div class="col-4">
                            <div class="product-card card overflow-hidden">
                                <img class="w-100 mt-0" src="{{ asset('img/ecommerce/01.jpg') }}" alt="product-image">
                                <div class="card-body h-100 pt-4">
                                    <div class="d-flex">
                                        <span class="text-muted small mg-b-5">{{ trans('main.addon') }}</span>
                                    </div>
                                    <h3 class="h6 mb-2 font-weight-bold text-uppercase">{{ $addon->title }}</h3>
                                    <div class="d-block">
                                        <h4 class="h5 w-50 font-weight-bold text-danger mb-0 monthly" data-tabs="{{ $addon->monthly_after_vat }}">{{ $addon->monthly_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                        <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $addon->annual_after_vat }}">{{ $addon->annual_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                                    </div>
                                    <button class="btn btn-primary cartButton add btn-block mb-0 mt-4" data-cols="{{ $addon->id }}" data-area="addon">
                                        <i class="fe fe-shopping-cart mr-1"></i>
                                        {{ trans('main.addToCart') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-4">
                            <form class="mainForm">
                                <div class="card myCard" data-toggle=".first">
                                    <div class="card-body">
                                        <h3 class="card-title">{{ $addon->title }}</h3>
                                        <div class="row mainCol">
                                            <div class="form-group row">
                                                <div class="col-3" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="monthly{{ $addon->id }}" data-cols="{{ $addon->id }}" data-area="{{ $addon->monthly_after_vat }}" class="monthly" type="checkbox" name="addons[{{ $addon->id }}][1]">
                                                        <label for="monthly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                                <label class="col-8 col-form-label">{{ $addon->monthly_after_vat . ' ' . trans('main.sar') . ' '.trans('main.monthly') }}</label>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-3" style="margin-top: -8px;">
                                                    <div class="checkbox checkbox-success">
                                                        <input id="yearly{{ $addon->id }}" data-cols="{{ $addon->id }}" data-area="{{ $addon->annual_after_vat }}" class="yearly" type="checkbox" name="addons[{{ $addon->id }}][2]">
                                                        <label for="yearly{{ $addon->id }}"></label>
                                                    </div>
                                                </div>
                                                <label class="col-8 col-form-label">{{ $addon->annual_after_vat . ' ' . trans('main.sar') . ' '.trans('main.yearly') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div> --}}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div aria-multiselectable="true" class="accordion border-top pt-3" id="accordion2" role="tablist">
            <div class="card mb-0">
                <div class="card-header headingOnes border-bottom pb-3 pt-3" id="headingOne2" role="tab">
                    <a aria-controls="collapseOne" aria-expanded="false" data-toggle="collapse" href="#collapseOne2" class="collapsed card-title">{{ trans('main.extraQuotas') }}</a>
                </div>
                <div aria-labelledby="headingOne2" class="collapse" data-parent="#accordion2" id="collapseOne2" role="tabpanel" style="">
                    <div class="d-flex pt-4">
                        @foreach($data->extraQuotas as $one)
                        <div class="col-4">
                            <div class="product-card card overflow-hidden">
                                <img class="w-100 mt-0" src="{{ asset('img/ecommerce/01.jpg') }}" alt="product-image">
                                <div class="card-body h-100 pt-4">
                                    <div class="d-flex">
                                        <span class="text-muted small mg-b-5">{{ trans('main.extra_quota') }}</span>
                                    </div>
                                    <h3 class="h6 mb-2 font-weight-bold text-uppercase">{{ $one->extraTypeText }}</h3>
                                    <small class="text-muted tx-13">{{ $one->extra_count . ' '.$one->extraTypeText . ' ' . ($one->extra_type == 1 ? trans('main.msgPerDay') : '')}}</small>
                                    <div class="d-block">
                                        <h4 class="h5 w-50 font-weight-bold text-danger mb-0 monthly" data-tabs="{{ $one->monthly_after_vat }}">{{ $one->monthly_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                        <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $one->annual_after_vat }}">{{ $one->annual_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                                    </div>
                                    <button class="btn btn-primary cartButton add btn-block mb-0 mt-4" data-cols="{{ $one->id }}" data-area="extra_quota">
                                        <i class="fe fe-shopping-cart mr-1"></i>
                                        {{ trans('main.addToCart') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="col-4">
                            <a href="javascript:void(0);" class="extra" data-cols="{{ $one->id }}" data-area={{ $one->monthly_after_vat }}>
                                <div class="card myCard" data-toggle=".second">
                                    <div class="card-body">
                                        <h3 class="card-title">{{ $one->extraTypeText }}</h3>
                                        <p class="details">{{ $one->extra_count . ' '.$one->extraTypeText . ' ' . ($one->extra_type == 1 ? trans('main.msgPerDay') : '')}}</p>
                                        <p>{{ $one->monthly_after_vat . ' ' . trans('main.sar') . ' '.trans('main.monthly') }}</p>
                                    </div>
                                </div>
                            </a>
                        </div> --}}
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4">
        <div class="card cart">
            <div class="card-header border-bottom py-3 d-sm-flex align-items-center">
                <div class="row w-100">
                    <div class="col-6">
                        <h4 class="card-title mg-t-15"><i class="fe fe-shopping-cart mr-1"></i> {{ trans('main.myCart') }}(<span class="cartCount">1</span>)</h4>
                    </div>
                    <div class="col-6">
                        <div class="row w-100">
                            <input type="hidden" name="addon" value="{{ trans('main.addon') }}">
                            <input type="hidden" name="extra_quota" value="{{ trans('main.extra_quota') }}">
                            <div class="font-weight-bold mg-t-10 col-2"><i class="fe fe-clock"></i> </div>
                            <div class="col-10">
                                <select class="form-control" data-toggle="select2" name="duration_type">
                                    <option value="1" selected>{{ trans('main.monthly') }}</option>
                                    <option value="2">{{ trans('main.yearly') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form class="payments" method="post" action="{{ URL::current() }}">
                @csrf
                <input type="hidden" name="data" value="">
                <input type="hidden" name="totals" value="">
            </form>
            <div class="card-body membership" data-cols="{{ $data->membership->id }}">
                
                <div class="media">
                    <div class="card-aside-img">
                        <img src="{{ asset('img/ecommerce/01.jpg') }}" alt="img" class="wd-100-f ht-100 m{{ DIRECTION == 'ltr' ? 'r' : 'l' }}-4">
                    </div>
                    <div class="media-body">
                        <div class="card-item-desc mt-0">
                            <h6 class="font-weight-semibold mt-0 text-uppercase">{{ $data->membership->title }}</h6>
                            <small class="text-muted tx-13"></small>
                            <p class="tx-13 mg-b-5"><b>{{ trans('main.type') }}:</b> {{ trans('main.membership') }} </p>
                            <div class="d-flex">
                                <h4 class="h5 w-50 font-weight-bold text-danger monthly" data-tabs="{{ $data->membership->monthly_after_vat }}">{{ $data->membership->monthly_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $data->membership->annual_after_vat }}">{{ $data->membership->annual_price }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                            </div>
                            <a class="tx-gray-900 tx-uppercase font-weight-bold rmv" href="#">{{ trans('main.remove') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 d-block">
            <div class="card">
                <div class="card-header pd-y-20 border-bottom">
                    <div class="card-title mb-0">{{ trans('main.order_sum') }}</div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-6"><input class="productcart form-control" type="text" placeholder="{{ trans('main.couponCode') }}"></div>
                        <div class="col-6"><a href="#" style="left: unset;bottom: unset;" class="btn btn-primary btn-md">{{ trans('main.apply') }}</a></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>{{ trans('main.grandTotal') }}</td>
                                    <td class="text-right"><span class="grandTotal">{{ $data->membership->monthly_after_vat }}</span> {{ trans('main.sar') }}</td>
                                </tr>
                                <tr>
                                    <td><span>{{ trans('main.estimatedTax') }}</span></td>
                                    <td class="text-right text-muted"><span class="estimatedTax">0</span> {{ trans('main.sar') }}</td>
                                </tr>
                                <tr>
                                    <td><span>{{ trans('main.total') }}</span></td>
                                    <td><h2 class="price text-right mb-0"><p class="mb-0 total d-inline">{{ $data->membership->monthly_after_vat }}</p> {{ trans('main.sar') }}</h2></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-secondary checkout float-right mt-2 m-b-20" value="{{ trans('main.checkout') }}">{{ trans('main.checkout') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- <div class="row">
    <div class="col-xl-7 col-lg-7">
        <div class="card">
            <div class="card-header border-bottom py-3 d-sm-flex align-items-center">
                <div class="row w-100">
                    <div class="col-6">
                        <h4 class="card-title mg-t-15">{{ trans('main.myCart') }}(1)</h4>
                    </div>
                    <div class="col-6">
                        <div class="row w-100">
                            <input type="hidden" name="addon" value="{{ trans('main.addon') }}">
                            <input type="hidden" name="extra_quota" value="{{ trans('main.extra_quota') }}">
                            <div class="font-weight-bold mg-t-10 col-6"><i class="fe fe-map"></i> {{ trans('main.subscriptionPeriod') }}</div>
                            <div class="col-6">
                                <select class="form-control" data-toggle="select2" name="duration_type">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" selected>{{ trans('main.monthly') }}</option>
                                    <option value="2">{{ trans('main.yearly') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(!Request::has('membership_id'))
            <p>You Must Choose A Package At Least</p>
            @else
            <div class="card-body membership">
                <form class="payments" method="post" action="{{ URL::current() }}">
                    @csrf
                    <input type="hidden" name="data" value="">
                    <input type="hidden" name="totals" value="">
                </form>
                <div class="media">
                    <div class="card-aside-img">
                        <img src="{{ asset('img/ecommerce/01.jpg') }}" alt="img" class="wd-100-f ht-100 m{{ DIRECTION == 'ltr' ? 'r' : 'l' }}-4">
                    </div>
                    <div class="media-body">
                        <div class="card-item-desc mt-0">
                            <h6 class="font-weight-semibold mt-0 text-uppercase">{{ $data->membership->title }}</h6>
                            <small class="text-muted tx-13"></small>
                            <p class="tx-13 mg-b-5"><b>{{ trans('main.type') }}:</b> {{ trans('main.membership') }} </p>
                            <div class="d-flex">
                                <h4 class="h5 w-50 font-weight-bold text-danger">{{ $data->membership->monthly_after_vat }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>

                            </div>
                            <a class="tx-gray-900 tx-uppercase font-weight-bold" href="#">{{ trans('main.remove') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div aria-multiselectable="true" class="accordion" id="accordion" role="tablist">
                <div class="card mb-0">
                    <div class="card-header headingOnes border-bottom pb-3" id="headingOne" role="tab">
                        <a aria-controls="collapseOne" aria-expanded="false" data-toggle="collapse" href="#collapseOne" class="collapsed card-title">{{ trans('main.addons') }}</a>
                    </div>
                    <div aria-labelledby="headingOne" class="collapse" data-parent="#accordion" id="collapseOne" role="tabpanel" style="">
                        <div class="card-body">
                            @foreach($data->addons as $addon)
                            <div class="card-body pt-4">
                                <div class="media">
                                    <div class="card-aside-img">
                                        <img src="{{ asset('img/ecommerce/04.jpg') }}" alt="img" class="wd-100-f ht-100 m{{ DIRECTION == 'ltr' ? 'r' : 'l' }}-4">
                                        <div class="d-flex mg-t-10">
                                            <a class="tx-24 mg-t-5"><i class="fe fe-minus-circle"></i></a>
                                            <input type="text" class="form-control form-control-sm text-center wd-50 mg-x-5" value="0" max="1">
                                            <a class="tx-24 mg-t-5"><i class="fe fe-plus-circle"></i></a>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div class="card-item-desc mt-0">
                                            <h6 class="font-weight-semibold mt-0 text-uppercase">{{ $addon->title }}</h6>
                                            <small class="text-muted tx-13"></small>
                                            <p class="tx-13 mg-b-5"><b>{{ trans('main.type') }}:</b> {{ trans('main.addon') }} </p>
                                            <div class="d-flex">
                                                <h4 class="h5 w-50 font-weight-bold text-danger">{{ $addon->monthly_after_vat }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                            </div>
                                            <a class="tx-gray-900 tx-uppercase font-weight-bold" href="#">{{ trans('main.remove') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <form class="mainForm">
                                    <div class="card myCard" data-toggle=".first">
                                        <div class="card-body">
                                            <h3 class="card-title">{{ $addon->title }}</h3>
                                            <div class="row mainCol">
                                                <div class="form-group row">
                                                    <div class="col-3" style="margin-top: -8px;">
                                                        <div class="checkbox checkbox-success">
                                                            <input id="monthly{{ $addon->id }}" data-cols="{{ $addon->id }}" data-area="{{ $addon->monthly_after_vat }}" class="monthly" type="checkbox" name="addons[{{ $addon->id }}][1]">
                                                            <label for="monthly{{ $addon->id }}"></label>
                                                        </div>
                                                    </div>
                                                    <label class="col-8 col-form-label">{{ $addon->monthly_after_vat . ' ' . trans('main.sar') . ' '.trans('main.monthly') }}</label>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-3" style="margin-top: -8px;">
                                                        <div class="checkbox checkbox-success">
                                                            <input id="yearly{{ $addon->id }}" data-cols="{{ $addon->id }}" data-area="{{ $addon->annual_after_vat }}" class="yearly" type="checkbox" name="addons[{{ $addon->id }}][2]">
                                                            <label for="yearly{{ $addon->id }}"></label>
                                                        </div>
                                                    </div>
                                                    <label class="col-8 col-form-label">{{ $addon->annual_after_vat . ' ' . trans('main.sar') . ' '.trans('main.yearly') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div aria-multiselectable="true" class="accordion border-top pt-3" id="accordion2" role="tablist">
                <div class="card mb-0">
                    <div class="card-header headingOnes border-bottom pb-3" id="headingOne2" role="tab">
                        <a aria-controls="collapseOne" aria-expanded="false" data-toggle="collapse" href="#collapseOne2" class="collapsed card-title">{{ trans('main.extraQuotas') }}</a>
                    </div>
                    <div aria-labelledby="headingOne2" class="collapse" data-parent="#accordion2" id="collapseOne2" role="tabpanel" style="">
                        <div class="card-body">
                            @foreach($data->extraQuotas as $one)
                            <div class="card-body pt-4">
                                <div class="media">
                                    <div class="card-aside-img">
                                        <img src="{{ asset('img/ecommerce/04.jpg') }}" alt="img" class="wd-100-f ht-100 m{{ DIRECTION == 'ltr' ? 'r' : 'l' }}-4">
                                        <div class="d-flex mg-t-10">
                                            <a class="tx-24 mg-t-5"><i class="fe fe-minus-circle"></i></a>
                                            <input type="text" class="form-control form-control-sm text-center wd-50 mg-x-5" value="0" max="5">
                                            <a class="tx-24 mg-t-5"><i class="fe fe-plus-circle"></i></a>
                                        </div>
                                    </div>
                                    <div class="media-body">
                                        <div class="card-item-desc mt-0">
                                            <h6 class="font-weight-semibold mt-0 text-uppercase">{{ $one->extraTypeText }}</h6>
                                            <small class="text-muted tx-13">{{ $one->extra_count . ' '.$one->extraTypeText . ' ' . ($one->extra_type == 1 ? trans('main.msgPerDay') : '')}}</small>
                                            <p class="tx-13 mg-b-5"><b>{{ trans('main.type') }}:</b> {{ trans('main.extra_quota') }} </p>
                                            <div class="d-flex">
                                                <h4 class="h5 w-50 font-weight-bold text-danger">{{ $one->monthly_after_vat }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                            </div>
                                            <a class="tx-gray-900 tx-uppercase font-weight-bold" href="#">{{ trans('main.remove') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>                           
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    <div class="col-xl-5 col-lg-5">
        <div class="card">
            <div class="card-header pd-y-20 border-bottom">
                <div class="card-title mb-0">{{ trans('main.order_sum') }}</div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-6"><input class="productcart form-control" type="text" placeholder="{{ trans('main.couponCode') }}"></div>
                    <div class="col-6"><a href="#" style="left: unset;bottom: unset;" class="btn btn-primary btn-md">{{ trans('main.apply') }}</a></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>{{ trans('main.grandTotal') }}</td>
                                <td class="text-right"><span>{{ $data->membership->monthly_after_vat }}</span> {{ trans('main.sar') }}</td>
                            </tr>
                            <tr>
                                <td><span>{{ trans('main.estimatedTax') }}</span></td>
                                <td class="text-right text-muted"><span>0</span> {{ trans('main.sar') }}</td>
                            </tr>
                            <tr>
                                <td><span>{{ trans('main.total') }}</span></td>
                                <td><h2 class="price text-right mb-0"><p class="mb-0">{{ $data->membership->monthly_after_vat }} {{ trans('main.sar') }}</p></h2></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <form class="text-center">
                    <button class="btn btn-secondary float-right mt-2 m-b-20 " type="submit" value="{{ trans('main.checkout') }}">{{ trans('main.checkout') }}</button>
                </form>
            </div>
        </div>
    </div>
</div> --}}
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
<script src="{{ asset('components/newPackage.js') }}" type="text/javascript"></script>
@endsection
