@extends('Tenancy.WhatsappOrder.Views.V5.Designs.1.index')
@section('itemCounts',$data->order->products_count)

@section('title',$data->user->company)

@php 
$counter = 0;
@endphp
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
    	<div class="step active">
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
	<label class="titleForm">الدولة</label>
	<div class="inputStyle formSelect1">
		<input type="text" class="inputStyle" name="country" id="inputCountry" placeholder="حدد الدولة" />
		<span  class="angle fa fa-angle-left" data-toggle="modal" data-target="#selectCountry"></span>
	</div>
	
	
	<label class="titleForm">المدينة</label>
	<div class="inputStyle formSelect2">
		<input type="text" class="inputStyle" name="city" placeholder="حدد المدينة" />
		<span  class="angle fa fa-angle-left"  data-toggle="modal" data-target="#selectCity"></span>
	</div>
	
	<label class="titleForm">الحي</label>
	<input type="text" class="inputStyle" name="region" placeholder="اكتب اسم الحي" />
	<label class="titleForm">الشارع</label>
	<input type="text" class="inputStyle" name="address" placeholder="اكتب اسم الشارع" />
	
	
	<label class="titleForm">خيارات الشحن</label>
	<div class="inputStyle selectForm3">
		<input type="hidden" name="shipping_method" value="2">
		<input type="text" class="inputStyle" placeholder="حدد خيار الشحن المناسب" />
		<span  class="angle fa fa-angle-left openSelect"></span>
   		<ul class="selectCircle selectForm" id="selectForm3">
   			<li data-area="1">
                <label class="checkStyle">
                    <i></i>
                     <span class="text">شحن سريع</span>
                    <span class="days">(1-2 ايام)</span>
                </label>
   			</li>
   			<li data-area="2">
                <label class="checkStyle">
                    <i></i>
                    <span class="text">شحن عادي مجاني</span>
                    <span class="days">(1-2 ايام)</span>
                </label>
   			</li>
		</ul>
	</div>
	
	
	<button class="btnStyle">التالي</button>
</form>


<div id="selectCountry" class="modal modalStyle fade" role="dialog">
      <div class="modal-dialog">
                    
        <div class="modal-content">

            <div class="modal-body">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <div class="selectForm" id="formSelect1">
               		<h2 class="title">تحديد الدولة </h2>
               		<ul class="selectList">
               			@foreach($data->countries as $key => $country)
               			@if($key != 'il')
               			<li data-area="{{ $key }}">
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">{{ $country['native_official_name'] }}</span>
                            </label>
               			</li>
               			@endif
               			@endforeach
               		</ul>
               		<span class="save" data-dismiss="modal">حفظ</span>
               </div>
               
            </div>
        </div>
    </div>
</div>

<div id="selectCity" class="modal modalStyle fade" role="dialog">
      <div class="modal-dialog">
                    
        <div class="modal-content">

            <div class="modal-body">
               <button type="button" class="close" data-dismiss="modal">&times;</button>
               <div class="selectForm" id="formSelect2">
               		<h2 class="title">تحديد المدينة</h2>
               		<ul class="selectList">
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 1</span>
                            </label>
               			</li>
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 2</span>
                            </label>
               			</li>
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 3</span>
                            </label>
               			</li>
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 4</span>
                            </label>
               			</li>
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 5</span>
                            </label>
               			</li>
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 6</span>
                            </label>
               			</li>
               			<li>
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">حي 7</span>
                            </label>
               			</li>
               		</ul>
               		<span class="save" data-dismiss="modal">حفظ</span>
               </div>
               
            </div>
        </div>
    </div>
</div>
@endsection

