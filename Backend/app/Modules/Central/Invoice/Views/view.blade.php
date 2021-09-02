{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    
                </div> <!-- end card body-->

                <div class="card-box" id="main" style="padding: 20px;">
                    <!-- Logo & title -->
                    <div class="clearfix">
                        <div class="float-left">
                            <div class="auth-logo">
                                <div class="logo logo-dark">
                                    <span class="logo-lg">
                                        <img src="{{ asset('tenancy/assets/images/full_logo.svg') }}" alt="" height="32">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="float-right">
                            <h4 class="m-0 d-print-none">{{ trans('main.invoice') }} #{{ $data->data->id }}</h4>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mt-3">
                                <p><b>{{ trans('main.hello') }}, {{ $data->data->client }}</b></p>
                                <p class="text-muted">Thanks a lot because you keep purchasing our products. Our company
                                    promises to provide high quality products for you as well as outstanding
                                    customer service for every transaction. </p>
                            </div>

                        </div><!-- end col -->
                        <div class="col-md-4 offset-md-2">
                            <div class="mt-3 float-right">
                                <p class="m-b-10"><strong>{{ trans('main.due_date') }} : </strong> <span class="float-right"> &nbsp;&nbsp;&nbsp;&nbsp; {{ date('M d, Y',strtotime($data->data->due_date)) }}</span></p>
                                <p class="m-b-10"><strong>{{ trans('main.status') }} : </strong> 
                                    <span class="float-right">
                                        @php
                                            $className = '';
                                            if($data->data->status == 0){
                                                $className = 'secondary';
                                            }else if($data->data->status == 1){
                                                $className = 'success';
                                            }else if($data->data->status == 2){
                                                $className = 'danger';
                                            }else if($data->data->status == 3){
                                                $className = 'primary';
                                            }else if($data->data->status == 4){
                                                $className = 'info';
                                            }else if($data->data->status == 5){
                                                $className = 'warning';
                                            }

                                        @endphp
                                        <span class="badge badge-{{ $className }}" style="padding: 6px;">{{ trans('main.invoice_status_'.$data->data->status) }}</span>
                                    </span>
                                </p>
                            </div>
                        </div><!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table mt-4 table-centered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ trans('main.item') }}</th>
                                            <th style="width: 10%">{{ trans('main.quantity') }}</th>
                                            <th style="width: 10%">{{ trans('main.start_date') }}</th>
                                            <th style="width: 10%">{{ trans('main.end_date') }}</th>
                                            <th style="width: 10%" class="text-right">{{ trans('main.total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $mainPrices = 0; @endphp
                                        @foreach($data->data->items as $key => $item)
                                        @php $mainPrices+=$item['data']['price'] * $item['data']['quantity'] @endphp
                                        <tr class="mainRow">
                                            <td>{{ $key+1 }}</td>
                                            <td>
                                                <p class="m-0 d-inline-block align-middle font-16">
                                                    <a href="#" class="text-reset font-family-secondary">{{ $item['data']['title_'.LANGUAGE_PREF] }}</a><br>
                                                    <small class="mr-2"><b>{{ trans('main.type') }}:</b> {{ trans('main.'.$item['type']) }} </small>
                                                </p>
                                            </td>
                                            <td>{{ $item['data']['quantity'] }}</td>
                                            <td>{{ $data->data->due_date }}</td>
                                            <td>{{ $item['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($data->data->due_date)))  : date('Y-m-d',strtotime('+1 year',strtotime($data->data->due_date))) }}</td>
                                            <td class="text-right">{{ $item['data']['quantity'] * $item['data']['price_after_vat'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> <!-- end table-responsive -->
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix pt-5">
                                <h6 class="text-muted">{{ trans('main.notes') }}:</h6>

                                <small class="text-muted">
                                    {{ $data->data->notes }}
                                </small>
                            </div>
                        </div> <!-- end col -->
                        <div class="col-sm-6">
                            <div class="float-right">
                                <p><b>{{ trans('main.grandTotal') }}:</b> <span class="float-right">{{ $mainPrices }} {{ trans('main.sar') }}</span></p>
                                <p><b>{{ trans('main.discount') }} (0%):</b> <span class="float-right"> &nbsp;&nbsp;&nbsp; 0</span></p>
                                <h3>{{ $data->data->total }}  {{ trans('main.sar') }}</h3>
                            </div>
                            <div class="clearfix"></div>
                        </div> <!-- end col -->
                    </div>
                    <!-- end row -->

                    <div class="mt-4 mb-1">
                        <div class="text-right d-print-none">
                            <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> {{ trans('main.print') }}</a>
                            <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" class="btn btn-danger waves-effect waves-light">{{ trans('main.back') }}</a>
                        </div>
                    </div>
                </div> <!-- end card-box -->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection