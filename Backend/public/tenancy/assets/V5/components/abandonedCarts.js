$(function(){
	var lang = $('html').attr('lang');
	$('select[name="message_type"]').on('change',function(){
		var dateID = $(this).val();
		$('input[name="reply"]').val('');
		$('.reply[data-id="'+dateID+'"]').children('.hidden').removeClass('hidden');
		$('.reply[data-id="'+dateID+'"]').siblings('.reply').children('.row').addClass('hidden');
	});


	$('input[name="sending"]').on('change',function(){
		$("input[name='date']").toggleClass('hidden');
	});

	$('#datetimepicker').datetimepicker({
		format: 'YYYY-MM-DD HH:mm',
		stepping: 5,
	});


 	

	$(".resendCarts").click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		var sendTime = 1;
		var message = $('textarea[name="body"]').text();

		if($('input#radio2').is(':checked')){
			sendTime = $('input[name="date"]').val();
		}

		var clientsData = [];
		$.each($('select[name="clients"] option:selected'),function(index,item){
			var client={
				'name' : $(item).data('name') ,
				'mobile' : $(item).data('mobile') ,
				'order_id' : $(item).val() ,
				'total' : $(item).data('total') ,
				'url' : $(item).data('url') ,
			};
			clientsData.push(client);
		});
		

		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'POST',
	        url: myURL+'/sendAbandoned',
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'sendTime': sendTime,
	            'message': message,
	            'clientsData': clientsData,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	                successNotification(data.status.message);
	                $('.modal').modal('hide');
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

})