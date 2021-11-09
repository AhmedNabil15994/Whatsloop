@extends('Tenancy.WhatsappOrder.Views.V5.Designs.2.index')

@section('title','مراجعة الطلب')
@section('itemCounts',$data->products_count)

@section('styles')
<style type="text/css" media="screen">
    .myTotal{
        margin-right: 5px;
    }
    .Count{
    	background: transparent;
    }
</style>
@endsection

@section('content')


    
<div class="products">
	<div class="container">
		<h2 class="titleStyle">مراجعة الطلب</h2>
    	<div class="items">
    		@foreach($data->products as $product)
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
    		<h2 class="totalPrice clearfix">الأجمالي: <span class="myTotal">{{ $data->total }}</span> &nbsp; <span>{{ $data->products[0]['currency'] }}</span></h2>
    		<form action="{{ URL::current() }}" method="post">
               	@csrf
       			<input type="hidden" name="total_after_discount" value="">
       			<input type="hidden" name="coupon" value="">
       			{{-- <input type="hidden" name="payment_type" value=""> --}}
    			<button href="Info3.html" class="btnStyle">التالي</button>
       		</form>
    	</div>
	</div>
</div>

@endsection

