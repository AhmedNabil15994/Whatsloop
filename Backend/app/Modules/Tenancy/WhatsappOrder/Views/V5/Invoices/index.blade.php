<!DOCTYPE html>
<html>
<head>
    
    <meta charset="UTF-8" />
    <!-- IE Compatibility Meta -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- First Mobile Meta  -->
	<meta name="viewport" content="width=device-width, height=device-height ,  maximum-scale=1 , initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Invoice</title>
    

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="{{ asset('invoices/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('invoices/css/bootstrap-ar.min.css') }}">
    <link rel="stylesheet" href="{{ asset('invoices/css/style.css') }}">
        
    @yield('styles')
   <!--[if lt IE 9]>
       <script src="js/html5shiv.min.js"></script>
       <script src="js/respond.min.js"></script>
   <![endif]-->
  
    
</head>
<body class="invoice-color">

    @yield('content')    
     
    
    <script src="{{ asset('invoices/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('invoices/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('invoices/js/main.js') }}"></script>
    
</body>

</html>