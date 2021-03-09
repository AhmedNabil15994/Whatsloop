$(function(){
	$('.myCard').on('click',function(){
		$($(this).data('toggle')).slideToggle(250);
	});
});	