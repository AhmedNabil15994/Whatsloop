$(function(){
	var lang = $('html').attr('lang');
	if(lang == 'en'){
		var myNow = 'Now';
		var groupVar = 'Group';
		var senderVar = 'Sender';
		var messageVar = 'Message';
		var countVar = 'Messages Count';
		var sendTimeVar = 'Sending Time';
		var sendVar = 'Send';
		var titleVar = "Confirm sending Message(s)";
		var backVar = "Back";
		var contactsVar = 'Contacts';
		var newContactsVar = 'New Contacts';
	}else{
		var myNow = 'الآن';
		var groupVar = 'المجموعة';
		var senderVar = 'الراسل';
		var messageVar = 'الرسالة';
		var countVar = 'عدد الرسائل';
		var sendTimeVar = 'تاريخ الراسل';
		var sendVar = 'ارسال';
		var titleVar = "تأكيد ارسال الرسائل";
		var backVar = "الرجوع";
		var contactsVar = 'جهات الارسال';
		var newContactsVar = 'جهات ارسال جديدة';
	}

	$('textarea[name="messageText"],input[name="message"],input[name="https_url"],input[name="whatsapp_no"]').keyup(function(){
		$('.message.received').html($(this).val());
	});
	$('textarea[name="messageText"],input[name="message"],input[name="https_url"],input[name="whatsapp_no"]').blur(function(){
		$('.message.received').html($(this).val());
	});

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


 	$("select[name='group_id']").on('change',function(){
 		var lastOpt = $(this).val();
 		if(lastOpt == '@'){
	    	$(this).parents('.row').siblings('.row.mains').toggleClass('hidden');
	    }else{
	    	$(this).parents('.row').siblings('.row.mains').addClass('hidden');
	    }
 	});

 	$(document).on('click','.segment',function(e){
 		e.preventDefault();
 		$('.segmentModal').modal('toggle');
 	});

 	var deleteFlag = 0;
 	$(document).on('click','.segmentModal .clientRow .addClient',function(e){
 		e.preventDefault();
 		e.stopPropagation();
 		var phone = $(this).parents('.clientRow').find('.phone').text();
 		var textareaElement = $('textarea[name="whatsappNos"]');
 		if (textareaElement.val().indexOf(phone) == -1){
 			if(deleteFlag){
	 			var newVal = textareaElement.val() + '\n'+  phone +'\r' ;
	 			deleteFlag = 0;
 			}else{
	 			var newVal = textareaElement.val() +  phone + '\r\n' ;
 			}
 			textareaElement.val(newVal);
 		}
 		
 	});

 	$(document).on('click','.segmentModal .clientRow .removeClient',function(e){
 		e.preventDefault();
 		e.stopPropagation();
 		var phone = $(this).parents('.clientRow').find('.phone').text();
 		var textareaElement = $('textarea[name="whatsappNos"]');
 		if (textareaElement.val().indexOf(phone) > -1){
			var val = textareaElement.val();
 			textareaElement.val(val.replace(phone , "").trim());
 			deleteFlag = 1;
 		}
 	});
 	
	$(".AddBTN").click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		$('input[name="status"]').val(1);
		var inputName = 'name_'+lang;

		var groupName = "";
		if($('select[name="group_id"]').val()){
			if($('select[name="group_id"]').val() != '@'){
				groupName+= $('select[name="group_id"] option:selected').text() + "<br>";
			}else{
				groupName+= $('input[name="name_'+lang+'"]').val() + "<br>";
			}
		}

		var sendTime = myNow;
		var message_type = $('select[name="message_type"]').val();
		if(message_type == 1){
			var message = $('textarea[name="messageText"]').val();
		}

		if($('input#radio2').is(':checked')){
			sendTime = $('input[name="date"]').val();
		}
		$('form.grpmsg').submit();
	});

})