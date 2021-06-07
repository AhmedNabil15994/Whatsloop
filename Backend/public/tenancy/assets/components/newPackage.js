$(function(){
	
	$('.tdPrice input[type="checkbox"]').on('change',function(e){
		e.stopPropagation();
		e.preventDefault();
		
		var price = $(this).data('area');
		var start_date = $('td.start_date').html();
		var duration_type = 1;
		
		if($(this).is(':checked')){
			if($(this).attr('class') == 'monthlyPack'){
				if($(this).parents('.tdPrice').find('input[type="checkbox"].yearlyPack').is(':checked')){
					// Add Monthly Data
					$(this).parents('.tdPrice').find('input[type="checkbox"].yearlyPack').prop('checked',false);
					$('span.mainPrices').html( parseInt($('span.mainPrices').html()) - parseInt($('.yearlyPack').data('area')));
					$('span.price').html( parseInt($('span.price').html()) - parseInt($('.yearlyPack').data('area')));
				}
				$('td.end_date').html(moment(start_date).add(1, 'months').format('YYYY-MM-DD'));
			}else{
				if($(this).parents('.tdPrice').find('input[type="checkbox"].monthlyPack').is(':checked')){
					// Add Yearly Data
					$(this).parents('.tdPrice').find('input[type="checkbox"].monthlyPack').prop('checked',false);
					$('span.mainPrices').html( parseInt($('span.mainPrices').html()) - parseInt($('.monthlyPack').data('area')));
					$('span.price').html( parseInt($('span.price').html()) - parseInt($('.monthlyPack').data('area')));
				}
				duration_type = 2;
				$('td.end_date').html(moment(start_date).add(12, 'months').format('YYYY-MM-DD'));
			}
			$('span.mainPrices').html( parseInt($('span.mainPrices').html()) + parseInt(price));
			$('span.price').html( parseInt($('span.price').html()) + parseInt(price));
		}else{
			$('span.mainPrices').html( parseInt($('span.mainPrices').html()) - parseInt(price));
			$('span.price').html( parseInt($('span.price').html()) - parseInt(price));
		}
		
		$(this).parents('tr').find('td.price_with_vat').html(price);
		$(this).parents('tr').attr('data-period',duration_type);
	});

	function addRow(type,id,title,price,period=null) {
		var start_date = $('tr[data-tabs="membership"]').find('td.start_date').text();
		var end_date = moment(start_date).add(1, 'months').format('YYYY-MM-DD');
		var myType = $('input[name="'+type+'"]').val();
		var duration_type = 1;
		if(period == 'year'){
			end_date = moment(start_date).add(12, 'months').format('YYYY-MM-DD');
			duration_type = 2;
		}

		if($('table.items tbody tr[data-tabs="'+type+'"][data-cols="'+id+'"]').length){
			$('table.items tbody tr[data-tabs="'+type+'"][data-cols="'+id+'"]').remove();
			$('span.mainPrices').html( parseInt($('span.mainPrices').html()) - parseInt(price));
			$('span.price').html( parseInt($('span.price').html()) - parseInt(price));
		}

		var newRow =    '<tr data-tabs="'+type+'" data-cols="'+id+'" data-period="'+duration_type+'">'+
                            '<td>'+
                                '<p class="m-0 d-inline-block align-middle font-16">'+
                                    '<a href="#" class="text-reset font-family-secondary">'+title+'</a><br>'+
                                    '<small class="mr-2"><b>'+$('tr[data-tabs="membership"]').find('small.typeText b').text()+':</b> '+ myType +' </small>'+
                                '</p>'+
                            '</td>'+
                            '<td class="tdPrice">'+price+'</td>'+
                            '<td>1</td>'+
                            '<td class="start_date">'+start_date+'</td>'+
                            '<td class="end_date">'+end_date+'</td>'+
                            '<td class="price_with_vat">'+price+'</td>'+
                            '<td>'+
                                '<a href="javascript:void(0);" class="action-icon"> <i class="mdi mdi-delete"></i></a>'+
                            '</td>'+
                        '</tr>';
        $('table.items tbody').append(newRow);
        $('span.mainPrices').html( parseInt($('span.mainPrices').html()) + parseInt(price));
		$('span.price').html( parseInt($('span.price').html()) + parseInt(price));
	}

	$('a.extra').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		var id = $(this).data('cols');
		var price = $(this).data('area');
		
		addRow('extra_quota',id,$(this).find('p.details').text(),price,'month');
	});

	$('.mainCol input[type="checkbox"]').on('change',function(e){
		e.stopPropagation();
		e.preventDefault();

		var price = $(this).data('area');

		if($(this).is(':checked')){
			if($(this).attr('class') == 'monthly'){
				if($(this).parents('.mainCol').find('input[type="checkbox"].yearly').is(':checked')){
					$(this).parents('.mainCol').find('input[type="checkbox"].yearly').prop('checked',false);
				}
				addRow('addon',$(this).data('cols'),$(this).parents('.mainForm').find('h3.card-title').text(),price,'month');
			}else{
				if($(this).parents('.mainCol').find('input[type="checkbox"].monthly').is(':checked')){
					$(this).parents('.mainCol').find('input[type="checkbox"].monthly').prop('checked',false);
				}
				addRow('addon',$(this).data('cols'),$(this).parents('.mainForm').find('h3.card-title').text(),price,'year');
			}
		}
	});

	$(document).on('click','.action-icon',function(e){
		e.preventDefault();
		e.stopPropagation();
		var oldPrice = $(this).parents('tr').children('td.price_with_vat').html();
		var rowType   = $(this).parents('tr').data('tabs');
		$(this).parents('tr').remove();
		$('span.mainPrices').html( parseInt($('span.mainPrices').html()) - parseInt(oldPrice));
		$('span.price').html( parseInt($('span.price').html()) - parseInt(oldPrice));
		if(rowType == 'membership'){
			location.reload();
		}
	});


	$('a.checkout').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    
	    var data = [];
	    $.each($('.items tbody tr'),function(index,item){
	    	data.push([
	    		$(item).data('cols'),
	    		$(item).data('tabs'),
	    		$(item).find('td.tdDets .text-reset.font-family-secondary').text(),
	    		$(item).data('period'),
	    		$(item).find('td.start_date').html(),
	    		$(item).find('td.end_date').html(),
	    		$(item).find('td.price_with_vat').html(),
	    	]);
	    });

	    var totals = [
        	$('span.mainPrices').html(),
        	$('span.discount').html(),
        	$('span.tax').html(),
        	$('span.price').html(),
        ];

	    $('input[name="data"]').val(JSON.stringify(data));
	    $('input[name="totals"]').val(JSON.stringify(totals));
	    
	    if(totals.length && data.length){
	    	$('.payments').submit();
	    }

	});

});