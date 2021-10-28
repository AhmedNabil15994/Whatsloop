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
	var className = 'mr-4';
	var typeText = 'Type';
	var selectMemebership = "You Have to select one membership before going to payment";
	if(lang == 'ar'){
		add = 'أضف إلى السلة';
		added = 'تمت الإضافة إلى السلة';
		remove = "حذف";
	    className = 'ml-4';
		typeText = 'النوع';
	 	selectMemebership = "عفوا يجب عليك اختيار الباقة قبل التوجه الى الدفع";
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
								'<a class="tx-24 mg-t-10"><i class="fe fe-minus-circle"></i></a>'+
								'<input type="text" class="form-control form-control-sm text-center wd-50 mg-x-5" max="5" value="1" min="1">'+
								'<a class="tx-24 mg-t-10"><i class="fe fe-plus-circle"></i></a>'+
							'</div>';
		}

		$('.cart.card .card-body.'+classType+'[data-cols="'+classID+'"]').remove();
		var elemString = '<div class="card-body '+classType+'" '+'data-cols="'+classID+'"'+'>'+
							'<div class="media">'+
								'<div class="card-aside-img">'+
									'<img src="'+$(this).parent('.card-body').siblings('img').attr('src')+'" alt="img" class="wd-100-f ht-100 '+className+'">'+
									extraString +
								'</div>'+
								'<div class="media-body">'+
									'<div class="card-item-desc mt-0">'+
										'<h6 class="font-weight-semibold mt-0 text-uppercase">'+ ( classType == 'extra_quota' ? $(this).siblings('small.text-muted').text() : $(this).siblings('.h6.text-uppercase').text() ) +'</h6>'+
										'<small class="text-muted tx-13"></small>'+
										'<p class="tx-13 mg-b-5"><b>'+typeText+':</b> '+ $(this).siblings('.d-flex').find('span.text-muted').text() +' </p>'+
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
		calcPrices($(this).siblings('.d-block').find('.h5:not(.d-hidden)').html(),$(this).siblings('.d-block').find('.h5:not(.d-hidden)').data('tabs'),'plus',classType);

		$(this).empty();
		$(this).html('<i class="fe fe-check mr-1"></i>' + added);
		$(this).removeClass('add');
		$(this).addClass('added');
	});


	$(document).on('click','.fe.fe-plus-circle',function(){
		var counterElem = $(this).parent('a').siblings('input.form-control');
		var oldCounterValue = counterElem.val();
		var newCounterValue = parseInt(parseInt(oldCounterValue) + parseInt(1));
		if(newCounterValue <= counterElem.attr('max')){
			counterElem.val(newCounterValue);
			var currentPrice = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').text();
			var currentPriceWithVat = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').data('tabs');
			calcPrices(parseInt(currentPrice),parseInt(currentPriceWithVat),'plus');
		}
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


	function calcPrices(itemPrice,itemAfterVat,operator,classType=null){
		var oldGrandTotal = $('span.grandTotal').text();
		var oldEstimatedTax = $('span.estimatedTax').text();
		var oldTotal = $('span.total').text();
		var userCredits = $('input[name="background"]').val();
		
		if(classType == 'membership'){
			if(operator == 'minus'){
				oldGrandTotal = parseInt(itemPrice) - parseFloat(userCredits);
				oldTotal = parseInt(itemAfterVat) - parseFloat(userCredits);
			}else{
				oldGrandTotal = parseInt(itemPrice) - parseFloat(userCredits);
				oldTotal = parseInt(itemAfterVat) - parseFloat(userCredits);
			}
		}else{
			if(operator == 'minus'){
				oldGrandTotal = parseInt(oldGrandTotal) - parseInt(itemPrice);
				oldTotal = parseInt(oldTotal) - parseInt(itemAfterVat);
			}else{
				oldGrandTotal = parseInt(oldGrandTotal) + parseInt(itemPrice);
				oldTotal = parseInt(oldTotal) + parseInt(itemAfterVat);
			}
		}
		calcTaxes(oldTotal);
	};

	function calcAllPrices(){
		var oldGrandTotal = 0;
		var oldEstimatedTax = 0;
		var oldTotal = 0;
		var userCredits = $('input[name="background"]').val();
		$.each($('.cart .card-body'),function(index,item){
			var currentPrice = $(item).find('.h5:not(.d-hidden)').text();
			var currentPriceWithVat = $(item).find('.h5:not(.d-hidden)').data('tabs');
			if($(item).hasClass('extra_quota')){
				oldGrandTotal = (parseInt(oldGrandTotal) + (parseInt(currentPrice) * $(item).find('input[type="text"]').val())) - parseFloat(userCredits);
				oldTotal = (parseInt(oldTotal) + (parseInt(currentPriceWithVat) * $(item).find('input[type="text"]').val())) - parseFloat(userCredits);
			}else {
				oldGrandTotal = (parseInt(oldGrandTotal) + parseInt(currentPrice)) - parseFloat(userCredits);
				oldTotal = (parseInt(oldTotal) + parseInt(currentPriceWithVat)) - parseFloat(userCredits);
			}
		});

		calcTaxes(oldTotal);
	}

	function calcTaxes(oldGrandTotal){
		var oldTotal = oldGrandTotal.toFixed(2);
        var estimatedTax = oldTotal * (15/115);
        
        estimatedTax = estimatedTax.toFixed(2);
		oldEstimatedTax = parseFloat(oldGrandTotal) - parseFloat(estimatedTax);
		oldEstimatedTax = oldEstimatedTax.toFixed(2);

		$('span.grandTotal').text(oldEstimatedTax);
		$('span.estimatedTax').text(estimatedTax);
		$('span.total').text(oldTotal);
	}

	$('button.checkout').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    var start_date = $('input[name="start_date"]').val();
	    var data = [];
	    $.each($('.cart .card-body'),function(index,item){
	    	var end_date = moment(start_date).add( ( $('select[name="duration_type"]').val() == 1 ? 1 : 12 ) , 'months').format('YYYY-MM-DD');
	    	data.push([
	    		$(item).data('cols'), // id
	    		$(item).attr('class').replace('card-body ',''), // type
	    		$(item).find('.text-uppercase').text(), // name
	    		parseInt($('select[name="duration_type"]').val()), // period
	    		start_date, // start_date,
	    		end_date, // end_date
	    		$(item).find('.h5:not(.d-hidden)').data('tabs'), // total
	    		($(item).find('input[type="text"]').length ? parseInt($(item).find('input[type="text"]').val()) : 1), // qunatity
	    	]);
	    });

	    var totals = [
        	$('span.grandTotal').html(),
        	0, // discount
        	$('span.estimatedTax').html(),
        	$('span.total').html(),
        ];

	    $('input[name="data"]').val(JSON.stringify(data));
	    $('input[name="totals"]').val(JSON.stringify(totals));
	    if(totals.length && data.length){
	    	$('.payments').submit();
	    }

	});

});