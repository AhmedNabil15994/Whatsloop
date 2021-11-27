$(function(){

	$('select[name="duration_type"]').on('change',function(){
		if($(this).val() == 1){
			$('.mainCol input[type="checkbox"].yearly').attr('disabled',true);
			$('.mainCol input[type="checkbox"].yearly').prop('checked',false);
		}else{
			$('.mainCol input[type="checkbox"].yearly').attr('disabled',false);
		}
	});

	$('.mainCol input[type="checkbox"]').on('change',function(e){
		e.stopPropagation();
		e.preventDefault();
		if($(this).is(':checked')){
			if($(this).attr('class') == 'monthly'){
				if($(this).parents('.mainCol').find('input[type="checkbox"].yearly').is(':checked')){
					$(this).parents('.mainCol').find('input[type="checkbox"].yearly').prop('checked',false);
				}
			}else{
				if($(this).parents('.mainCol').find('input[type="checkbox"].monthly').is(':checked')){
					$(this).parents('.mainCol').find('input[type="checkbox"].monthly').prop('checked',false);
				}
			}
		}
		if(!$(this).hasClass('old')){
			$(this).parents('form').submit();
		}
	});

	$('#transferDaysModal .btn-success').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    var days = $('#transferDaysModal input[name="days"]').val();
	    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'POST',
	        url: myURL+'/transferDays',
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'days': days,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	                successNotification(data.status.message);
	                $('#transferDaysModal').modal('hide');
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

});