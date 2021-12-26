$(function(){
	$('a.screen').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'get',
	        url: myURL+'/screenshot',
	        data:{'_token': $('meta[name="csrf-token"]').attr('content'),},
	        success:function(data){
	            if(data.image){
	            	$('#full-width-modal img').attr('src',data.image);
	            	$('#full-width-modal').modal('show');
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

	$(document).on('change','.custom-switch-input',function(){
        var key = $(this).data('area');
        var status = 0;
        if($(this).is(':checked')){
            status = 1;
        }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: myURL+'/updateChannelSettings',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'key' : key,
                'value': status,
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
});	