@extends('Tenancy.WhatsappOrder.Views.V5.Designs.3.index')

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
    	<div class="items">
    		@foreach($data->products as $product)
    		<div class="item">
    			<a href="{{ $product['images'][0] }}" class="img"><img src="{{ $product['images'][0] }}" alt="" /></a>
    			{{-- <i class="remove"><img src="images/trash.png" /> </i> --}}
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
			{{--<div class="dates">
    				<h2 class="openDateTitle">المواعيد المتاحة <i class="fa fa-angle-down"></i></h2>
    				<div class="content">
						<center>
				  			<input type="text" id="openDateFirst" class="openDate" />
				  			<div class="date-range-container" id="date-range-container"></div>
			  			</center>
	    				<ul class="selectTime">
	    					<li>
	    						<span class="from"><strong>من :</strong> 11:00 ص</span>
	    						<span class="to"><strong>إلى :</strong> 10:00 م</span>
	    						<span class="day">17 Aug</span>
	    						<i class="fa fa-check check"></i>
	    					</li>
	    					<li>
	    						<span class="from"><strong>من :</strong> 11:00 ص</span>
	    						<span class="to"><strong>إلى :</strong> 10:00 م</span>
	    						<span class="day">18 Aug</span>
	    						<i class="fa fa-check check"></i>
	    					</li>
	    					<li>
	    						<span class="from"><strong>من :</strong> 11:00 ص</span>
	    						<span class="to"><strong>إلى :</strong> 10:00 م</span>
	    						<span class="day">19 Aug</span>
	    						<i class="fa fa-check check"></i>
	    					</li>
	    				</ul>
    				</div> 
    			</div>--}}
    		</div>
    		@endforeach
    	</div>
    	<div class="total">
	    	<div class="coupon">
	    		<div class="relative">
		    		<input type="text" placeholder="أضف كوبون الخصم" />
		    		<button class="addCoupon">اضافة</button>
	    		</div>
    		</div>
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

