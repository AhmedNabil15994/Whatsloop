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
});	