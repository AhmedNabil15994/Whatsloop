$(function(){
	var pageUrl = $('form.card-body').attr('action');
	
	$('input[name="keyword"]').on('keyup',function () {
		var keyword = $(this).val();
		var recordNumber = $('select[name="records"]').val();

		fetchData(recordNumber,keyword,pageUrl);
	});

	$('select[name="records"]').on('change',function(){
		var recordNumber = $(this).val();
	    var keyword = $('input[name="keyword"]').val();

		fetchData(recordNumber,keyword,pageUrl);
	});

	function fetchData(recordNumber,keyword,pageUrl){
		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'GET',
	        url: pageUrl,
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'recordNumber': recordNumber,
	            'keyword': keyword,
	        },
	        success:function(data){
	            if(data.success  == true){
	                $('.row.data').remove();
	            	$('.row.pagin').remove();
	            	$(data.html).insertAfter('.extraSearch')
	            }
	        },
	    });
	}
	
});