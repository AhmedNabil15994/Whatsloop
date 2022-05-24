$(function(){
	Dropzone.options.myAwesomeDropzone = false;
	Dropzone.autoDiscover = false;

	$.each($('.reply .kt_dropzone_1'),function(index,item){
		var dateID = $(this).parents('.reply').data('id');
		var dropz = $(item).dropzone({
		    url: myURL + "/uploadImage/"+dateID,
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

 	$('#SubmitBTNss.AddBTNss').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();

	    $('input[name="status"]').val(1);
	    if(teles){
	        var phone =  tr.getNumber();
	        if (!tr.isValidNumber() && !$('.teles').parents('.row').hasClass('hidden') &&(
	                ($('input[name="vType"]').length && $('input[name="vType"]').val() == 2) || !$('input[name="vType"]').length)){
	            if(lang == 'en'){
	                errorNotification("This Phone Number Isn't Valid!");
	            }else{
	                errorNotification("هذا رقم الجوال غير موجود");
	            }
	        }else{
	            $('input.teles').val(phone);
	            if($(this).data('clicked') == 1){
		            $(this).parents('form').submit();    	
				}else{
		            $('.alert-modal').modal('show');
	            	$(this).data('clicked',1);
				}
	        }
	    }else{
	    	if($(this).data('clicked') == 1){
	            $(this).parents('form').submit();    	
			}else{
	            $('.alert-modal').modal('show');
	            $(this).data('clicked',1);
			}
	    }
	    
	});

 	$(document).on('click','.alert-modal .btn-info',function(e){
 		e.preventDefault();
 		$('.alert-modal').modal('hide');
 		setTimeout(function(){
	 		$('#tipsModal').modal('show');
 		},500);
 	});
 	
	$(".AddBTN").click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if (!$(".teles").intlTelInput("isValidNumber") && !$('.teles').parents('.row').hasClass('hidden')) {
		    if(lang == 'en'){
		        return errorNotification("This Phone Number Isn't Valid!");
		    }else{
		        return errorNotification("هذا رقم الجوال غير موجود");
		    }
		}

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
		var message = '';
		var message_type = $('select[name="message_type"]').val();
		if(message_type == 1){
			message = $('textarea[name="messageText"]').val();
		}else if(message_type == 2){
			message = $('input[name="message"]').val();
		}else if(message_type == 4){
			message = $('input[name="https_url"]').val();
		}else if(message_type == 5){
			message = $('input[name="whatsapp_no"]').val();
		}

		if($('input#radio2').is(':checked')){
			sendTime = $('input[name="date"]').val();
		}

		var myLength = 0;
		var arrDe = message.match(/.{1,140}/g);
		if(arrDe){
			myLength=arrDe.length;
		}

		var htmlVar = 	'<div class="row text-left">'+
							'<div class="col-4"> '+groupVar+' : </div>'+
							'<div class="col-8"> '+ groupName +' </div>'+
						'</div>'+
						'<div class="row text-left">'+
							'<div class="col-4"> '+senderVar+' : </div>'+
							'<div class="col-8"> '+$('span.pro-user-name.ml-1').text()+' </div>'+
						'</div>'+
						'<div class="row text-left">'+
							'<div class="col-4"> '+messageVar+' : </div>'+
							'<div class="col-8"> '+message+' </div>'+
						'</div>'+
						'<div class="row text-left">'+
							'<div class="col-4"> '+countVar+' : </div>'+
							'<div class="col-8"> '+myLength+' </div>'+
						'</div>' +
						'<div class="row text-left">'+
							'<div class="col-4"> '+sendTimeVar+' : </div>'+
							'<div class="col-8"> '+sendTime+' </div>'+
						'</div>';
		Swal.fire({
			title: titleVar,
			type:"info",
			html: htmlVar,
			showCloseButton:!0,
			showCancelButton:!0,
			confirmButtonClass:"btn btn-success",
			cancelButtonClass:"btn btn-cancel",
			confirmButtonText:'<i class="mdi mdi-send-outline"></i> '+sendVar,
			cancelButtonText:'<i class="mdi mdi-backburger"></i>'+backVar,
		}).then(function(result){
			if (result.value) {
				var phone =  $(".teles").intlTelInput("getNumber");
				$('.teles').val(phone);
				$('form.grpmsg').submit();
			}
		});
	});

})