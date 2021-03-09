$(function(){
	$('.dropdown-item.channel-item').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var channel = $(this).children('span').data('area');
		var _token = $('meta[name="csrf-token"]').attr('content');

		$.ajax({
			url : '/changeChannel',
			type : 'POST',
			data:{
				"_token" : _token,
				"channel": channel,
			},
			datatype: "json",
			complete:function(data){
				window.location.reload(true);
			}

		});
	});

	$('.theme input').on('change',function(e){
		e.preventDefault();
		e.stopPropagation();

		var type = $(this).attr('name');
		var value = $(this).val();
		var _token = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			url : '/changeTheme',
			type : 'POST',
			data:{
				"_token" : _token,
				'type' : type,
				'value' : value,
			},
			datatype: "json",
			complete:function(data){
				window.location.reload(true);
			}

		});
	});

	$('.theme #resetBtn').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var _token = $('meta[name="csrf-token"]').attr('content');
		$.ajax({
			url : '/changeTheme/default',
			type : 'POST',
			data:{
				"_token" : _token,
			},
			datatype: "json",
			complete:function(data){
				window.location.reload(true);
			}

		});
	});

	$('.SelectAllCheckBox').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		$('input[type="checkbox"]').attr('checked','checked');
	});
	$('.UnSelectAllCheckBox').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		$('input[type="checkbox"]').attr('checked',false);
	});
});
