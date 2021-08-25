$(function(){
	$('.lang-item').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var locale = $(this).data('next-area');
		var _token = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
			url : '/changeLang',
			type : 'POST',
			data:{
				"_token" : _token,
				"locale": locale,
			},
			datatype: "json",
			complete:function(data){
				window.location.reload(true);
			}

		});
	});
});
