{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',$data->designElems['mainData']['title'])
@section('styles')
<style type="text/css" media="screen">
	.helpHead .nextPrev a.btnNext {
	    width: auto;
	    height: auto;
	    display: inline-block;
	    padding: 10px;
	    text-align: center;
	}
</style>
@endsection
@section('content')
<div class="helpPage">
	<div class="helpHead">
		<div class="btns">
			<div class="nextPrev clearfix mt-3">
				@if(IS_ADMIN && $data->data->status != 1)
	            <a href="{{ URL::current().'/checkout' }}" class="btnNext"> {{ trans('main.checkout') }}</a>
	            @endif
	            <div class="clearfix"></div>
	        </div>
		</div>
		<div class="ticketHead">
			<h2 class="title"> {{ trans('main.invoice') }}</h2>
			<div class="numbTicket">
				<span class="numb">#{{ $data->data->id + 10000 }}</span>
				<a href="#">{{ trans('main.invoice_status_'.$data->data->status) }}</a>
			</div>
			@php 
	            $paymentObj = App\Models\PaymentInfo::NotDeleted()->where('user_id',$data->data->client_id)->first();
	            if($paymentObj)
	                $paymentObj = App\Models\PaymentInfo::getData($paymentObj);
	        @endphp
		</div>
	</div>

	<div class="detailsHelp">
		<div class="overflowTable">
			<table class="tableDetails">
				<thead>
					<tr>
						<th>{{ trans('main.due_date') }}</th>
						<th>{{ trans('main.paymentMethod') }}</th>
						<th>{{ trans('main.appName') }}</th>
						<th>{{ trans('main.createdFor') }}</th>
		   				<th>{{ trans('main.eInvoice') }}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>{{ date('M d, Y',strtotime($data->data->due_date)) }}</td>
						<td>{{ $data->data->status == 1 ? $data->data->payment_gateaway : '-------' }}</td>
						<td>
							{{ $data->companyAddress->servers }}
							<br>
							{{ $data->companyAddress->address }}
							<br>
							{{ $data->companyAddress->region . ', ' . $data->companyAddress->postal_code  }}
							<br>
							{{ $data->companyAddress->city }}
							<br>
							{{ $data->companyAddress->country  }}
							<br>
							{{ trans('main.tax_id') }}: {{ $data->companyAddress->tax_id }}
						</td>
						<td>
							{{ $data->data->company }}
							<br>
							{{ $data->data->client }}
							<br>
							{{ (isset($paymentObj) ? $paymentObj->address : '') }}
							<br>
							{{ (isset($paymentObj) ? $paymentObj->city : '') . ', ' . (isset($paymentObj) ? $paymentObj->region : '') . ', ' . (isset($paymentObj) ? $paymentObj->postal_code : '')  }}
							<br>
							{{ (isset($paymentObj) ? $paymentObj->country : '')  }}
							<br>
							@if((isset($paymentObj) ? $paymentObj->tax_id : ''))
							{{ trans('main.tax_id') }}: {{ $paymentObj->tax_id }}
	                        @endif
						</td>
						<td>
							{!! \QrCode::size(100)->generate(URL::current()) !!}
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="tables bill">
			<div class="overflowTable">
	            <table class="tableBills">
		            <thead>
		                <tr>
		                    <th>#</th>
		                    <th>{{ trans('main.item') }}</th>
		                    <th>{{ trans('main.quantity') }}</th>
		                    <th>{{ trans('main.start_date') }}</th>
		                    <th>{{ trans('main.end_date') }}</th>
		                    <th class="text-center">{{ trans('main.total') }}</th>
		                </tr>
		            </thead>
		            <tbody>
						@php $mainPrices = 0; @endphp
                        @foreach($data->data->items as $key => $item)
                        @php 
                        	$mainPrices+=$item['data']['price'] * $item['data']['quantity']; 
                        	$oldDiscount = $mainPrices - $data->data->total + $data->data->discount;
                        @endphp
                        <tr class="mainRow">
                            <td>{{ $key+1 }}</td>
                            <td>
                                <p>
                                    <a href="#">{{ $item['data']['title_'.LANGUAGE_PREF] }}</a><br>
                                    <small><b>{{ trans('main.extra_type') }}:</b> {{ trans('main.'.$item['type']) }} </small>
                                </p>
                            </td>
                            <td>{{ $item['data']['quantity'] }}</td>
                            <td>{{ $data->data->due_date }}</td>
                            <td>{{ $item['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($data->data->due_date)))  : date('Y-m-d',strtotime('+1 year',strtotime($data->data->due_date))) }}</td>
                            <td class="text-center">{{ $item['data']['quantity'] * $item['data']['price_after_vat'] }} {{ trans('main.sar') }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-left">
                            	<p class="mb-2">
                                    <span class="tx-bold">{{ trans('main.discount') }} :</span>
                                    <span class="float-right">{{ $oldDiscount }} {{ trans('main.sar') }}</span>
                                    <div class="clearfix"></div>
                                </p>
                                <p class="mb-2">
                                    @php 
                                        $tax = Helper::calcTax($data->data->total - $oldDiscount);
                                    @endphp

                                    <span class="tx-bold">{{ trans('main.grandTotal') }} :</span>
                                    <span class="float-right">{{ $data->data->total - $oldDiscount - $tax }} {{ trans('main.sar') }}</span>
                                    <div class="clearfix"></div>
                                </p>
                                <p class="mb-2">
                                    <span class="tx-bold">{{ trans('main.estimatedTax') }} :</span>
                                    <span class="float-right">{{ $tax }} {{ trans('main.sar') }}</span>
                                    <div class="clearfix"></div>
                                </p>
                                <p class="mb-2">
                                    <span class="tx-bold">{{ trans('main.total') }} :</span>
                                    <span class="float-right">{{ $data->data->total - $oldDiscount }}  {{ trans('main.sar') }}</span>
                                    <div class="clearfix"></div>
                                </p>
                            </td>
                        </tr>
		            </tbody>
		        </table>
			</div>
			@if($data->data->transaction_id)
			<div class="overflowTable">
	            <table class="tableBills">
		            <thead>
	                    <tr>
	                        <th>#</th>
	                        <th>{{ trans('main.transaction_date') }}</th>
	                        <th>{{ trans('main.paymentGateaway') }}</th>
	                        <th>{{ trans('main.transaction_id') }}</th>
	                        <th>{{ trans('main.transaction_price') }}</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    @php $mainPrices = 0; @endphp
	                    @foreach($data->data->items as $key => $item)
	                    @php $mainPrices+=$item['data']['price'] * $item['data']['quantity'] @endphp
	                    @endforeach
	                    <tr class="mainRow">
	                        <td>{{ $key+1 }}</td>
	                        <td>
	                            <p class="m-0 d-inline-block align-middle font-16">
	                                <a href="#" class="text-reset font-family-secondary">{{ $data->data->due_date }}</a><br>
	                            </p>
	                        </td>
	                        <td>{{ $data->data->payment_gateaway }}</td>
	                        <td>{{ $data->data->transaction_id }}</td>
	                        <td>{{ $data->data->total }} {{ trans('main.sar') }}</td>
	                    </tr>
	                </tbody>
		        </table>
			</div>
			@endif
			<div class="nextPrev last clearfix">
				<a href="javascript:window.print()" class="btnNext btn">{{ trans('main.print') }}</a>
				<a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" class="btnNext btn">{{ trans('main.back') }}</a>
			</div>		  	 			
		</div>
	</div>
</div>
@endsection
