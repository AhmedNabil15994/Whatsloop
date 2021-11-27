<!DOCTYPE html>
<html>
<head>
    
    <meta charset="UTF-8" />
    <!-- IE Compatibility Meta -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- First Mobile Meta  -->
	<meta name="viewport" content="width=device-width, height=device-height ,  maximum-scale=1 , initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title')</title>
    

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="{{ asset('designs/2/css/owl.carousel.min.css') }}" />
	<link rel="stylesheet" href="{{ asset('designs/2/css/owl.theme.default.min.css') }}" />

	<link rel="stylesheet" href="{{ asset('designs/2/css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('designs/2/css/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('designs/2/css/bootstrap-rtl.css') }}" />
    <link rel="stylesheet" href="{{ asset('designs/2/css/font-awesome.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('designs/2/css/jquery.bxslider.css') }}" />
    <link rel="stylesheet" href="{{ asset('V5/css/toastr.min.css') }}"  type="text/css">
    <link rel="stylesheet" href="{{ asset('designs/2/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('designs/2/css/responisve.css') }}" />
        
    @yield('styles')
   <!--[if lt IE 9]>
       <script src="js/html5shiv.min.js"></script>
       <script src="js/respond.min.js"></script>
   <![endif]-->
  
    
</head>
<body>

    
	<div class="splash">
		<div>
			<img src="{{ asset('designs/2/images/logoSplash.png') }}" alt="" />
		</div>
	</div> 
    
    <div class="header">
        @if(Request::segment(3) != 'finish')
    	<a href="#" class="cart"><img src="{{ asset('designs/2/images/cart.png') }}" alt="" /> @yield('itemCounts')</a>
    	@endif
        <h2 class="title">@yield('title')</h2>
    </div>
    
   <div class="headBg"></div>
    
    
    @yield('content')    
     
    
    <script src="{{ asset('designs/2/js/jquery-1.11.2.min.js') }}"></script>
    <script src="{{ asset('designs/2/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('designs/2/js/jquery.bxslider.min.js') }}"></script>
    <script src="{{ asset('designs/2/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('designs/2/js/wow.min.js') }}"></script>
    <script src="{{ asset('designs/2/js/scrollIt.min.js') }}"></script>
    <script src="{{ asset('designs/2/js/custom.js') }}"></script>
    <script src="{{ asset('V5/js/toastr.min.js') }}"></script>
    <script src="{{ asset('V5/components/notifications.js') }}"></script>
    <script src="{{ asset('V5/components/orders.js') }}"></script>
    @yield('scripts')
    @include('tenant.Partials.notf_messages')
    
</body>

</html>