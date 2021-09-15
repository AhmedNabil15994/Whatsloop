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
<div class="row">
    <div class="col-xl-8 col-lg-8">
        <input type="hidden" name="start_date" value="{{ $data->start_date }}">
        @if(!empty($data->memberships))
        <div aria-multiselectable="true" class="accordion mb-3" id="accordion4" role="tablist">
            <div class="card mb-0">
                <div class="card-header headingOnes border-bottom pb-3 pt-3" id="headingOne4" role="tab">
                    <a aria-controls="collapseOne4" aria-expanded="false" data-toggle="collapse" href="#collapseOne4" class="collapsed card-title"><i class="si si-diamond"></i> {{ trans('main.memberships') }}</a>
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
                                        <h4 class="h5 w-50 font-weight-bold text-danger mb-0 monthly" data-tabs="{{ $membership->monthly_after_vat }}">{{ number_format((float)$membership->monthly_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal d-block tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                        <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $membership->annual_after_vat }}">{{ number_format((float)$membership->annual_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal d-block tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                                    </div>
                                    <button class="btn btn-primary btn-block cartButton mb-0 mt-4 {{ $data->membership->id == $membership->id ? 'added' : 'add' }}" data-cols="{{ $membership->id }}" data-area="membership">
                                        @if($data->membership->id == $membership->id)
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
        @endif
        @if(!empty($data->addons))
        <div aria-multiselectable="true" class="accordion" id="accordion" role="tablist">
            <div class="card mb-0">
                <div class="card-header headingOnes border-bottom pb-3 pt-3" id="headingOne" role="tab">
                    <a aria-controls="collapseOne" aria-expanded="false" data-toggle="collapse" href="#collapseOne" class="collapsed card-title"><i class="si si-puzzle"></i> {{ trans('main.addons') }}</a>
                </div>
                <div aria-labelledby="headingOne" class="collapse show" data-parent="#accordion" id="collapseOne" role="tabpanel" style="">
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
                                        <h4 class="h5 w-50 font-weight-bold text-danger mb-0 monthly" data-tabs="{{ $addon->monthly_after_vat }}">{{ number_format((float)$addon->monthly_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                        <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $addon->annual_after_vat }}">{{ number_format((float)$addon->annual_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                                    </div>
                                    <button class="btn btn-primary cartButton add btn-block mb-0 mt-4" data-cols="{{ $addon->id }}" data-area="addon">
                                        <i class="fe fe-shopping-cart mr-1"></i>
                                        {{ trans('main.addToCart') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(!empty($data->extraQuotas))
        <div aria-multiselectable="true" class="accordion border-top pt-3" id="accordion2" role="tablist">
            <div class="card mb-0">
                <div class="card-header headingOnes border-bottom pb-3 pt-3" id="headingOne2" role="tab">
                    <a aria-controls="collapseOne" aria-expanded="false" data-toggle="collapse" href="#collapseOne2" class="collapsed card-title"><i class="si si-equalizer"></i> {{ trans('main.extraQuotas') }}</a>
                </div>
                <div aria-labelledby="headingOne2" class="collapse show" data-parent="#accordion2" id="collapseOne2" role="tabpanel" style="">
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
                                        <h4 class="h5 w-50 font-weight-bold text-danger mb-0 monthly" data-tabs="{{ $one->monthly_after_vat }}">{{ number_format((float)$one->monthly_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                        <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $one->annual_after_vat }}">{{ number_format((float)$one->annual_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                                    </div>
                                    <button class="btn btn-primary cartButton add btn-block mb-0 mt-4" data-cols="{{ $one->id }}" data-area="extra_quota">
                                        <i class="fe fe-shopping-cart mr-1"></i>
                                        {{ trans('main.addToCart') }}
                                    </button>
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

    <div class="col-xl-4 col-lg-4">
        <div class="card cart">
            <div class="card-header border-bottom py-3 d-sm-flex align-items-center">
                <div class="row w-100">
                    <div class="col-6">
                        <h4 class="card-title mg-t-15"><i class="fe fe-shopping-cart mr-1"></i> {{ trans('main.myCart') }}(<span class="cartCount">{{ !empty($data->memberships) ? '1' : '0' }}</span>)</h4>
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
                <input type="hidden" name="background" value="{{ $data->userCredits }}">
                <input type="hidden" name="type" value="{{ Request::get('type') }}">
            </form>
            @if(!empty($data->memberships))
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
                                <h4 class="h5 w-50 font-weight-bold text-danger monthly" data-tabs="{{ $data->membership->monthly_after_vat }}">{{ number_format((float)$data->membership->monthly_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.monthly') }}</span></h4>
                                <h4 class="h5 w-50 font-weight-bold text-danger yearly d-hidden" data-tabs="{{ $data->membership->annual_after_vat }}">{{ number_format((float)$data->membership->annual_price, 2, '.', '') }} {{ trans('main.sar') }} <span class="text-secondary font-weight-normal tx-13 ml-1">{{ trans('main.yearly') }}</span></h4>
                            </div>
                            <a class="tx-gray-900 tx-uppercase font-weight-bold rmv" href="#">{{ trans('main.remove') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
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
                        <table class="table table-bordered borderTop">
                            <tbody>
                                <tr>
                                    <td class="text-left">{{ trans('main.grandTotal') }}</td>
                                    <td class="text-right"><span class="grandTotal">0</span> {{ trans('main.sar') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left"><span>{{ trans('main.estimatedTax') }}</span></td>
                                    <td class="text-right text-muted"><span class="estimatedTax">0</span> {{ trans('main.sar') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-left"><span>{{ trans('main.total') }}</span></td>
                                    <td class="text-right"><span class="total">0</span> {{ trans('main.sar') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <button class="btn btn-success d-block w-100 checkout float-right mt-2 m-b-20" value="{{ trans('main.checkout') }}">{{ trans('main.checkout') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('components/userCart.js') }}" type="text/javascript"></script>
@endsection
