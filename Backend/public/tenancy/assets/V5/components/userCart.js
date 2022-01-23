$(function(){
	var lang = $('html').attr('lang');

	var monthlyHidden = '';
	var yearlyHidden = 'd-hidden';
	$('.btnsTabs li').on('click',function(){
		if($(this).attr('id') == 'tab1'){
			$('select[name="duration_type"]').val(1).selectmenu("refresh").trigger("selectmenuselect");
		}else{
			$('select[name="duration_type"]').val(2).selectmenu("refresh").trigger("selectmenuselect");
		}
		calcAllPrices();
		$('.yearly,.monthly').toggleClass('d-hidden');
	});

	$('select[name="duration_type"]').on('selectmenuchange', function() {
		if($(this).val() == 2){
			monthlyHidden = 'd-hidden';
			yearlyHidden = '';
		}else{
			monthlyHidden = '';
			yearlyHidden = 'd-hidden';
		}
		$('li#tab'+$(this).val()).trigger('click');
	});

	var add = 'Add To Cart';
	var added = 'Added To Cart';
	var remove = "Remove";
	var className = 'mr-4';
	var typeText = 'Type';
	var butIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="17.121" height="17.414" viewBox="0 0 17.121 17.414"><g id="Group_1283" data-name="Group 1283" transform="translate(1.414 0.707)"><path id="Path_891" data-name="Path 891" d="M1409,3149l-8,8,8,8" transform="translate(-1401 -3149)" fill="none" stroke="#fff" stroke-width="2"></path><path id="Path_892" data-name="Path 892" d="M1409,3149l-8,8,8,8" transform="translate(-1394 -3149)" fill="none" stroke="#fff" stroke-width="2" opacity="0.6"></path></g></svg>';
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
		var classType = $(this).parents('.card-body.col-lg-12').attr('class').replace('col-sm-12 col-lg-12 card-body ','');
		var classID = $(this).parents('.card-body.col-lg-12').data('cols');
		$(this).parents('.card-body').remove();

		// Decrease Cart Element 
		var count = $('span.cartCount').html();
		$('span.cartCount').html(parseInt(parseInt(count) - parseInt(1)));

		var buttonElem = $('.item-parent').find('.cartButton[data-area="'+classType+'"][data-cols="'+classID+'"]');
		buttonElem.empty();
		buttonElem.html(butIcon + add);
		buttonElem.removeClass('added');
		buttonElem.removeClass('active');
		buttonElem.addClass('add');

		// ReCalculate Prices
		var currentPrice = $(this).siblings('.card-text-2:not(.d-hidden)').text();
		var currentPriceWithVat = $(this).siblings('.card-text-2:not(.d-hidden)').data('tabs');
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
		if(classType == 'membership'){
			itemPrice = $(this).parents('.quta-card').children('.quta-info').find('.price:not(.d-hidden)').text();
			itemPrice = parseInt(itemPrice);
			itemAfterVat = $(this).parents('.quta-card').children('.quta-info').find('.price:not(.d-hidden)').data('tabs'); 
		}else{
			itemPrice = $(this).parents('.card-body').find('.price:not(.d-hidden)').text();
			itemPrice = parseInt(itemPrice);
			itemAfterVat = $(this).parents('.card-body').find('.price:not(.d-hidden)').data('tabs'); 
		}
		calcPrices(itemPrice,itemAfterVat,'minus');

		$(this).empty();
		$(this).html(butIcon + add);
		$(this).removeClass('added');
		$(this).removeClass('active');
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
			extraString =   '';
		}

		var elementTitle = '';
		var prodImage = '';
		var monthly = '';
		var yearly = '';
		if(classType == 'membership'){
			prodImage =  'tenancy/assets/V5/images/sell.png';
			elementTitle = $(this).parent('.card-body').find('.card-title').text();
			monthly = $(this).parents('.quta-card').children('.quta-info').find('.price.monthly');
			yearly = $(this).parents('.quta-card').children('.quta-info').find('.price.yearly');
			itemPrice = $(this).parents('.quta-card').children('.quta-info').find('.price:not(.d-hidden)').text();
			itemPrice = parseInt(itemPrice);
			itemAfterVat = $(this).parents('.quta-card').children('.quta-info').find('.price:not(.d-hidden)').data('tabs'); 
		}else{
			prodImage = $(this).parent('.card-body').siblings('.card-img').children('img').attr('src');
			elementTitle = $(this).parents('.card-body').find('.card-title').text();
			monthly = $(this).parents('.card-body').find('.price.monthly');
			yearly = $(this).parents('.card-body').find('.price.yearly');
			itemPrice = $(this).parents('.card-body').find('.price:not(.d-hidden)').text();
			itemPrice = parseInt(itemPrice);
			itemAfterVat = $(this).parents('.card-body').find('.price:not(.d-hidden)').data('tabs'); 
			if(classType == 'extra_quota'){
				elementTitle = $(this).siblings('.tooltip').text();
			}
		}

		$('.cart.card .card-body.'+classType+'[data-cols="'+classID+'"]').remove();
		var elemString = '<div class="col-sm-12 col-lg-12 card-body '+classType+'" '+'data-cols="'+classID+'"'+'>' +
						 	'<div class="sell-card">' +
						 		'<div class="card-img">' +
						 			'<img src="'+prodImage+'" alt="">' +
						 		'</div>' +
						 		'<div class="card-body">' +
						 			'<a href="" class="delete rmv"><i class="fa fa-times"></i></a>' +
						 			'<h5 class="card-title-2">'+elementTitle+'</h5>' +
						 			'<p class="card-text-2 monthly '+monthlyHidden+'" data-tabs="'+$(monthly).data('tabs')+'">'+$(monthly).text() + ' <span>' + $('.link #tab1').text()  +'</span></p>' + 
						 			'<p class="card-text-2 yearly '+yearlyHidden+'" data-tabs="'+$(yearly).data('tabs')+'">'+$(yearly).text() + ' <span>' + $('.link #tab2').text()  +'</span></p>' + 
						 			'<p class="card-text-3 card-text-last"><span>'+typeText+'</span>:'+ $(this).siblings('.type.d-hidden').text() +'</p>' +
						 			'<a href="#" class="card-link rmv">'+remove+'</a>'+
						 		'</div>' +
						 	'</div>' +
						 '</div>';

		if(classType == 'membership'){
			$('.sellCards').prepend(elemString);
		}else{
			$('.sellCards').append(elemString);
		}
		// Increase Cart Element 
		var count = $('span.cartCount').html();
		$('span.cartCount').html(parseInt(parseInt(count) + parseInt(1)));

		// ReCalculate Prices
		calcPrices(itemPrice,itemAfterVat,'plus',classType);

		$(this).empty();
		$(this).html(butIcon + added);
		$(this).removeClass('add');
		if(classType == 'membership'){
			$(this).parents('.item-parent').siblings().find('.added.active').html(butIcon + add).removeClass('active').removeClass('added').addClass('add');
		}
		$(this).addClass('added');
		$(this).addClass('active');
	});


	// $(document).on('click','.fe.fe-plus-circle',function(){
	// 	var counterElem = $(this).parent('a').siblings('input.form-control');
	// 	var oldCounterValue = counterElem.val();
	// 	var newCounterValue = parseInt(parseInt(oldCounterValue) + parseInt(1));
	// 	if(newCounterValue <= counterElem.attr('max')){
	// 		counterElem.val(newCounterValue);
	// 		var currentPrice = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').text();
	// 		var currentPriceWithVat = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').data('tabs');
	// 		calcPrices(parseInt(currentPrice),parseInt(currentPriceWithVat),'plus');
	// 	}
	// });

	// $(document).on('click','.fe.fe-minus-circle',function(){
	// 	var counterElem = $(this).parent('a').siblings('input.form-control');
	// 	var oldCounterValue = counterElem.val();
	// 	var counterMaxValue = 0;
	// 	var newCounterValue = parseInt(parseInt(oldCounterValue) - parseInt(1));
	// 	if(newCounterValue > counterMaxValue){
	// 		counterElem.val(newCounterValue);
	// 		var currentPrice = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').text();
	// 		var currentPriceWithVat = $(this).parents('.card-aside-img').siblings('.media-body').find('.h5:not(.d-hidden)').data('tabs');
	// 		calcPrices(parseInt(currentPrice),parseInt(currentPriceWithVat),'minus');
	// 	}
	// });


	function calcPrices(itemPrice,itemAfterVat,operator,classType=null){
		var oldGrandTotal = $('span.grandTotal').text();
		var oldEstimatedTax = $('span.estimatedTax').text();
		var oldTotal = $('span.total').text();
		var userCredits = $('input[name="background"]').val();
	    var invoiceID = $('input[name="inv"]').val();

		if(classType == 'membership' && !invoiceID){
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
		$.each($('.sellCards .card-body .sell-card'),function(index,item){
			var currentPrice = $(item).find('.card-text-2.d-hidden').text();
			var currentPriceWithVat = $(item).find('.card-text-2.d-hidden').data('tabs');
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
	    var start_date = moment().format('YYYY-MM-DD')
	    var data = [];
	    $.each($('.cart .card-body .sell-card'),function(index,item){
	    	var end_date = moment(start_date).add( ( $('select[name="duration_type"]').val() == 1 ? 1 : 12 ) , 'months').format('YYYY-MM-DD');
	    	data.push([
	    		$(item).parents('.card-body').data('cols'), // id
	    		$(item).parents('.card-body').attr('class').replace('col-sm-12 col-lg-12 card-body ',''), // type
	    		$(item).find('.card-title-2').text(), // name
	    		parseInt($('select[name="duration_type"]').val()), // period
	    		start_date, // start_date,
	    		end_date, // end_date

	    		$(item).find('.card-text-2:not(.d-hidden)').data('tabs'), // total
	    		1, // qunatity
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

	    // if(!$('.card-body.membership').length){
	    // 	errorNotification(selectMemebership);
	    // }

	    var invoiceID = $('input[name="inv"]').val();
	    if(invoiceID > 0){
	    	if(totals.length && data.length && $('.card-body.membership').length){
		    	$('.payments').submit();
		    }
	    }else{
	    	if(totals.length && data.length){
		    	$('.payments').submit();
		    }
	    }

	});

});