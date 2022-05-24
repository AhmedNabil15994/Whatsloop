$(function(){
	$('select[name="type"]').change(function(){
		if($(this).val() == 1){
			$('.form2').addClass('hidden');
			$('.form1').removeClass('hidden');
		}else{
			$('.form1').addClass('hidden');
			$('.form2').removeClass('hidden');
			$('textarea[name="body"]').val($('textarea[name="content_ar"]').val());
		}
	});

	var lang = $('html').attr('lang');
	var text = 'Text';
	var newReply = "New Reply";
	var botMsg = "Bot Message";
	var msgContent = "Message Content";
	var changeOrderStatusTo = 'Change Order Status To';
	var choose = "Choose";
	if(lang == 'ar'){
		text = 'النص';
		newReply = 'رسالة جديدة';
		botMsg = "رسالة بوت";
		msgContent = "نص الرسالة";
		choose = "حدد اختيارك";
		changeOrderStatusTo = 'تغيير حالة الطلب لـ';
	}

	$('select[name="buttons"]').on('change',function(e) {
		e.preventDefault();
		e.stopPropagation();

		var buttons = $(this).val();
		if( buttons && buttons > 0 && buttons <= 10){
			var oldItems = $('.buts .row.mains').length;
			var result = buttons-oldItems;
			if(result > 0){
				for (var i = 0; i < result; i++) {
					appendButtons(i+1+oldItems);
				}
			}else if(result<0){
				result = Math.abs(result);
				for (var i = 0; i < result; i++) {
					$('.buts').children('.row.mains').last().remove()
				}
			}
			
		}
	});

	function appendButtons(itemIndex) {
		var buttonsData = "Button "+itemIndex+" Data ";
		if(lang == 'ar'){
			buttonsData = "بيانات الزر "+itemIndex;
		}
		var myString =  "<div class='row mains'>"+
							"<div class='col-md-3'>"+
                                "<label class='titleLabel'>"+buttonsData+":</label>"+
							"</div>"+
							"<div class='col-md-9'>"+
								"<div class='row'>"+
									"<div class='col-md-4'>" +
										"<input type='text' name='btn_text_"+itemIndex+"' placeholder='"+text+"'>"+
									" </div>"+
									"<div class='col-md-4'>" +
										"<select data-toggle='select2' class='reply_types' name='btn_reply_type_"+itemIndex+"'>"+
											"<option value='1' selected>"+newReply+"</option>"+
											"<option value='2'>"+botMsg+"</option>"+
											"<option value='3'>"+changeOrderStatusTo+"</option>"+
										"</select>"+
									" </div>"+
									"<div class='col-md-4 repy'>" +
										"<textarea name='btn_reply_"+itemIndex+"' placeholder='"+msgContent+"'></textarea>"+
										"<select class='hidden dets select1s' name='btn_msg_"+itemIndex+"'>"+
											"<option value='' selected>"+choose+"</option>"+
											$('select[name="bots"]').html()+
										"</select>"+
										"<select class='hidden dets select2s' name='btn_msgs_"+itemIndex+"'>"+
											"<option value='' selected>"+choose+"</option>"+
											$('select[name="statuses"]').html()+
										"</select>"+
										"<input type='hidden' name='btn_msg_type_"+itemIndex+"' value=''>"+
									" </div>"+
								"</div>"+
							"</div>"+
						"</div>";
		$('.buts').append(myString);
		$('.buts .row select[data-toggle="select2"]').select2();
	}

	$(document).on('change','.mains select.reply_types',function(){
		var itemValue = $(this).val();
		if(itemValue == 1){
			$(this).parents('.mains').find('.repy').children('textarea').removeClass('hidden');
			$(this).parents('.mains').find('.repy').children('select:not(.hidden)').select2('destroy');
			$(this).parents('.mains').find('.repy').children('select').addClass('hidden');
		}else if(itemValue == 2){
			$(this).parents('.mains').find('.repy').children('textarea').addClass('hidden');
			$(this).parents('.mains').find('.repy').children('select:not(.hidden)').select2('destroy');
			$(this).parents('.mains').find('.repy').children('select.select2s').addClass('hidden');
			$(this).parents('.mains').find('.repy').children('select.select1s').removeClass('hidden');
			$(this).parents('.mains').find('.repy').children('select.select1s').select2();
		}else if(itemValue == 3){
			$(this).parents('.mains').find('.repy').children('textarea').addClass('hidden');
			$(this).parents('.mains').find('.repy').children('select:not(.hidden)').select2('destroy');
			$(this).parents('.mains').find('.repy').children('select.select1s').addClass('hidden');
			$(this).parents('.mains').find('.repy').children('select.select2s').select2();
			$(this).parents('.mains').find('.repy').children('select.select2s').removeClass('hidden');
		}
	});

	$(document).on('change','select.dets',function(){
		var itemValue = $(this).children("option:selected").data('type');
		if(itemValue){
			$(this).siblings("input[type='hidden']").val(itemValue);
		}
	});
});