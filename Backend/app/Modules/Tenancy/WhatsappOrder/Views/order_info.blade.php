<!DOCTYPE html>
<html>
<head>
    
   <meta charset="UTF-8" />
    <!-- IE Compatibility Meta -->
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- First Mobile Meta  -->
	<meta name="viewport" content="width=device-width, height=device-height ,  maximum-scale=1 , initial-scale=1">
    
    <title></title>
    

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="{{ asset('products/theme1/css/owl.carousel.min.css') }}" />
	<link rel="stylesheet" href="{{ asset('products/theme1/css/owl.theme.default.min.css') }}" />

	<link rel="stylesheet" href="{{ asset('products/theme1/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('products/theme1/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('products/theme1/css/bootstrap-rtl.css') }}" />
    <link rel="stylesheet" href="{{ asset('products/theme1/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('products/theme1/css/jquery.bxslider.css') }}" />
    <link rel="stylesheet" href="{{ asset('products/theme1/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('products/theme1/css/responisve.css') }}" />
        
          
   <!--[if lt IE 9]>
       <script src="js/html5shiv.min.js"></script>
       <script src="js/respond.min.js"></script>
   <![endif]-->
  
    
</head>
<body>

    
	<div class="splash">
		<div>
			<img src="{{ asset('products/theme1/images/logoSplash.png') }}" alt="" />
		</div>
	</div> 
    
    <div class="header">
    	<a href="#" class="cart"><img src="{{ asset('products/theme1/images/cart.png') }}" alt="" /> {{ count($data->products) }}</a>
    	<h2 class="title">مراجعة الطلب</h2>
    </div>
    
   <div class="headBg"></div>
   
    
    
    <div class="products">
    	<div class="items">
            @foreach($data->products as $product)
    		<div class="item">
    			<a href="#" class="img"><img src="{{ $product['images'][0] }}" alt="" /></a>
    			{{-- <i class="remove"><img src="{{ asset('products/theme1/images/trash.png') }}" alt="" /></i> --}}
    			<div class="details">
    				<a href="#" class="title">{{ $product['name'] }}</a>
    				<div class="clearfix price">
    					<div class="newprice">{{ $product['price'] }} {{ $product['currency'] }}</div>
    					<div class="oldprice">{{ $product['price'] }} {{ $product['currency'] }}</div>
    				</div>
					<div class="priceCount clearfix">
    					<h2 class="selectCount">{{ trans('main.quantity') }}:</h2>
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
	    		<input type="text" placeholder="{{ trans('main.couponCode') }}" />
	    		<button>{{ trans('main.add') }}</button>
    		</div>
    	</div>
    	<div class="total">
    		<h2 class="totalPrice clearfix">{{ trans('main.total') }}: <span>{{ $data->total }} {{ $product['currency'] }}</span></h2>
    		<button data-toggle="modal" data-target="#selectPayment">التالي</button>
    	</div>
    </div>
    
    <form action="{{ URL::current().'/info' }}" method="get" accept-charset="utf-8">
        <input type="hidden" name="type">
    </form>
    
    <div id="selectPayment" class="modal modalStyle fade" role="dialog">
          <div class="modal-dialog">
                        
            <div class="modal-content">
    
                <div class="modal-body">
                   <button type="button" class="close" data-dismiss="modal">&times;</button>
                   <div class="selectForm" id="formSelect2">
                   		<h2 class="title">حدد طريقة الدفع</h2>
                   		<ul class="selectCircle selectList">
                   			<li data-type="1">
	                            <label class="checkStyle">
	                                <i></i>
	                                <span class="text">الدفع عند الاستلام</span>
	                            </label>
                   			</li>
                   			<li data-type="2">
	                            <label class="checkStyle">
	                                <i></i>
	                                <span class="text">الدفع داخل الفرع</span>
	                            </label>
                   			</li>
                   			<li data-type="3">
	                            <label class="checkStyle">
	                                <i></i>
	                                <span class="text">التحويل البنكي</span>
	                            </label>
                   			</li>
                   			<li data-type="4">
	                            <label class="checkStyle">
	                                <i></i>
	                                <span class="text">دفع الكتروني</span>
	                            </label>
                   			</li>
                   		</ul>
                   		<a href="#" class="next" data-dismiss="modal">التالي</a>
                   </div>
                   
                </div>
            </div>
        </div>
    </div>
    
 
    <div id="thanks" class="modal thanks fade" role="dialog">
          <div class="modal-dialog">
                        
            <div class="modal-content">
    			<button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modal-body">
    				<center>
	                   <img src="{{ asset('products/theme1/images/waiting.png') }}" class="fa fa-spin" alt="" />
	                   <h2 class="thankU">شكراً لك</h2>
	                   <div class="desc">جاري التوجه لبوابة الدفع الالكتروني</div>
                   </center>
                </div>
            </div>
        </div>
    </div>
    
    <div id="thanks2" class="modal thanks fade" role="dialog">
          <div class="modal-dialog">
                        
            <div class="modal-content">
    			<button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="modal-body">
    				<center>
	                   <img src="{{ asset('products/theme1/images/delivery-truck.png') }}" alt="" />
	                   <h2 class="thankU">شكراً لك</h2>
	                   <div class="desc">
		                   	تم ارسال طلبكم بنجاح رقم الطلب
		                   	<span>#2568656</span>
	                   </div>
                   </center>
                </div>
            </div>
        </div>
    </div>
     
    
    <script src="{{ asset('products/theme1/js/jquery-1.11.2.min.js') }}"></script>
    <script src="{{ asset('products/theme1/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('products/theme1/js/jquery.bxslider.min.js') }}"></script>
    <script src="{{ asset('products/theme1/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('products/theme1/js/wow.min.js') }}"></script>
    <script src="{{ asset('products/theme1/js/scrollIt.min.js') }}"></script>
    <script src="{{ asset('products/theme1/js/custom.js') }}"></script>
    
</body>

</html>