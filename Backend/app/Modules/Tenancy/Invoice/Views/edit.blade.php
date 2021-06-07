{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-11">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ URL::to('/dashboard') }}">{{ trans('main.dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data->designElems['mainData']['title'] }}</li>
                    </ol>
                </div>
                <h3 class="page-title">{{ $data->designElems['mainData']['title'] }}</h3>
            </div>
        </div>

        <div class="col-1 text-right">
            <div class="btn-group dropleft mb-3 mt-2">
                <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-cog"></i>
                </button>
                <div class="dropdown-menu">
                    @if(\Helper::checkRules('add-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/add') }}"><i class="fa fa-plus"></i> {{ trans('main.add') }}</a>
                    @endif
                    @if(\Helper::checkRules('sort-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/arrange') }}"><i class="fa fa-sort-numeric-up"></i> {{ trans('main.sort') }}</a>
                    @endif
                    @if(\Helper::checkRules('charts-'.$data->designElems['mainData']['nameOne']))
                    <a class="dropdown-item" href="{{ URL::to('/'.$data->designElems['mainData']['url'].'/charts') }}"><i class="fas fa-chart-bar"></i> {{ trans('main.charts') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>     
    <!-- end page title --> 
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->id) }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.client') }} :</label>
                            <div class="col-9">
                                <select name="client_id" class="form-control" data-toggle="select2">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @foreach($data->clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == $data->data->client_id ? 'selected' : '' }}>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.due_date') }} :</label>
                            <div class="col-9">
                                <input type="text" name="due_date" class="form-control datepicker" value="{{ $data->data->due_date }}">
                            </div>
                        </div>

                        <input type="hidden" name="total" class="form-control" value="{{ $data->data->total }}">

                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.status') }} :</label>
                            <div class="col-9">
                                <select name="status" class="form-control" data-toggle="select2">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    @for($i=0; $i<=5; $i++)
                                    <option value="{{ $i }}" {{ $i == $data->data->status ? 'selected' : '' }}>{{ trans('main.invoice_status_'.$i) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.paymentMethod') }} :</label>
                            <div class="col-9">
                                <select name="payment_method" class="form-control" data-toggle="select2">
                                    <option value="">{{ trans('main.choose') }}</option>
                                    <option value="1" {{ $data->data->payment_method == 1 ? 'selected' : '' }}>{{ trans('main.mada') }}</option>
                                    <option value="2" {{ $data->data->payment_method == 2 ? 'selected' : '' }}>{{ trans('main.visaMaster') }}</option>
                                    <option value="3" {{ $data->data->payment_method == 3 ? 'selected' : '' }}>{{ trans('main.bankTransfer') }}</option>

                                </select>
                            </div>
                        </div>

                        <hr>
                        <div class="table-responsive">
                            <h4 class="page-title mb-3">{{ trans('main.invoice_items') }}</h4>
                            <table class="table table-borderless table-centered mb-0">
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
                                    @php $mainPrices = 0; @endphp
                                    @foreach($data->data->items as $item)
                                    @php $mainPrices+=$item['data']['price'] @endphp
                                    <tr class="mainRow">
                                        <td>
                                            <p class="m-0 d-inline-block align-middle font-16">
                                                <a href="#" class="text-reset font-family-secondary">{{ $item['data']['title_'.LANGUAGE_PREF] }}</a><br>
                                                <small class="mr-2"><b>{{ trans('main.type') }}:</b> {{ trans('main.'.$item['type']) }} </small>
                                            </p>
                                        </td>
                                        <td class="tdPrice">{{ $item['data']['price'] }}</td>
                                        <td>1</td>
                                        <td>{{ $data->data->due_date }}</td>
                                        <td>{{ $item['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($data->data->due_date)))  : date('Y-m-d',strtotime('+1 year',strtotime($data->data->due_date))) }}</td>
                                        <td>{{ $item['data']['price_after_vat'] }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="action-icon" data-area="{{ json_encode($item) }}"> <i class="mdi mdi-delete"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

                        <!-- Add note input-->
                        <div class="mt-3">
                            <label for="example-textarea">{{ trans('main.notes') }}:</label>
                            <textarea class="form-control" name="notes" id="example-textarea" rows="3" placeholder="{{ trans('main.notes') }}..">{{ $data->data->notes }}</textarea>
                        </div>

                        <div class="form-group mt-3 mb-0 justify-content-end row">
                            <div class="col-9">
                                <button class="btn btn-success AddBTNz">{{ trans('main.edit') }}</button>
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="border p-3 mt-4 mt-lg-0 rounded">
                        <h4 class="header-title mb-3">{{ trans('main.order_sum') }}</h4>

                        <div class="table-responsive">
                            <table class="table mb-0">
                                <tbody>
                                    <tr>
                                        <td>{{ trans('main.grandTotal') }} :</td>
                                        <td><span class="mainPrices">{{ $mainPrices }}</span> {{ trans('main.sar') }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('main.discount') }} : </td>
                                        <td>0</td>
                                    </tr>
                                    <tr>
                                        <td>{{ trans('main.estimatesTax') }} : </td>
                                        <td>{{ $data->data->total - $mainPrices }} {{ trans('main.sar') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans('main.total') }} :</th>
                                        <th><span class="price">{{ $data->data->total }}</span>  {{ trans('main.sar') }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- end table-responsive -->
                    </div>
                </div>
            </div>
        </div> <!-- end col -->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
<script src="{{ asset('components/editInvoice.js') }}" type="text/javascript"></script>
@endsection