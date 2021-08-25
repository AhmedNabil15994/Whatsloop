$(function(){
	var lang = $('html').attr('lang');

	var monthlyHidden = '';
	var yearlyHidden = 'd-hidden';
	$('select[name="duration_type"]').on('change',function(){
		if($(this).val() == 2){
			monthlyHidden = 'd-hidden';
			yearlyHidden = '';
		}else{
			monthlyHidden = '';
			yearlyHidden = 'd-hidden';
		}
		$('.product-card .yearly,.media-body .yearly').toggleClass('d-hidden');
		$('.product-card .monthly,.media-body .monthly').toggleClass('d-hidden');

		calcAllPrices();
	});

	var add = 'Add To Cart';
	var added = 'Added To Cart';
	var remove = "Remove";
	if(lang == 'ar'){
		add = 'أضف إلى السلة';
		added = 'تمت الإضافة إلى السلة';
		remove = "حذف";
	}

	$(document).on('click','a.rmv',function(e){
		e.preventDefault();
		e.stopPropagation();
		var classType = $(this).parents('.card-body').attr('class').replace('card-body ','');
		var classID = $(this).parents('.card-body').data('cols');
		$(this).parents('.card-body').remove();

		// Decrease Cart Element 
		var count = $('span.cartCount').html();
		$('span.cartCount').html(parseInt(parseInt(count) - parseInt(1)));

		var buttonElem = $('.product-card').find('.cartButton[data-area="'+classType+'"][data-cols="'+classID+'"]');
		buttonElem.empty();
		buttonElem.html('<i class="fe fe-shopping-cart mr-1"></i>' + add);
		buttonElem.removeClass('added');
		buttonElem.addClass('add');

		// ReCalculate Prices
		var currentPrice = $(this).siblings('.d-flex').find('.h5:not(.d-hidden)').text();
		var currentPriceWithVat = $(this).siblings('.d-flex').find('.h5:not(.d-hidden)').data('tabs');
		calcPrices(parseInt(currentPrice),parseInt(currentPriceWithVat),'minus');
	});

	$(document).on('click','.cartButton.added',function (e){
		e.preventDefault();
		e.stopPropagation();

		// Remove Element From Cart
		var classType = $(this).data('area');
		var classID = $(this).data('cols');
		$('.card-body.'+classType+'[data-cols="'+classID+'"]').remove();

		// Decrease Cart Element 
		var count = $('span.cartCount').html();
		$('span.cartCount').html(parseInt(parseInt(count) - parseInt(1)));

		// ReCalculate Prices
		calcPrices($(this).siblings('.d-block').find('.h5:not(.d-hidden)').html(),$(this).siblings('.d-block').find('.h5:not(.d-hidden)').data('tabs'),'minus');

		$(this).empty();
		$(this).html('<i class="fe fe-shopping-cart mr-1"></i>' + add);
		$(this).removeClass('added');
		$(this).addClass('add');
	});

	$(document).on('click','.cartButton.add',function (e){
		e.preventDefault();
		e.stopPropagation();
		
		// Add Element From Cart
		var classType = $(this).data('area');
		var classID = $(this).data('cols');

		var extraString = '';
		if(classType == 'membership' && $('.card-body.membership a.rmv').length ){
			$('.card-body.membership a.rmv')[0].click();
		}else if(classType == 'extra_quota'){
			extraString =   '<div class="d-flex mg-t-10">'+
								'<a class="tx-24 mg-t-5"><i class="fe fe-minus-circle"></i></a>'+
								'<input type="text" class="form-control form-control-sm text-center wd-50 mg-x-5" value="1" min="1">'+
								'<a class="tx-24 mg-t-5"><i class="fe fe-plus-circle"></i></a>'+
							'</div>';
		}



		$('.cart.card .card-body.'+classType+'[data-cols="'+classID+'"]').remove();
		var elemString = '<div class="card-body '+classType+'" '+'data-cols="'+classID+'"'+'>'+
							'<div class="media">'+
								'<div class="card-aside-img">'+
									'<img src="'+$(this).parent('.card-body').siblings('img').attr('src')+'" alt="img" class="wd-100-f ht-100 mr-4">'+
									extraString +
								'</div>'+
								'<div class="media-body">'+
									'<div class="card-item-desc mt-0">'+
										'<h6 class="font-weight-semibold mt-0 text-uppercase">'+ ( classType == 'extra_quota' ? $(this).siblings('small.text-muted').text() : $(this).siblings('.h6.text-uppercase').text() ) +'</h6>'+
										'<small class="text-muted tx-13"></small>'+
										'<p class="tx-13 mg-b-5"><b>Type:</b> '+ $(this).siblings('.d-flex').find('span.text-muted').text() +' </p>'+
										'<div class="d-flex">'+
											'<h4 class="h5 w-50 font-weight-bold text-danger monthly '+monthlyHidden+'" data-tabs="'+$(this).siblings('.d-block').find('.monthly').data('tabs')+'">'+ $(this).siblings('.d-block').find('.monthly').html() +'</h4>'+
											'<h4 class="h5 w-50 font-weight-bold text-danger yearly '+yearlyHidden+'" data-tabs="'+$(this).siblings('.d-block').find('.yearly').data('tabs')+'">'+$(this).siblings('.d-block').find('.yearly').html() +'</h4>'+
										'</div>'+
										'<a class="tx-gray-900 tx-uppercase font-weight-bold rmv" href="#">'+ remove +'</a>' +
									'</div>'+
								'</div>'+
							'</div>'+
						'</div>';
		if(classType == 'membership'){
			$('form.payments').after(elemString);
		}else{
			$('.cart.card').append(elemString);
		}

		// Increase Cart Element 
		var count = $('span.cartCount').html();
		$('span.cartCount').html(parseInt(parseInt(count) + parseInt(1)));

		// ReCalculate Prices
		calcPrices($(this).siblings('.d-block').find('.h5:not(.d-hidden)').html(),$(this).siblings('.d-block').find('.h5:not(.d-hidden)').data('tabs'),'plus');

		$(this).empty();
		$(this).html('<i class="fe fe-check mr-1"></i>' + added);
		$(this).removeClass('add');
		$(this).addClass('added');
	});


	$(document).on('click','.fe.fe-plus-circle',function(){
		var counterElem = $(this).parent('a').siblings('input.form-control');
		var oldCounterValue = counterElem.val();
		var newCounterValue = parseInt(parseInt(oldCounterValue) + parseInt(1));
		counterElem.val(newCounterValue);

		var currentPrice = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').text();
		var currentPriceWithVat = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').data('tabs');
		calcPrices(parseInt(currentPrice),parseInt(currentPriceWithVat),'plus');
	});

	$(document).on('click','.fe.fe-minus-circle',function(){
		var counterElem = $(this).parent('a').siblings('input.form-control');
		var oldCounterValue = counterElem.val();
		var counterMaxValue = 0;
		var newCounterValue = parseInt(parseInt(oldCounterValue) - parseInt(1));
		if(newCounterValue > counterMaxValue){
			counterElem.val(newCounterValue);
			var currentPrice = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').text();
			var currentPriceWithVat = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').data('tabs');
			calcPrices(parseInt(currentPrice),parseInt(currentPriceWithVat),'minus');
		}
	});


	function calcPrices(itemPrice,itemAfterVat,operator){
		var oldGrandTotal = $('span.grandTotal').text();
		var oldEstimatedTax = $('span.estimatedTax').text();
		var oldTotal = $('p.total').text();

		if(operator == 'minus'){
			oldGrandTotal = parseInt(oldGrandTotal) - parseInt(itemPrice);
			oldTotal = parseInt(oldTotal) - parseInt(itemAfterVat);
		}else{
			oldGrandTotal = parseInt(oldGrandTotal) + parseInt(itemPrice);
			oldTotal = parseInt(oldTotal) + parseInt(itemAfterVat);
		}
		oldEstimatedTax = parseInt(oldTotal) - parseInt(oldGrandTotal);

		$('span.grandTotal').text(oldGrandTotal);
		$('span.estimatedTax').text(oldEstimatedTax);
		$('p.total').text(oldTotal);
	};

	function calcAllPrices(){
		var oldGrandTotal = 0;
		var oldEstimatedTax = 0;
		var oldTotal = 0;
		$.each($('.cart .card-body'),function(index,item){
			var currentPrice = $(item).find('.h5:not(.d-hidden)').text();
			var currentPriceWithVat = $(item).find('.h5:not(.d-hidden)').data('tabs');
			if($(item).hasClass('extra_quota')){
				oldGrandTotal = parseInt(oldGrandTotal) + parseInt(currentPrice) * $(item).find('input[type="text"]').val();
				oldTotal = parseInt(oldTotal) + parseInt(currentPriceWithVat) * $(item).find('input[type="text"]').val();
			}else {
				oldGrandTotal = parseInt(oldGrandTotal) + parseInt(currentPrice);
				oldTotal = parseInt(oldTotal) + parseInt(currentPriceWithVat);
			}
		});

		oldEstimatedTax = parseInt(oldTotal) - parseInt(oldGrandTotal);

		$('span.grandTotal').text(oldGrandTotal);
		$('span.estimatedTax').text(oldEstimatedTax);
		$('p.total').text(oldTotal);
	}



	// Old Scripts
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


	$('button.checkout').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    var start_date = moment().format('YYYY-MM-DD')
	    var data = [];
	    $.each($('.cart .card-body'),function(index,item){
	    	data.push([
	    		$(item).data('cols'), // id
	    		$(item).attr('class').replace('card-body ',''), // type
	    		$(item).find('.text-uppercase').text(), // name
	    		parseInt($('select[name="duration_type"]').val()), // period
	    		start_date, // start_date,
	    		moment(start_date).add( ( $('select[name="duration_type"]') == 1 ? 1 : 12 ) , 'months').format('YYYY-MM-DD'), // end_date
	    		$(item).find('.h5:not(.d-hidden)').data('tabs'), // total
	    		($(item).find('input[type="text"]').length ? parseInt($(item).find('input[type="text"]').val()) : 1), // qunatity
	    	]);
	    });

	    var totals = [
        	$('span.grandTotal').html(),
        	0, // discount
        	$('span.estimatedTax').html(),
        	$('p.total').html(),
        ];

	    $('input[name="data"]').val(JSON.stringify(data));
	    $('input[name="totals"]').val(JSON.stringify(totals));
	    console.log(data);
	    if(totals.length && data.length){
	    	// $('.payments').submit();
	    }

	});

	$('input[name="billingOptions"]').on('change',function(){
		if($(this).is(":checked")){
			$('input[name="payType"]').val($(this).data('area'));
		}
	});

});