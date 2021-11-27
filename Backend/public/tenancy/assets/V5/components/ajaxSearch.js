$(function(){


	if($('.changeDesign').length){
	    $('.changeDesign').on('click',function (e) {
	        e.preventDefault();
	        e.stopPropagation();
	        $('.sa.hidden').removeClass('hidden').siblings('.sa').addClass('hidden');
	    });
	}

	var pageUrl = $('form.searchForm').attr('action');
	
	$('input[name="keyword"]').on('keyup',function () {
		var keyword = $(this).val();
		var recordNumber = $('input[name="records"]').val();

		fetchData(recordNumber,keyword,pageUrl);
	});

	$('input[name="records"]').on('keyup',function(){
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
	            	if($('.numbers').length){
	            		$('.numbers').remove();
	                	$('.sa.cl').append(data.html);
	            	}
	                
	                if($('.users').length){
	            		$('.data').empty();
	                	$('.data').append(data.html);
	            	}
	            }
	        },
	    });
	}
	
});