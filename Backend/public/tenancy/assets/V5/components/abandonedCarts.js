$(function(){
	Dropzone.options.myAwesomeDropzone = false;
	Dropzone.autoDiscover = false;

	var dateID = $(document).find('#scheduledMsgs .reply .kt_dropzone_1').parents('.reply').data('id');
	var dropz = $(document).find('#scheduledMsgs .reply .kt_dropzone_1').dropzone({
	    url: myURL.split("?")[0] + "/uploadImage/"+dateID,
	    paramName: "file", // The name that will be used to transfer the file
	    maxFiles: 1,
	    // maxFilesize: 0.1, // MB
	    addRemoveLinks: true,
	    // previewTemplate: $('#uploadPreviewTemplate').html(),
	    accept: function(file, done) {
	        if (file.name == "justinbieber.jpg") {
	            done("Naha, you don't.");
	        } else {
	            done();
	        }
	    },
	    success:function(file,data){
	    	var dropzone = this;
	        if(data){
	            if(data.status.status != 1){
	                errorNotification(data.status.message);
					dropzone.removeFile(file);		            
				}
	        }
	    },
	});

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

	var lang = $('html').attr('lang');
	$('select[name="message_type"]').on('change',function(){
		var dateID = $(this).val();
		$('input[name="reply"]').val('');
		$('.reply[data-id="'+dateID+'"].hidden').removeClass('hidden');
		$('.reply[data-id="'+dateID+'"]').children('.hidden').removeClass('hidden');
		$('.reply[data-id="'+dateID+'"]').children('.row.hidden').removeClass('hidden');
		$('.reply[data-id="'+dateID+'"]').siblings('.reply').children('.row').addClass('hidden');
	});


	$('input[name="sending"]').on('change',function(){
		$("input[name='date']").toggleClass('hidden');
	});

	$('#datetimepicker').datetimepicker({
		format: 'YYYY-MM-DD HH:mm',
		stepping: 5,
	});

	$('#time').datetimepicker({
		format: 'HH',
		stepping: 5,
	});

 	$(document).on('change','select[name="clients"]',function(){
 		var clientsVal = $(this).val();
 		if(clientsVal.indexOf("@") != -1){  
		   $('select[name="clients"] option:not(.di)').prop('selected',true);
		   $('select[name="clients"] option.di').prop('selected',false);
		}
 	});

 	$(document).on('click','#resendModal .resendCarts',function(e){
		e.preventDefault();
		e.stopPropagation();
		
		var sendTime = 1;
		var message = $('textarea[name="body"]').val();

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
	        url: myURL.split("?")[0]+'/sendAbandoned',
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
 	
	$(".schedMSG").click(function(e){
		e.preventDefault();
		e.stopPropagation();
		$('#scheduledMsgs form.formPayment').submit();
	});

	$(".toggleBut").click(function() {
    	$(this).toggleClass("active");
    	var eventID = $(this).data('type');
    	var status = $(this).hasClass("active") ? 1 : 0;

    	$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'POST',
	        url: myURL.split("?")[0]+'/updateEvent',
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'id': eventID,
	            'status': status,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	                successNotification(data.status.message);
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
    });

    $('.mainSearch').on('click',function(e){
    	e.preventDefault();
    	e.stopPropagation();
    	$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    	$.ajax({
	        type: 'GET',
	        url: myURL.split("?")[0],
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'status': $('.formSearch select[name="status"]').val(),
	            'client': $('.formSearch select[name="client"]').val(),
	            'date': $('.formSearch input[name="date"]').val(),
	            'phone': $('.formSearch input[name="phone"]').val(),
	            'price': $('.formSearch input[name="price"]').val(),
	            'duration': $('.formSearch select[name="duration"]').val(),
	        },
	        success:function(data){
	            if(data.success == true){
	            	$('.carts').empty();
					$('.carts').html(data.html);
					$('#resendModal span.clno').html($('input[name="clientNo"]').val());
					setTimeout(function(){$('#resendModal').modal('show')},500);
	            }
	        },
	    });
    });

	$('.editEvent').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var id = $(this).data('type');
		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'GET',
	        url: myURL.split("?")[0]+'/getEvent',
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'id': id,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	            	$('#scheduledMsgs textarea[name="content"]').text('');
	            	$('#scheduledMsgs textarea[name="caption"]').text('');
	            	$('#scheduledMsgs textarea[name="body"]').text('');
	            	$('#scheduledMsgs input[name="event_id"]').val(id);
	            	$('#scheduledMsgs select[name="message_type"]').val(data.data.message_type).trigger("change");
	            	$('#scheduledMsgs input[name="time"]').val(data.data.time);
	            	$('#scheduledMsgs .reply').addClass('hidden');
	            	$('#scheduledMsgs .reply[data-id="'+data.data.message_type+'"]').removeClass('hidden');
	            	if(data.data.message_type == 1){
	            		$('#scheduledMsgs #my-preview').addClass('hidden');
	            		$('#scheduledMsgs textarea[name="content"]').text(data.data.message);
	            	}else if(data.data.message_type == 2){
	            		$('#scheduledMsgs #my-preview').removeClass('hidden');
	            		$('#scheduledMsgs #my-preview .dz-image img').attr('src',data.data.message);
	            		$('#scheduledMsgs #my-preview .dz-details .dz-size span strong').text(data.data.file_size);
	            		$('#scheduledMsgs #my-preview .dz-details .dz-filename span').text(data.data.file_name);
	            		$('#scheduledMsgs #my-preview .PhotoBTNS .my-gallery figure a').attr('href',data.data.message);
	            		$('#scheduledMsgs #my-preview .PhotoBTNS .my-gallery figure img').attr('src',data.data.message);

	            		$('#scheduledMsgs textarea[name="caption"]').text(data.data.caption);
	            	}else if(data.data.message_type == 3){
	            		$('#scheduledMsgs #my-preview').addClass('hidden');
	            		$('#scheduledMsgs input[name="title"]').val(data.data.bot_plus.title);
	            		$('#scheduledMsgs input[name="footer"]').val(data.data.bot_plus.footer);
	            		$('#scheduledMsgs textarea[name="body"]').text(data.data.bot_plus.body);
	            		$('#scheduledMsgs select[name="buttons"]').val(data.data.bot_plus.buttons).trigger("change");
	            		$.each(data.data.bot_plus.buttonsData,function(index,item){
	            			$('#scheduledMsgs .repy [name="btn_reply_'+item.id+'"],#scheduledMsgs .repy [name="btn_msg_'+item.id+'"]').addClass('hidden');
	            			$('#scheduledMsgs input[name="btn_text_'+item.id+'"]').val(item.text);
	            			$('#scheduledMsgs input[name="btn_msg_type_'+item.id+'"]').val(item.msg_type);
	            			if(item.reply_type == 1){
	            				$('#scheduledMsgs .repy [name="btn_reply_'+item.id+'"]').val(item.msg);	            				
	            				$('#scheduledMsgs .repy [name="btn_reply_'+item.id+'"]').removeClass('hidden');
	            			}else{
	            				$('#scheduledMsgs select[name="btn_reply_type_'+item.id+'"] option[value="'+item.reply_type+'"]').prop('selected',true).trigger('change');
	            				$('#scheduledMsgs .repy select[name="btn_msg_'+item.id+'"] option[value="'+item.msg+'"][data-type="'+item.msg_type+'"]').prop('selected',true).trigger('change');
	            				$('#scheduledMsgs .repy [name="btn_msg_'+item.id+'"]').removeClass('hidden');
	            			}
	            		});
	            	}
	                setTimeout(function(){$('#scheduledMsgs').modal('show');},500);
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });

	});
})