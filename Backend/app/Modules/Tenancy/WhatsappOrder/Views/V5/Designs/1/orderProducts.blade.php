@extends('Tenancy.WhatsappOrder.Views.V5.Designs.1.index')

@section('title','مراجعة الطلب')
@section('itemCounts',$data->products_count)

@section('styles')
<style type="text/css" media="screen">
    .myTotal{
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<div class="products">
	<div class="items">
		@foreach($data->products as $product)
		<div class="item">
			<a href="{{ $product['images'][0] }}" class="img"><img src="{{ $product['images'][0] }}" alt="" /></a>
			{{-- <i class="remove"><img src="images/trash.png" alt="" /></i> --}}
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
		<button data-toggle="modal" data-target="#selectPayment">التالي</button>
	</div>
</div>


<div id="selectPayment" class="modal modalStyle fade" role="dialog">
    <div class="modal-dialog">
                    
        <div class="modal-content">

            <div class="modal-body">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <div class="selectForm" id="formSelect2">
               		<h2 class="title">حدد طريقة الدفع</h2>
               		<ul class="selectCircle selectList">
               			<li data-area="1">
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">الدفع عند الاستلام</span>
                            </label>
               			</li>
               			<li data-area="2">
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">الدفع داخل الفرع</span>
                            </label>
               			</li>
               			<li data-area="3">
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">التحويل البنكي</span>
                            </label>
               			</li>
               			<li data-area="4">
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">دفع الكتروني</span>
                            </label>
               			</li>
               		</ul>
               		<a href="#" class="next" data-dismiss="modal">التالي</a>
               		<form action="{{ URL::current() }}" method="post">
               			@csrf

               			<input type="hidden" name="total_after_discount" value="">
               			<input type="hidden" name="coupon" value="">
               			<input type="hidden" name="payment_type" value="">
               		</form>
               </div>
            </div>
        </div>
    </div>
</div>
@endsection

