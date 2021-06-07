@extends('tenant.Layouts.master')
@section('title',trans('main.checkout'))
@section('styles')
<style type="text/css" media="screen">
    
</style>
@endsection


{{-- Content --}}
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">{{ trans('main.checkout') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ trans('main.checkout') }}</h4>
                <input type="hidden" name="addon" value="{{ trans('main.addon') }}">
                <input type="hidden" name="extra_quota" value="{{ trans('main.extra_quota') }}">
            </div>
        </div>
    </div>     
    <!-- end page title --> 

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="nav nav-pills flex-column nav-pills-tab text-center" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active shadow show py-2" id="custom-v-pills-billing-tab" data-toggle="pill" href="#custom-v-pills-billing" role="tab" aria-controls="custom-v-pills-billing"
                                    aria-selected="true">
                                    <i class="mdi mdi-account-circle d-block font-24"></i>
                                    {{ trans('main.tax_setting') }}
                                </a>
                                <a class="nav-link mt-2 shadow py-2" id="custom-v-pills-payment-tab" data-toggle="pill" href="#custom-v-pills-payment" role="tab" aria-controls="custom-v-pills-payment"
                                    aria-selected="false">
                                    <i class="mdi mdi-cash-multiple d-block font-24"></i>
                                    {{ trans('main.payment_setting') }}</a>
                            </div>  

                            <div class="border mt-4 rounded">
                                <h4 class="header-title p-2 mb-0">{{ trans('main.order_sum') }}</h4>

                                <div class="table-responsive">
                                    <table class="table table-centered table-nowrap mb-0">
                                        <tbody>

                                            @foreach($data->data as $oneItem)
                                            <tr>
                                                <td>
                                                    <a href="#" class="text-reset font-family-secondary">{{ $oneItem[2] }}</a><br>
                                                    <small class="mr-2 typeText"><b>{{ trans('main.type') }}:</b> {{ trans('main.'.$oneItem[1]) }} </small>
                                                </td>
                                                <td></td>
                                                <td class="text-right">
                                                    {{ $oneItem[6] }} {{ trans('main.sar') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            <tr class="text-right">
                                                <td colspan="2">
                                                    <h6 class="m-0">{{ trans('main.subTotal') }}:</h6>
                                                </td>
                                                <td class="text-right">
                                                    {{ $data->totals[0] }}  {{ trans('main.sar') }}
                                                </td>
                                            </tr>
                                            <tr class="text-right">
                                                <td colspan="2">
                                                    <h6 class="m-0">{{ trans('main.discount') }}:</h6>
                                                </td>
                                                <td class="text-right">
                                                    {{ $data->totals[1] }}
                                                </td>
                                            </tr>
                                            <tr class="text-right">
                                                <td colspan="2">
                                                    <h6 class="m-0">{{ trans('main.estimatedTax') }}:</h6>
                                                </td>
                                                <td class="text-right">
                                                    {{ $data->totals[2] }}
                                                </td>
                                            </tr>
                                            <tr class="text-right">
                                                <td colspan="2">
                                                    <h5 class="m-0">{{ trans('main.total') }}:</h5>
                                                </td>
                                                <td class="text-right font-weight-semibold">
                                                    {{ $data->totals[3] }}  {{ trans('main.sar') }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- end table-responsive -->
                            </div> <!-- end .border-->
                        </div> <!-- end col-->
                        <div class="col-lg-8">
                            <form action="{{ URL::to('/completeOrder') }}" method="post" accept-charset="utf-8">
                                @csrf
                                
                                <div class="tab-content p-3">
                                    <div class="tab-pane fade active show" id="custom-v-pills-billing" role="tabpanel" aria-labelledby="custom-v-pills-billing-tab">
                                        <h4 class="header-title">{{ trans('main.tax_setting') }}</h4>
                                        <p class="sub-header">{{ trans('main.tax_setting_p') }}</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.address') }} :</label>
                                                    <input class="form-control" name="address" value="" placeholder="{{ trans('main.address') }}">
                                                </div> 
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.address') }} 2 :</label>
                                                    <input class="form-control" name="address2" value="" placeholder="{{ trans('main.address') }} 2">
                                                </div> 
                                            </div>    
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.city') }} :</label>
                                                    <input class="form-control" name="city" value="" placeholder="{{ trans('main.city') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.region') }} :</label>
                                                    <input class="form-control" name="region" value="" placeholder="{{ trans('main.region') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.postal_code') }} :</label>
                                                    <input class="form-control" name="postal_code" value="" placeholder="{{ trans('main.postal_code') }}">
                                                </div> 
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('main.country') }} :</label>
                                                    <input class="form-control" value="" name="country" placeholder="{{ trans('main.country') }}">
                                                </div> 
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="form-group">
                                                    <label>{{ trans('main.tax_id') }} :</label>
                                                    <input class="form-control" name="tax_id" value="" placeholder="{{ trans('main.tax_id') }}">
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="custom-v-pills-payment" role="tabpanel" aria-labelledby="custom-v-pills-payment-tab">
                                        <div>
                                            <h4 class="header-title">{{ trans('main.payment_setting') }}</h4>

                                            <p class="sub-header">{{ trans('main.payment_setting_p') }}</p>

                                            <!-- Pay with Paypal box-->
                                            <div class="border p-3 mb-3 rounded">
                                                <div class="float-right">
                                                    <i class="fab fa-cc-paypal font-24 text-primary"></i>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="BillingOptRadio2" name="billingOptions" class="custom-control-input">
                                                    <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio2">{{ trans('main.bankTransfer') }}</label>
                                                </div>
                                                <p class="mb-0 pl-3 pt-1">You will be Asked to attach transfer image.</p>
                                            </div>
                                            <!-- end Pay with Paypal box-->

                                            <!-- Credit/Debit Card box-->
                                            <div class="border p-3 mb-3 rounded">
                                                <div class="float-right">
                                                    <i class="far fa-credit-card font-24 text-primary"></i>
                                                </div>
                                                <div class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" id="BillingOptRadio1" name="billingOptions" class="custom-control-input" checked>
                                                    <label class="custom-control-label font-16 font-weight-bold" for="BillingOptRadio1">Credit / Debit Card</label>
                                                </div>
                                                <p class="mb-0 pl-3 pt-1">Safe money transfer using your bank account. We support Mastercard, Visa, Discover and Stripe.</p>
                                                
                                                <div class="row mt-4">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="card-number">Card Number</label>
                                                            <input type="text" id="card-number" class="form-control" data-toggle="input-mask" data-mask-format="0000 0000 0000 0000" placeholder="4242 4242 4242 4242">
                                                        </div>
                                                    </div>
                                                </div> <!-- end row -->
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="card-name-on">Name on card</label>
                                                            <input type="text" id="card-name-on" class="form-control" placeholder="Master Shreyu">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="card-expiry-date">Expiry date</label>
                                                            <input type="text" id="card-expiry-date" class="form-control" data-toggle="input-mask" data-mask-format="00/00" placeholder="MM/YY">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label for="card-cvv">CVV code</label>
                                                            <input type="text" id="card-cvv" class="form-control" data-toggle="input-mask" data-mask-format="000" placeholder="012">
                                                        </div>
                                                    </div>
                                                </div> <!-- end row -->
                                            </div>
                                            <!-- end Credit/Debit Card box-->

                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <input type="hidden" name="totals" value="{{ json_encode($data->totals) }}">
                                    <input type="hidden" name="data" value="{{ json_encode($data->data) }}">
                                    <div class="col-sm-6">
                                        <a href="{{ URL::previous() }}" class="btn btn-secondary">
                                            <i class="mdi mdi-arrow-left"></i> Back to Shopping Cart </a>
                                    </div> <!-- end col -->
                                    <div class="col-sm-6">
                                        <div class="text-sm-right mt-2 mt-sm-0">
                                            <button type="submit" class="btn btn-success">
                                                <i class="mdi mdi-cash-multiple mr-1"></i> Complete Order </button>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row-->
                            </form>

                        </div> <!-- end col-->
                    </div> <!-- end row-->

                </div>
            </div>
        </div>
    </div>
    <!-- end row -->

    <!-- end row -->
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
<script src="{{ asset('components/newPackage.js') }}" type="text/javascript"></script>
@endsection
