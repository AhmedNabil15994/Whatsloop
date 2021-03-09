$(function(){
	$('.example-copy').on('click',function(){
		var $temp = $("<input>");
		var myText = $(this).parent('.col.text-right.mt-5').siblings('.col.code').children('.tab-content').find('.tab-pane.active').html();
	    $("body").append($temp);
	    $temp.val(myText).select();
	    document.execCommand("copy");
	    $temp.remove();	
	});

	$(document).on('click','.example-toggled',function(){
		if($(this).children('i.fa').hasClass('fa-eye')){
			$(this).children('i.fa').removeClass('fa-eye');
			$(this).children('i.fa').addClass('fa-eye-slash');
		}else{
			$(this).children('i.fa').removeClass('fa-eye-slash');
			$(this).children('i.fa').addClass('fa-eye');
		}
		$(this).parent('.col.text-right.mt-5').siblings('.col.code').children('.tab-content').slideToggle();
	});
});