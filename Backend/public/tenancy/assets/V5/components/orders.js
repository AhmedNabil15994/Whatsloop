$(function(){

	$('#selectCountry .save').on('click',function(e){
	    var id = $('#selectCountry .selectList li.active').data('area');
	    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'get',
	        url: '/orders/getCities',
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'id': id,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	                $('#selectCity .selectList').empty();
	                var elemString = '';
	                $.each(data.regions,function(index,item){
	                    elemString+= '<li data-area="'+index+'"><label class="checkStyle"><i></i><span class="text">'+item.name+'</span></label></li>'
	                });
	                $('#selectCity .selectList').append(elemString);
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

	$(document).on('click','#selectPayment .selectForm li',function(){
		if($(this).hasClass('active')){
			$('input[name="payment_type"]').val($('.selectCircle li.active').data('area'));
		}else{
			$('input[name="payment_type"]').val('');
		}
	});

	$(document).on('click','.products.selectPayment .selectCircle li',function(){
		if($(this).hasClass('active')){
			$('input[name="payment_type"]').val($('.selectCircle li.active').data('area'));
		}else{
			$('input[name="payment_type"]').val('');
		}
	});

	$(document).on('click','.selectForm3 li',function(){
		if($(this).hasClass('active')){
			$('input[name="shipping_method"]').val($('.selectForm3 li.active').data('area'));
		}else{
			$('input[name="shipping_method"]').val('');
		}
	});


	$(document).on('click','#selectPayment a.next',function () {
		if($('input[name="payment_type"]').val()){
			$('input[name="total_after_discount"]').val($('span.myTotal').html());
			$(this).siblings('form').submit();
		}
	});

	$('.addCoupon').on('click',function (e) {
		e.preventDefault();
		var coupon = $(this).siblings('input[type="text"]').val();
		if(coupon && $(this).hasClass('addCoupon')){
			$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	        $.ajax({
	            type: 'POST',
	            url: '/orders/checkCoupon',
	            data:{
	                '_token': $('meta[name="csrf-token"]').attr('content'),
	                'coupon': coupon,
	                'total': $('span.myTotal').text(),
	            },
	            success:function(data){
	            	if(data.status){
	            		if(data.status.status == 0){
	            			errorNotification(data.status.message);
							$('input[name="coupon"]').val('');
	            		}
	            	}else{
	            		$('span.myTotal').html(data);
						$('input[name="coupon"]').val(coupon); 
						$('button.addCoupon').removeClass('addCoupon');
						$('input[name="total_after_discount"]').val(data);
	            	}
	            },
	        });
		}
	});
});