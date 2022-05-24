@extends('Tenancy.WhatsappOrder.Views.V5.Designs.1.index')
@section('itemCounts',$data->order->products_count)

@section('title',$data->user->company)

@section('content')

<div class="storeHead">
	<img src="{{ $data->user->photo }}" alt="" />
</div>

<div class="infoSteps">
	<div class="container">
    	<div class="step active">
    		<img src="{{ asset('designs/1/images/002-user.png') }}" alt="" class="icon" /> 
    		البيانات الشخصية
    		<a href="#" class="iconEdit"><img src="{{ asset('designs/1/images/edit (1).png') }}" alt="" /></a>
    		<i class="fa fa-check checkStep"></i>
    	</div>
    	<div class="step">
    		<img src="{{ asset('designs/1/images/001-delivery.png') }}" alt="" class="icon" /> 
    		معلومات الشحن
    		<a href="#" class="iconEdit"><img src="{{ asset('designs/1/images/edit (1).png') }}" alt="" /></a>
    		<i class="fa fa-check checkStep"></i>
    	</div>
    	<div class="step">
    		<img src="{{ asset('designs/1/images/credit-card (1).png') }}" alt="" class="icon credit" /> 
    		طرق الدفع
    		<a href="#" class="iconEdit"><img src="{{ asset('designs/1/images/edit (1).png') }}" alt="" /></a>
    		<i class="fa fa-check checkStep"></i>
    	</div>
	</div>
</div>


<form class="formInfo" method="POST" action="{{ URL::current() }}">
	@csrf
	<label class="titleForm">الاسم</label>
	<input type="text" class="inputStyle" name="name" value="{{$data->order->Details != null ? $data->order->Details->name : ''}}" placeholder="اضف اسمك ثنائي" />
	<label class="titleForm">البريد الالكتروني</label>
	<input type="email" class="inputStyle" name="email" value="{{$data->order->Details != null ? $data->order->Details->email : ''}}" placeholder="اضف البريد الالكتروني" />
	<label class="titleForm">رقم الجوال</label>
	<input type="number" class="inputStyle" name="phone" value="{{$data->order->Details != null ? $data->order->Details->phone : ''}}" {{ $data->order->Details != null ? $data->order->Details->phone : 'disabled' }} placeholder="رقم جوالك على الواتس اب" />
	<button class="btnStyle">التالي</button>
</form>
    
@endsection

