@extends('Tenancy.WhatsappOrder.Views.V5.Designs.2.index')

@section('title','مراجعة الطلب')
@section('itemCounts',$data->data->products_count)

@section('styles')
<style type="text/css" media="screen">
    .myTotal{
        margin-right: 5px;
    }
    .Count{
    	background: transparent;
    }
    .option{
    	margin: 10px;
    }
    .selectCircle li{
    	width: 50%;
    	display: inline-block;
    	float: right;
    	border-bottom: 0 !important;
    }
    .selectCircle li .text {
	    margin-right: 25px;
	    padding-top: 5px;
	}
	.checkStyle i{
		left: unset;
		right: 15px;
	}
</style>
@endsection

@section('content')


    
<div class="products">
	<div class="container">
		<form action="{{ URL::current() }}" method="post">
        	@csrf
			<h2 class="titleStyle">مراجعة الطلب</h2>
	    	<div class="items">
	    		@foreach($data->data->products as $product)
	    		<div class="item">
	    			<a href="{{ $product['images'][0] }}" class="img"><img src="{{ $product['images'][0] }}" alt="" /></a>
	    			{{-- <i class="remove fa fa-close"></i> --}}
	    			<div class="details">
	    				<a href="#" class="title">{{ $product['name'] }}</a>
	    				<div class="clearfix price">
	    					<div class="newprice">{{ $product['price'] . ' ' . $product['currency'] }}</div>
	    					<div class="oldprice">{{ $product['price'] . ' ' . $product['currency'] }}</div>
	    				</div>
						<div class="priceCount clearfix">
	    					<h2 class="selectCount">الكمية:</h2>
	                        <div class="Count" id="count1">
	                            {{-- <span class="plus"><i class="fa fa-angle-up"></i></span> --}}
	                            <strong>{{ $product['quantity'] }}</strong>
	                            {{-- <span class="minus"><i class="fa fa-angle-down"></i></span> --}}
	                        </div>
						</div>
	    			</div>

	    			@php $details = $data->productDetails[$product['id']]; @endphp
					@foreach($details['options'] as $value)
					<div class="details option">
						<div class="row">
							<div class="col-md-3">
			    				<div class="title">{{ $value['name'] }}</div>					
							</div>
							<div class="col-md-9">
								@if($value['type'] == 'radio')
								<select class="form-control" name="options[{{ $product['id'] }}][{{$value['id']}}]">
									<option value="">{{ trans('main.choose') }}</option>
									@foreach($value['values'] as $oneOption)
									<option value="{{ $oneOption['id'] }}">{{ $oneOption['name'] }}</option>
									@endforeach
								</select>

								@elseif($value['type'] == 'text')
								<input type="text" class="form-control" name="options[{{ $product['id'] }}][{{ $value['id'] }}]" placeholder="{{ $value['description'] != "" ? $value['description'] : $value['name'] }}">

								@elseif($value['type'] == 'textarea')
								<textarea class="form-control" name="options[{{ $product['id'] }}][{{ $value['id'] }}]" placeholder="{{ $value['description'] != "" ? $value['description'] : $value['name'] }}"></textarea>

								@elseif($value['type'] == 'checkbox')
								<div class="selectForm">
			                   		<ul class="selectCircle selectList">
			                   			@foreach($value['values'] as $oneCheckBox)
			                   			<li data-type="{{ $oneCheckBox['id'] }}">
				                            <label class="checkStyle">
				                                <i></i>
				                                <span class="text">{{ $oneCheckBox['name'] }}</span>
				                            </label>
			                   			</li>
			                   			@endforeach 
			                   		</ul>
			                   		<input type="hidden" name="options[{{ $product['id'] }}][{{ $value['id'] }}]">
			                   	</div>
			                   	<div class="clearfix"></div>
								@endif	
								<hr>
							</div>
						</div>
					</div>
					@endforeach
	    		</div>
	    		@endforeach
	    	</div>
	    	<div class="coupon">
				<div class="relative">
		    		<input type="text" placeholder="أضف كوبون الخصم" />
		    		<button class="addCoupon">اضافة</button>
				</div>
			</div>
	    	<div class="total">
	    		<h2 class="totalPrice clearfix">الأجمالي: <span class="myTotal">{{ $data->data->total }}</span> &nbsp; <span>{{ $data->data->products[0]['currency'] }}</span></h2>
	   			<input type="hidden" name="total_after_discount" value="{{ $data->data->total }}">
	   			<input type="hidden" name="coupon" value="">
	   			{{-- <input type="hidden" name="payment_type" value=""> --}}
				<button href="Info3.html" class="btnStyle">التالي</button>
	    	</div>
    	</form>
	</div>
</div>

@endsection

@section('scripts')
<script>
	$(function(){
		$('.selectCircle li').on('click',function(e){
			e.preventDefault();
			e.stopPropagation();
			$(this).toggleClass('active');
		});

		$('.btnStyle').on('click',function(e){
			e.preventDefault();
			e.stopPropagation();

			$.each($('.selectCircle'),function(index,item){
				var dataArr = [];
				var resultItem = $(item).siblings($('input[type="hidden"]'));
				$.each($(item).children('li.active'),function(liIndex,liItem){
					dataArr.push($(liItem).data('type'));
				});
				resultItem.val(JSON.stringify(dataArr));
			});

			$('form').submit();

		});
	});
</script>
@endsection
