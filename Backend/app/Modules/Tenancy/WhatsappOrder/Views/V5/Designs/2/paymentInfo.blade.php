@extends('Tenancy.WhatsappOrder.Views.V5.Designs.2.index')
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
    <div class="container clearfix">
        <div class="step">
            <svg xmlns="http://www.w3.org/2000/svg" width="23.319" height="30.303" viewBox="0 0 23.319 30.303">
              <g id="_002-user" data-name="002-user" transform="translate(-59)">
                <g id="Group_54784" data-name="Group 54784" transform="translate(59 18.348)">
                  <path id="Path_201434" data-name="Path 201434" d="M81.135,321.955a1.184,1.184,0,0,1-1.184-1.184,8.414,8.414,0,0,0-8.4-8.4H69.772a8.414,8.414,0,0,0-8.4,8.4,1.184,1.184,0,0,1-2.367,0A10.784,10.784,0,0,1,69.772,310h1.776a10.784,10.784,0,0,1,10.772,10.772A1.184,1.184,0,0,1,81.135,321.955Z" transform="translate(-59 -310)" fill="#009aa5"/>
                </g>
                <g id="Group_54785" data-name="Group 54785" transform="translate(62.551)">
                  <path id="Path_201435" data-name="Path 201435" d="M126.99,15.98a7.99,7.99,0,1,1,7.99-7.99A8,8,0,0,1,126.99,15.98Zm0-13.613a5.623,5.623,0,1,0,5.623,5.623A5.629,5.629,0,0,0,126.99,2.367Z" transform="translate(-119)" fill="#009aa5"/>
                </g>
              </g>
            </svg>
        </div>
        <div class="step active">
            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="33.004" viewBox="0 0 33 33.004">
              <g id="_001-delivery" data-name="001-delivery" transform="translate(-0.03)">
                <g id="Group_54780" data-name="Group 54780" transform="translate(0.03)">
                  <path id="Path_201430" data-name="Path 201430" d="M7.12,17.117,2.107,12.1A7.09,7.09,0,1,1,14.21,7.09,7.043,7.043,0,0,1,12.134,12.1Zm0-15.183a5.157,5.157,0,0,0-3.646,8.8L7.12,14.383l3.646-3.646a5.157,5.157,0,0,0-3.646-8.8Z" transform="translate(-0.03)" fill="#798aa8"/>
                </g>
                <g id="Group_54781" data-name="Group 54781" transform="translate(3.897 3.867)">
                  <path id="Path_201431" data-name="Path 201431" d="M63.255,66.45a3.223,3.223,0,1,1,3.223-3.223A3.226,3.226,0,0,1,63.255,66.45Zm0-4.512a1.289,1.289,0,1,0,1.289,1.289A1.291,1.291,0,0,0,63.255,61.938Z" transform="translate(-60.032 -60.004)" fill="#798aa8"/>
                </g>
                <g id="Group_54782" data-name="Group 54782" transform="translate(22.717 20.555)">
                  <path id="Path_201432" data-name="Path 201432" d="M357.179,331.363l-3.646-3.646a5.157,5.157,0,1,1,7.293,0Zm-2.279-5.013,2.279,2.279,2.279-2.279a3.223,3.223,0,1,0-4.558,0Z" transform="translate(-352.022 -318.914)" fill="#798aa8"/>
                </g>
                <g id="Group_54783" data-name="Group 54783" transform="translate(6.153 14.783)">
                  <path id="Path_201433" data-name="Path 201433" d="M112.05,247.578H101.258a6.223,6.223,0,0,1,0-12.445h3.588a1.919,1.919,0,1,0,0-3.838h-3.751v-1.934h3.751a3.853,3.853,0,1,1,0,7.706h-3.588a4.289,4.289,0,0,0,0,8.578H112.05v1.934Z" transform="translate(-95.035 -229.361)" fill="#798aa8"/>
                </g>
              </g>
            </svg>

        </div>
        <div class="step paym">
            <svg xmlns="http://www.w3.org/2000/svg" width="26.489" height="23.177" viewBox="0 0 26.489 23.177">
              <g id="credit-card_1_" data-name="credit-card (1)" transform="translate(0 -32.004)">
                <g id="Group_66973" data-name="Group 66973" transform="translate(0 36.97)">
                  <g id="Group_66972" data-name="Group 66972" transform="translate(0 0)">
                    <path id="Path_201521" data-name="Path 201521" d="M25.661,137.929a.828.828,0,0,0-.828.828v5.794H1.656v-9.933h9.106a.828.828,0,1,0,0-1.656H1.656v-3.311h9.106a.828.828,0,0,0,0-1.656H1.656A1.656,1.656,0,0,0,0,129.652v14.9a1.656,1.656,0,0,0,1.656,1.656H24.833a1.656,1.656,0,0,0,1.656-1.656v-5.794A.828.828,0,0,0,25.661,137.929Z" transform="translate(0 -127.996)" fill="#798aa8"/>
                  </g>
                </g>
                <g id="Group_66975" data-name="Group 66975" transform="translate(3.311 46.904)">
                  <g id="Group_66974" data-name="Group 66974" transform="translate(0 0)">
                    <path id="Path_201522" data-name="Path 201522" d="M68.139,320H64.828a.828.828,0,1,0,0,1.656h3.311a.828.828,0,1,0,0-1.656Z" transform="translate(-64 -319.996)" fill="#798aa8"/>
                  </g>
                </g>
                <g id="Group_66977" data-name="Group 66977" transform="translate(13.245 32.004)">
                  <g id="Group_66976" data-name="Group 66976" transform="translate(0 0)">
                    <path id="Path_201523" data-name="Path 201523" d="M268.743,34.553l-5.794-2.483a.845.845,0,0,0-.654,0L256.5,34.553a.83.83,0,0,0-.5.762v3.311c0,4.554,1.684,7.217,6.21,9.824a.832.832,0,0,0,.824,0c4.526-2.6,6.21-5.263,6.21-9.824V35.315A.829.829,0,0,0,268.743,34.553Zm-1.154,4.073c0,3.823-1.265,5.927-4.967,8.145-3.7-2.223-4.967-4.328-4.967-8.145V35.861l4.967-2.129,4.967,2.129Z" transform="translate(-256 -32.004)" fill="#798aa8"/>
                  </g>
                </g>
                <g id="Group_66979" data-name="Group 66979" transform="translate(16.556 36.971)">
                  <g id="Group_66978" data-name="Group 66978">
                    <path id="Path_201524" data-name="Path 201524" d="M326.323,128.2a.833.833,0,0,0-1.164.129l-2.6,3.255-1.03-1.54a.827.827,0,0,0-1.377.917l1.656,2.483a.834.834,0,0,0,.654.369h.035a.828.828,0,0,0,.647-.311l3.311-4.139A.828.828,0,0,0,326.323,128.2Z" transform="translate(-320.011 -128.016)" fill="#798aa8"/>
                  </g>
                </g>
              </g>
            </svg>

        </div>
    </div>
</div>    
    
<form class="formInfo" method="POST" action="{{ URL::current() }}">
	@csrf
    <div class="container clearfix">
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
    </div>
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

