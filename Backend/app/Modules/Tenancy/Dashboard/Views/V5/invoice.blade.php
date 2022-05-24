<div id="helpPage">
	<link rel="stylesheet" href="{{ asset('V5/css/font.css') }}" />
	<style type="text/css">
		td a {
		    color: inherit;
		}

		a {
		    text-decoration: none!important;
		    outline: none;
		    -webkit-transition: all 0.3s;
		    -moz-transition: all 0.3s;
		    -o-transition: all 0.3s;
		    transition: all 0.3s;
		}
		.float-right {
		    float: left !important;
		}
		.text-left {
		    text-align: right !important;
		}
		.bill .tableBills 
		{
			border-radius: 10px;
			overflow: hidden;
			width:100%;
			margin-top:30px;
			margin-bottom:25px;
		}

		.bill .tableBills thead tr
		{
			background-color:#9AE2DE
		}

		.bill .tableBills thead tr th
		{
			padding:15px 15px;
			font-family: "Tajawal-Bold";
			font-size:14px;
			text-align: center;
		}

		.bill .tableBills tbody tr
		{
			background-color:#F4F5FD
		}

		.bill .tableBills tbody tr td
		{
			padding:30px 15px;
			text-align: center;
			font-size:14px;
			font-family: "Tajawal-Medium";
		}

		.helpPage{
			font-family:Tajawal-Regular;
			text-align: right;
			direction: rtl;
		}
		.helpPage .helpHead
		{
			display:block;
			margin-top:-20px
			
		}

		.helpPage .helpHead .titleHelp
		{
			font-size:14px;
			font-family: "Tajawal-Bold";
			margin-bottom:25px;
			margin-top:15px;
		}

		.helpPage .helpHead .btnHelp
		{
			width:130px;
			height:40px;
			line-height:40px;
			font-family: "Tajawal-Medium";
			border-radius: 5px;
			margin-bottom:25px;
			background-color:#fff;
			color:#000;
			display:block;
			text-align: center
		}

		.helpPage .helpHead .ticketHead
		{
			width:300px;
			overflow: hidden;
			position:relative;
			height:100px;
			margin-right:20px;
			margin-bottom:20px;
		}

		.helpPage .helpHead .ticketHead:after
		{
			content:"";
			position:absolute;
			left:-9px;
			top:8px;
			height:100%;
			background:url("../images/Subtraction 3.png") no-repeat;
			width:18px;
		}

		.helpPage .helpHead .ticketHead .title
		{
			float:right;
			width:50%;
			background-color:#F6CD02;
			height:100px;
			line-height:100px;
			color:#000;
			font-size:22px;
			font-family: "Tajawal-Bold";
			text-align: center;
		}

		.helpPage .helpHead .ticketHead .numbTicket
		{
			width:50%;
			float:left;
			height:100px;
			background-color:#fff;
			padding-top:10px;
		}

		.helpPage .helpHead .ticketHead .numbTicket .numb
		{
			font-family: "Tajawal-ExtraBold";
			font-size:22px;
			margin-bottom:5px;
			display:block;
			text-align: center
		}

		.helpPage .helpHead .ticketHead .numbTicket a
		{
			width:90px;
			height:35px;
			border-radius: 5px;
			background-color:#00BFB5;
			color:#fff;
			font-size:16px;
			font-family: "Tajawal-Medium";
			display:block;
			margin:0 auto;
			text-align: center;
			line-height:35px;
		}

		.helpPage .detailsHelp
		{
			background-color:#fff;
			border-radius: 10px;
			overflow: hidden;
		}

		.helpPage .detailsHelp .tableDetails thead tr th
		{
			padding:25px 20px;
			font-family: "Tajawal-Bold";
			font-size:14px;
		}

		.helpPage .detailsHelp .tableDetails tr th:not(:last-of-type),
		.helpPage .detailsHelp .tableDetails tr td:not(:last-of-type),
		.helpPage .detailsHelp .tables.bill .tableBills tbody tr td:not(:last-of-type)
		{
			border-left:1px solid #F3F3F3
		}

		.helpPage .detailsHelp .tableDetails tr,
		.helpPage .detailsHelp .tables.bill .tableBills tbody tr
		{
			border-bottom:1px solid #f3f3f3
		}

		.helpPage .detailsHelp .tables.bill .overflowTable
		{
			border-radius: 10px;
			border:1px solid #F3F3F3;
			margin-bottom:30px;
		}

		.helpPage .detailsHelp .tables.bill .overflowTable .tableBills
		{
			margin:0;
		}


		.helpPage .detailsHelp  .tables.bill .tableBills thead tr th,
		.helpPage .detailsHelp .tables.bill .tableBills tbody tr td
		{
			padding-left:15px;
			padding-right:15px;
		}

		.helpPage .detailsHelp .tableDetails tbody tr td
		{
			padding:25px 20px;
			font-family: "Tajawal-Medium";
			font-size:14px;
		}

		.helpPage .detailsHelp .tableDetails tbody tr td svg
		{
			display:block;
			margin:0 auto;
			max-width:100px;
		}

		.helpPage .detailsHelp .tables 
		{
			padding:40px 15px 35px;
		}

		.helpPage .detailsHelp .tables.bill .tableBills tbody tr
		{
			background:none;
		}

		.helpPage .detailsHelp .tables.bill .nextPrev .btnNext:first-of-type
		{
			background-color:#C2C8D5;
			color:#3D5075
		}

	</style>

	<div class="helpPage">
		<div class="helpHead">
			<div class="ticketHead">
				<h2 class="title"> {{ trans('main.invoice') }}</h2>
				<div class="numbTicket">
					<span class="numb">#{{ $data->invoice->id + 10000 }}</span>
					<a href="#">{{ trans('main.invoice_status_'.$data->invoice->status) }}</a>
				</div>
				@php 
		            $paymentObj = App\Models\PaymentInfo::NotDeleted()->where('user_id',$data->invoice->client_id)->first();
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
							<th>{{ trans('main.pubDate') }}</th>
							<th>{{ trans('main.due_date') }}</th>
							<th>{{ trans('main.paymentMethod') }}</th>
							<th>{{ trans('main.appName') }}</th>
							<th>{{ trans('main.createdFor') }}</th>
		   					<th>{{ trans('main.eInvoice') }}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{{ date('M d, Y',strtotime($data->invoice->created_at)) }}</td>
							<td>{{ date('M d, Y',strtotime($data->invoice->due_date)) }}</td>
							<td>{{ $data->invoice->status == 1 ? $data->invoice->payment_gateaway : '-------' }}</td>
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
								{{ $data->invoice->company }}
								<br>
								{{ $data->invoice->client }}
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
								<img src="{{$data->qrImage}}" width="100" height="100">
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
			                    <th colspan="3">{{ trans('main.item') }}</th>
			                    <th>{{ trans('main.quantity') }}</th>
			                    {{-- <th>{{ trans('main.start_date') }}</th> --}}
			                    {{-- <th>{{ trans('main.end_date') }}</th> --}}
			                    <th class="text-center">{{ trans('main.total') }}</th>
			                </tr>
			            </thead>
			            <tbody>
							@php 
								$mainPrices = 0; 
							@endphp
	                        @foreach($data->invoice->items as $key => $item)
	                        @php 
		                        $mainPrices+=$item['data']['price'] * $item['data']['quantity']; 
	                        @endphp
	                        <tr class="mainRow">
	                            <td>{{ $key+1 }}</td>
	                            <td colspan="3">
	                                <p>
	                                    <a href="#">{{ $item['data']['title_'.LANGUAGE_PREF] }}</a><br>
	                                    <small><b>{{ trans('main.extra_type') }}:</b> {{ trans('main.'.$item['type']) }} </small>
	                                </p>
	                            </td>
	                            <td>{{ $item['data']['quantity'] }}</td>
	                            {{-- <td>
	                            	@if($data->invoice->status == 1)
	                            	{{ $data->invoice->due_date }}
	                            	@endif
	                            </td>
	                            <td>
	                            	@if($data->invoice->status == 1)
	                            	{{ $item['data']['duration_type'] == 1 ? date('Y-m-d',strtotime('+1 month',strtotime($data->invoice->due_date)- 86400))  : date('Y-m-d',strtotime('+1 year',strtotime($data->invoice->due_date )- 86400)) }}
	                            	@endif
	                            </td> --}}
	                            <td class="text-center">{{ number_format((float) $item['data']['quantity'] * $item['data']['price_after_vat'], 2, '.', '') }} {{ trans('main.sar') }}</td>
	                        </tr>
	                        @endforeach
	                        @php
	                        	if($data->invoice->zidOrSalla){
									$oldDiscount = $data->invoice->discount;
									$tax = Helper::calcTax($data->invoice->roTtotal);
			                        $grandTotal =  $data->invoice->roTtotal - $tax;
			                        $total = $data->invoice->roTtotal;
	                        	}else{
	                        		$oldDiscount = $mainPrices - $data->invoice->total + $data->invoice->discount;
			                        $tax = Helper::calcTax($data->invoice->total);
			                        $grandTotal =  $data->invoice->total - $tax;
			                        $total = $data->invoice->total;
	                        	}

	                        	if($data->invoice->discount_value != null && $data->invoice->discount_type != null){
	                        		$oldDiscount = $data->invoice->discount;
    								$tax = $data->invoice->tax;
    		                        $grandTotal =  $data->invoice->grandTotal;
    		                        $total = $tax + $grandTotal;
	                        	}
	                        @endphp
	                        <input type="hidden" name="invoice_id" value="{{ $data->invoice->id }}">
	                        <tr>
	                            <td colspan="5"></td>
	                            <td class="text-left">
	                            	<p class="mb-2">
	                                    <span class="tx-bold">{{ trans('main.discount') }} :</span>
	                                    <span class="float-right">{{ number_format((float)$oldDiscount, 2, '.', '') }} {{ trans('main.sar') }}</span>
	                                    <div class="clearfix"></div>
	                                </p>
	                                <p class="mb-2">
	                                    <span class="tx-bold">{{ trans('main.grandTotal') }} :</span>
	                                    <span class="float-right">{{ number_format((float)$grandTotal, 2, '.', '') }} {{ trans('main.sar') }}</span>
	                                    <div class="clearfix"></div>
	                                </p>
	                                <p class="mb-2">
	                                    <span class="tx-bold">{{ trans('main.estimatedTax') }} :</span>
	                                    <span class="float-right">{{ number_format((float)$tax, 2, '.', '') }} {{ trans('main.sar') }}</span>
	                                    <div class="clearfix"></div>
	                                </p>
	                                <p class="mb-2">
	                                    <span class="tx-bold">{{ trans('main.total') }} :</span>
	                                    <span class="float-right">{{ number_format((float)$total, 2, '.', '') }}  {{ trans('main.sar') }}</span>
	                                    <div class="clearfix"></div>
	                                </p>
	                            </td>
	                        </tr>
			            </tbody>
			        </table>
				</div>	  	 			
			</div>
		</div>
	</div>
</div>