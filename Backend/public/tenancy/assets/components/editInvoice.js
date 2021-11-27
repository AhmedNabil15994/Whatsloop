$(function(){
	$('.action-icon').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var price = $(this).parent('td').siblings('td.tdPrice').html();
		$(this).parents('tr').remove();
		$('span.mainPrices').html($('span.mainPrices').html() - price);
		$('span.price').html($('span.price').html() - price);
		$('input[name="total"]').val($('input[name="total"]').val() - price);
	});

	$('.AddBTNz').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		
		var items = [];
		$.each($('tr.mainRow'),function(index,item){
			items.push($(item).find('td a.action-icon').data('area'));
		});

		$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'POST',
	        url: $('form.form-horizontal').attr('action'),
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'client_id': $('select[name="client_id"]').val(),
	            'status': $('select[name="status"]').val(),
	            'payment_method': $('select[name="payment_method"]').val(),
	            'due_date': $('input[name="due_date"]').val(),
	            'total': $('input[name="total"]').val(),
	            'notes': $('textarea[name="notes"]').val(),
	            'items': items,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	                successNotification(data.status.message);
	                location.reload();
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

});