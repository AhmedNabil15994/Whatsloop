@extends('tenant.Layouts.master')
@section('title',trans('main.checkout'))
@section('styles')
<!---Internal Fileupload css-->
<link href="{{ asset('plugins/fileuploads/css/fileupload.css') }}" rel="stylesheet" type="text/css"/>

<!---Internal Fancy uploader css-->
<link href="{{ asset('plugins/fancyuploder/fancy_fileupload.css') }}" rel="stylesheet" />
<style type="text/css" media="screen">
    .border.rounded .float-right img{
        width: 80px;
    }
    .wizard>.steps>ul li{
        width: 33%;
    }
    html[dir="ltr"] .wizard>.steps>ul>li:not(.last) a:after{
        right: -150px;
        transform: rotateZ(180deg);
    }
    html[dir="rtl"] .wizard>.steps>ul>li:not(.last) a:after{
        left: -150px
    }
    span.number{
        padding: 15px;
    }
</style>
@endsection


{{-- Content --}}
@section('content')
<!-- Start Content-->
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="main-content-label mg-b-5">
                    {{ trans('main.checkout') }}
                </div>
                <p class="mg-b-20 card-sub-title tx-12 text-muted"></p>
                <div id="wizard1" role="application" class="wizard clearfix">
                    <div class="steps clearfix">
                        <ul role="tablist">
                            <li role="tab" class="first current">
                                <a id="wizard1-t-0" href="#wizard1-h-0" aria-controls="wizard1-p-0">
                                    <span class="current-info audible">current step: </span>
                                    <span class="number">1</span> 
                                    <span class="title">{{ trans('main.myCart') }}</span>
                                </a>
                            </li>
                            <li role="tab" class="disabled">
                                <a id="wizard1-t-3" href="#wizard1-h-3" aria-controls="wizard1-p-3">
                                    <span class="number">2</span> 
                                    <span class="title">{{ trans('main.financial_setting') }}</span>
                                </a>
                            </li>
                            <li role="tab" class="disabled last">
                                <a id="wizard1-t-2" href="#wizard1-h-4" aria-controls="wizard1-p-4">
                                    <span class="number">3</span> 
                                    <span class="title">{{ trans('main.payment_setting') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="content clearfix qrData">
                        <h3 id="wizard1-h-0" tabindex="-1" class="title mb-5 current">{{ trans('main.myCart') }}</h3>
                        <section id="wizard1-p-0" role="tabpanel" aria-labelledby="wizard1-h-0" class="body current" aria-hidden="false">
                            <div class="table-responsive mg-t-20">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>{{ trans('main.product') }}</th>
                                            <th class="w-150">{{ trans('main.quantity') }}</th>
                                            <th>{{ trans('main.price') }}</th>
                                            <th>{{ trans('main.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->data as $oneItem)
                                            <tr class="tableRow" data-cols="{{ $oneItem[0] }}" data-type="{{ $oneItem[1] }}" data-dur={{ $oneItem[3] }}>
                                                <td>
                                                    <div class="media">
                                                        <div class="card-aside-img">
                                                            <img src="{{ asset('img/ecommerce/01.jpg') }}" alt="img" class="ht-70-f wd-70-f mg-{{ DIRECTION == 'ltr' ? 'r' : 'l' }}-20">
                                                        </div>
                                                        <div class="media-body">
                                                            <div class="card-item-desc mt-1">
                                                                <h6 class="font-weight-semibold mt-0 text-uppercase">{{ $oneItem[2] }}</h6>
                                                                <dl class="card-item-desc-1">
                                                                    <dt>{{ trans('main.extra_type') }}: </dt>
                                                                    <dd>{{ trans('main.'.$oneItem[1]) }}</dd>
                                                                </dl>
                                                                <dl class="card-item-desc-1">
                                                                    <dt>{{ trans('main.subscription') }}: </dt>
                                                                    <dd>
                                                                        <span class="start_date">{{ $oneItem[4] }}</span> - 
                                                                        <span class="end_date">{{ $oneItem[5] }}</span>
                                                                    </dd>
                                                                </dl>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group">
                                                        <select name="quantity" class="form-control custom-select select2" data-area="{{ $oneItem[6] }}" data-tabs="{{ $oneItem[7] }}">
                                                            <option value="1" {{ $oneItem[7] == 1 ? 'selected' : '' }}>1</option>
                                                            @if($oneItem[1] == 'extra_quota')
                                                            <option value="2" {{ $oneItem[7] == 2 ? 'selected' : '' }}>2</option>
                                                            <option value="3" {{ $oneItem[7] == 3 ? 'selected' : '' }}>3</option>
                                                            <option value="4" {{ $oneItem[7] == 4 ? 'selected' : '' }}>4</option>
                                                            <option value="5" {{ $oneItem[7] == 5 ? 'selected' : '' }}>5</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="prices"><span class="price">{{ $oneItem[1] == 'extra_quota' ? $oneItem[6] * $oneItem[7] : $oneItem[6] }}</span> {{ trans('main.sar') }}</td>
                                                <td>
                                                    @if($oneItem[1] != 'membership')
                                                    <div class="d-flex">
                                                        <a class="btn btn-danger btn-icon btn-sm text-white mr-2 rmv"><i class="fe fe-trash"></i></a>
                                                    </div>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        <tr>
                                            <td colspan="2">{{ trans('main.subTotal') }}</td>
                                            <td colspan="2" class="text-right"><span class="grandTotal">{{ $data->totals[0] }}</span> {{ trans('main.sar') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">{{ trans('main.discount') }}</td>
                                            <td colspan="2" class="text-right">{{ $data->totals[1] }} {{ trans('main.sar') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><span>{{ trans('main.estimatedTax') }}</span></td>
                                            <td colspan="2" class="text-right text-muted"><span><span class="estimatedTax">{{ $data->totals[2] }}</span> {{ trans('main.sar') }}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><span>{{ trans('main.total') }}</span></td>
                                            <td colspan="2"><h2 class="text-right mb-0"><span class="total">{{ $data->totals[3] }}</span> {{ trans('main.sar') }}</h2></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        
                        <h3 id="wizard1-h-3" tabindex="-1" class="title mb-5">{{ trans('main.tax_setting') }}</h3>
                        <section id="wizard1-p-3" role="tabpanel" aria-labelledby="wizard1-h-3" class="body text-center" aria-hidden="true" style="display: none;">
                            <form class="completeOrder" action="{{ URL::to('/completeOrder') }}" method="post" accept-charset="utf-8">
                                @csrf
                                <div class="text-left">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.address') }} :</label>
                                                <input class="form-control" name="address" value="{{ isset($data->payment) ? $data->payment->address : '' }}" placeholder="{{ trans('main.address') }}">
                                            </div> 
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.address') }} 2 :</label>
                                                <input class="form-control" name="address2" value="{{ isset($data->payment) ? $data->payment->address2 : '' }}" placeholder="{{ trans('main.address') }} 2">
                                            </div> 
                                        </div>    
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.city') }} :</label>
                                                <input class="form-control" name="city" value="{{ isset($data->payment) ? $data->payment->city : '' }}" placeholder="{{ trans('main.city') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.region') }} :</label>
                                                <input class="form-control" name="region" value="{{ isset($data->payment) ? $data->payment->region : '' }}" placeholder="{{ trans('main.region') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.postal_code') }} :</label>
                                                <input class="form-control" name="postal_code" value="{{ isset($data->payment) ? $data->payment->postal_code : '' }}" placeholder="{{ trans('main.postal_code') }}">
                                            </div> 
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('main.country') }} :</label>
                                                <input class="form-control" value="{{ isset($data->payment) ? $data->payment->country : '' }}" name="country" placeholder="{{ trans('main.country') }}">
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="form-group">
                                                <label>{{ trans('main.tax_id') }} :</label>
                                                <input class="form-control" name="tax_id" value="{{ isset($data->payment) ? $data->payment->tax_id : '' }}" placeholder="{{ trans('main.tax_id') }}">
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="payType" value="">
                                <input type="hidden" name="totals" value="{{ json_encode($data->totals) }}">
                                <input type="hidden" name="data" value="{{ json_encode($data->data) }}">
                            </form>
                        </section>

                        <h3 id="wizard1-h-4" tabindex="-1" class="title mb-5">{{ trans('main.payment_setting') }}</h3>
                        <section id="wizard1-p-4" role="tabpanel" aria-labelledby="wizard1-h-4" class="body" aria-hidden="true" style="display: none;">
                            <div class="row">
                                <div class="col-6">
                                    <!-- Pay with Paypal box-->
                                    <div class="border p-3 mb-3 rounded payments" data-area="1">
                                        <div class="float-right">
                                            <i class="fab fa-cc-paypal font-24 text-primary"></i>
                                        </div>
                                        <div class="custom-control">
                                            <label class="font-16 font-weight-bold">{{ trans('main.bankTransfer') }}</label>
                                        </div>
                                    </div>
                                    <!-- end Pay with Paypal box-->
                                </div>
                                <div class="col-6">
                                    <!-- Pay with Paypal box-->
                                    <div class="border p-3 mb-3 rounded payments" data-area="2">
                                        <div class="float-right">
                                            <i class="fab fa-cc-paypal font-24 text-primary"></i>
                                        </div>
                                        <div class="custom-control">
                                            <label class="font-16 font-weight-bold">{{ trans('main.ePayment') }}</label>
                                        </div>
                                    </div>
                                    <!-- end Pay with Paypal box-->
                                </div>

                                <div class="row w-100">
                                    <div class="col-12 p-4 transfer d-hidden">
                                        <input type="file" class="dropify" data-height="200" />
                                    </div>

                                    <div class="col-12 p-4 ePayment d-hidden">
                                        <div class="border p-3 mb-3 rounded">
                                            <div class="float-right">
                                                <img src="{{ asset('/images/paytabs.svg') }}" alt="">
                                            </div>
                                            <div class="custom-control custom-radio custom-control-inline">
                                                <input type="radio" id="BillingOptRadio2" name="billingOptions" data-area="2" class="custom-control-input" checked>
                                                <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio2"> {{ trans('main.payViaPayTabs') }}</label>
                                            </div>
                                            <p class="mb-0 pl-3 pt-1"></p>
                                        </div>  
                                        <div class="border p-3 mb-3 rounded">
                                            <div class="float-right">
                                                <img src="{{ asset('/images/noon.svg') }}" alt="">
                                            </div>
                                            <div class="custom-control custom-radio custom-control">
                                                <input type="radio" id="BillingOptRadio3" name="billingOptions" data-area="3" class="custom-control-input" checked>
                                                <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio3"> {{ trans('main.payViaNoon') }} </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 p-4 noon d-hidden">
                                        <div class="border p-3 mb-3 rounded">
                                            <label class="font-16 font-weight-bold"> {{ trans('main.payViaNoon') }}</label>
                                            <div class="float-right">
                                                <img src="{{ asset('/images/noon.svg') }}" alt="">
                                            </div>
                                            <br>
                                            <br>
                                            <div class="custom-control custom-radio custom-control">
                                                <div class="custom-control custom-radio custom-control">
                                                    <input type="radio" id="BillingOptRadio4" name="billingOptions" data-area="4" class="custom-control-input" checked>
                                                    <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio4"> {{ trans('main.singlePayment') }}</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control">
                                                    <input type="radio" id="BillingOptRadio5" name="billingOptions" data-area="5" class="custom-control-input">
                                                    <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio5"> {{ trans('main.subscription1') }}</label>
                                                </div>
                                                <div class="custom-control custom-radio custom-control">
                                                    <input type="radio" id="BillingOptRadio6" name="billingOptions" data-area="6" class="custom-control-input">
                                                    <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio6"> {{ trans('main.subscription2') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Credit/Debit Card box-->
                                <div class="border p-3 mb-3 rounded d-hidden">
                                    <div class="float-right">
                                        <img src="{{ asset('/images/paytabs.svg') }}" alt="">
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="BillingOptRadio1" name="billingOptions" data-area="2" class="custom-control-input" checked>
                                        <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio1"> Pay Via Paytabs</label>
                                    </div>
                                    <p class="mb-0 pl-3 pt-1"></p>
                                </div>  
                                <div class="border p-3 mb-3 rounded d-hidden">
                                    <div class="float-right">
                                        <img src="{{ asset('/images/noon.svg') }}" alt="">
                                    </div>
                                    <div class="custom-control custom-radio custom-control">
                                        <input type="radio" id="BillingOptRadio3" name="billingOptions" data-area="3" class="custom-control-input" checked>
                                        <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio3"> Pay Via Noon</label>
                                        <br>
                                        <br>
                                        <div class="custom-control custom-radio custom-control">
                                            <input type="radio" id="BillingOptRadio5" name="billingOpt" data-area="4" class="custom-control-input">
                                            <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio5"> Set As Subscription</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control">
                                            <input type="radio" id="BillingOptRadio6" name="billingOpt" data-area="3" class="custom-control-input" checked>
                                            <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio6"> Single Payment</label>
                                        </div>
                                    </div>
                                    <p class="mb-0 pl-3 pt-1"></p>
                                </div>
                                <!-- end Credit/Debit Card box-->
                            </div>
                        </section>
                    </div>
                    <div class="actions clearfix qrData">
                        <ul role="menu" aria-label="Pagination">
                            <li class="disabled prev" aria-disabled="true">
                                <a href="#previous" role="menuitem">{{ trans('main.prev') }}</a>
                            </li>
                            <li aria-hidden="false" class="next" aria-disabled="false">
                                <a href="#next" role="menuitem">{{ trans('main.next') }}</a>
                            </li>
                            <li aria-hidden="true" class="finish" style="display: none;">
                                <a href="#finish" role="menuitem">{{ trans('main.finish') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{ asset('plugins/fileuploads/js/file-upload.js') }}"></script>
<script src="{{ asset('components/checkout.js') }}" type="text/javascript"></script>
<script src="{{ asset('js/file-upload.js') }}" type="text/javascript"></script>
@endsection