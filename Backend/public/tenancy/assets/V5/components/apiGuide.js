$(function(){
	$('.example-copy').on('click',function(){
		var $temp = $("<input>");
		var myText = $(this).parent('.col.text-right.mt-5').siblings('.col.code').find('.tab-pane.active').text();
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
		$(this).parent('.col.text-right.mt-5').siblings('.col.code').find('.tab-pane.active').slideToggle();
	});

	$('.card-header').on('click',function(){
		$(this).parents('.row').siblings('.row').find('.collapse.show').removeClass('show');
		$(this).siblings('.collapse').toggleClass('show');
	});
});