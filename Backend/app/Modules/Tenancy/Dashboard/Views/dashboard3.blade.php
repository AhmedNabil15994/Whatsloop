@extends('tenant.Layouts.master')
@section('title',trans('main.checkout'))
@section('styles')
<style type="text/css" media="screen">
    .card.card-pricing{
        min-height: 520px;
    }
    .card-body .btn-primary{
        display: block;
        margin: auto;
        position: absolute;
        bottom: 25px;
        left: calc(50% - 50px);
    }
    .check-title{
        margin-left: 25px;
        margin-right: 25px;
        margin-top: 15px;
    }
    .myCard{
        border: 1px solid #CCC;
        border-radius: 5px;
    }
    .checked{
        border: 2px solid #1abc9c;
    }
    .myCard .card-body{
        position: relative;
    }
    .myCard .selected{
        position: absolute;
        left: 10px;
        top: 10px;
    }
    .myCard .selected.selected2{
        top: 30px;
    }
    html[dir="ltr"] .myCard .selected{
        left: auto;
        right: 10px;
    }
    .form-group.row{
        width: 100%;
    }
    .card-title{
        width: 100%;
    }
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
                        <li class="breadcrumb-item active">{{ trans('main.packages') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ trans('main.packages') }}</h4>
                <input type="hidden" name="addon" value="{{ trans('main.addon') }}">
                <input type="hidden" name="extra_quota" value="{{ trans('main.extra_quota') }}">
            </div>
        </div>
    </div>     
    <!-- end page title --> 

    <div class="row">
        <div class="col-12">
            @if(!Request::has('membership_id'))
            <p>You Must Choose A Package At Least</p>
            @else
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="table-responsive mb-3">
                                <table class="table items table-borderless table-centered mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ trans('main.item') }}</th>
                                            <th>{{ trans('main.price') }}</th>
                                            <th>{{ trans('main.quantity') }}</th>
                                            <th>{{ trans('main.start_date') }}</th>
                                            <th>{{ trans('main.end_date') }}</th>
                                            <th>{{ trans('main.price_after_vat') }}</th>
                                            <th style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-tabs="membership" data-cols="{{ $data->membership->id }}">
                                            <td class="tdDets">
                                                <p class="m-0 d-inline-block align-middle font-16">
                                                    <a href="#" class="text-reset font-family-secondary">{{ $data->membership->title }}</a><br>
                                                    <small class="mr-2 typeText"><b>{{ trans('main.type') }}:</b> {{ trans('main.membership') }} </small>
                                                </p>
                                            </td>
                                            <td class="tdPrice">
                                                <div class="row">
                                                    <div class="form-group row">
                                                        <div class="col-2" style="margin-top: -8px;">
                                                            <div class="checkbox checkbox-success">
                                                                <input id="monthlyPack" data-area="{{ $data->membership->monthly_after_vat }}" class="monthlyPack" type="checkbox">
                                                                <label for="monthlyPack"></label>
                                                            </div>
                                                        </div>
                                                        <label class="col-8 col-form-label">{{ $data->membership->monthly_price . ' ' . trans('main.sar') . ' '.trans('main.monthly') }}</label>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-2" style="margin-top: -8px;">
                                                            <div class="checkbox checkbox-success">
                                                                <input id="yearlyPack" data-area="{{ $data->membership->annual_after_vat }}" class="yearlyPack" type="checkbox">
                                                                <label for="yearlyPack"></label>
                                                            </div>
                                                        </div>
                                                        <label class="col-8 col-form-label">{{ $data->membership->annual_price . ' ' . trans('main.sar') . ' '.trans('main.yearly') }}</label>
                                                    </div>
                                            </td>
                                            <td>1</td>
                                            <td class="start_date">{{ date('Y-m-d') }}</td>
                                            <td class="end_date"></td>
                                            <td class="price_with_vat"></td>
                                            <td>
                                                <a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive-->
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
                                            <div class="row">
                                                @foreach($data->addons as $addon)
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
</div> <!-- container -->
@endsection

{{-- Scripts Section --}}
@section('topScripts')
<script src="{{ asset('js/pages/dashboard-3.init.js') }}"></script>
<script src="{{ asset('components/newPackage.js') }}" type="text/javascript"></script>
@endsection
