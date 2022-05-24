@extends('Tenancy.WhatsappOrder.Views.V5.Designs.3.index')
@section('itemCounts',$data->order->products_count)

@section('title',$data->user->company)

@php 
$counter = 0;
@endphp
@section('content')

<div class="infoSteps">
    <div class="container clearfix">
        <a href="#" class="step active">
            <svg id="_002-user" data-name="002-user" xmlns="http://www.w3.org/2000/svg" width="34.086" height="44.294" viewBox="0 0 34.086 44.294">
              <g id="Group_54784" data-name="Group 54784" transform="translate(0 26.819)">
                <path id="Path_201434" data-name="Path 201434" d="M91.355,327.475a1.73,1.73,0,0,1-1.73-1.73A12.3,12.3,0,0,0,77.34,313.46h-2.6A12.3,12.3,0,0,0,62.46,325.745a1.73,1.73,0,0,1-3.46,0A15.763,15.763,0,0,1,74.745,310h2.6a15.763,15.763,0,0,1,15.745,15.745A1.73,1.73,0,0,1,91.355,327.475Z" transform="translate(-59 -310)" fill="#bcbcbc"/>
              </g>
              <g id="Group_54785" data-name="Group 54785" transform="translate(5.191)">
                <path id="Path_201435" data-name="Path 201435" d="M130.679,23.358a11.679,11.679,0,1,1,11.679-11.679A11.692,11.692,0,0,1,130.679,23.358Zm0-19.9a8.219,8.219,0,1,0,8.219,8.219A8.228,8.228,0,0,0,130.679,3.46Z" transform="translate(-119)" fill="#bcbcbc"/>
              </g>
            </svg>

        </a>
        <a href="#" class="step active">
            <svg xmlns="http://www.w3.org/2000/svg" width="49.568" height="50.557" viewBox="0 0 49.568 50.557">
              <g id="Group_67028" data-name="Group 67028" transform="translate(-168.73 -148.883)">
                <path id="Path_201430" data-name="Path 201430" d="M10.891,26.221l-7.68-7.68a10.861,10.861,0,1,1,18.541-7.68,10.79,10.79,0,0,1-3.181,7.68Zm0-23.259A7.9,7.9,0,0,0,5.306,16.447l5.585,5.585,5.586-5.585A7.9,7.9,0,0,0,10.891,2.962Z" transform="translate(168.7 148.883)" fill="#bcbcbc"/>
                <g id="Group_54781" data-name="Group 54781" transform="translate(174.64 154.808)">
                  <path id="Path_201431" data-name="Path 201431" d="M64.969,69.878a4.937,4.937,0,1,1,4.937-4.937A4.942,4.942,0,0,1,64.969,69.878Zm0-6.912a1.975,1.975,0,1,0,1.975,1.975A1.977,1.977,0,0,0,64.969,62.966Z" transform="translate(-60.032 -60.004)" fill="#bcbcbc"/>
                </g>
                <g id="Group_54782" data-name="Group 54782" transform="translate(202.5 180.37)">
                  <path id="Path_201432" data-name="Path 201432" d="M359.921,337.984l-5.586-5.585a7.9,7.9,0,1,1,11.171,0Zm-3.491-7.68,3.491,3.491,3.491-3.491a4.937,4.937,0,1,0-6.982,0Z" transform="translate(-352.022 -318.914)" fill="#bcbcbc"/>
                </g>
                <g id="Group_54783" data-name="Group 54783" transform="translate(177.748 171.529)">
                  <path id="Path_201433" data-name="Path 201433" d="M121.1,257.267H104.567a9.532,9.532,0,0,1,0-19.064h5.5a2.94,2.94,0,1,0,0-5.88h-5.746v-2.962h5.746a5.9,5.9,0,1,1,0,11.8h-5.5a6.57,6.57,0,0,0,0,13.14H121.1v2.962Z" transform="translate(-95.035 -229.361)" fill="#bcbcbc"/>
                </g>
              </g>
            </svg>


        </a>
        <a href="#" class="step paym">
            <svg id="credit-card_1_" data-name="credit-card (1)" xmlns="http://www.w3.org/2000/svg" width="42.52" height="37.205" viewBox="0 0 42.52 37.205">
              <g id="Group_66973" data-name="Group 66973" transform="translate(0 7.972)">
                <g id="Group_66972" data-name="Group 66972" transform="translate(0 0)">
                  <path id="Path_201521" data-name="Path 201521" d="M41.192,143.941a1.329,1.329,0,0,0-1.329,1.329v9.3H2.658V138.626H17.274a1.329,1.329,0,1,0,0-2.657H2.658v-5.315H17.274a1.329,1.329,0,0,0,0-2.658H2.658A2.658,2.658,0,0,0,0,130.654v23.918a2.658,2.658,0,0,0,2.658,2.658H39.863a2.659,2.659,0,0,0,2.658-2.658v-9.3A1.329,1.329,0,0,0,41.192,143.941Z" transform="translate(0 -127.996)" fill="#bcbcbc"/>
                </g>
              </g>
              <g id="Group_66975" data-name="Group 66975" transform="translate(5.315 23.917)">
                <g id="Group_66974" data-name="Group 66974" transform="translate(0 0)">
                  <path id="Path_201522" data-name="Path 201522" d="M70.644,320H65.329a1.329,1.329,0,1,0,0,2.657h5.315a1.329,1.329,0,1,0,0-2.657Z" transform="translate(-64 -319.996)" fill="#bcbcbc"/>
                </g>
              </g>
              <g id="Group_66977" data-name="Group 66977" transform="translate(21.26 0)">
                <g id="Group_66976" data-name="Group 66976">
                  <path id="Path_201523" data-name="Path 201523" d="M276.455,36.1l-9.3-3.986a1.357,1.357,0,0,0-1.05,0L256.8,36.1a1.332,1.332,0,0,0-.8,1.222v5.315c0,7.311,2.7,11.584,9.968,15.77a1.336,1.336,0,0,0,1.323,0c7.266-4.175,9.968-8.448,9.968-15.77V37.318A1.33,1.33,0,0,0,276.455,36.1ZM274.6,42.633c0,6.136-2.03,9.514-7.973,13.075-5.942-3.569-7.973-6.947-7.973-13.075V38.2l7.973-3.418L274.6,38.2Z" transform="translate(-256 -32.004)" fill="#bcbcbc"/>
                </g>
              </g>
              <g id="Group_66979" data-name="Group 66979" transform="translate(26.576 7.974)">
                <g id="Group_66978" data-name="Group 66978">
                  <path id="Path_201524" data-name="Path 201524" d="M330.143,128.3a1.338,1.338,0,0,0-1.868.207l-4.178,5.225-1.653-2.471a1.328,1.328,0,0,0-2.211,1.472l2.658,3.986a1.34,1.34,0,0,0,1.05.593H324a1.329,1.329,0,0,0,1.039-.5l5.315-6.644A1.33,1.33,0,0,0,330.143,128.3Z" transform="translate(-320.011 -128.016)" fill="#bcbcbc"/>
                </g>
              </g>
            </svg>

        </a>
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
               			<li data-area="{{ $country->id }}">
                            <label class="checkStyle">
                                <i></i>
                                <span class="text">{{ $country->name }}</span>
                            </label>
                        </li>
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

