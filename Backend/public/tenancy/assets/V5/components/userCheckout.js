$('.actions ul li a[href="#next"]').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $('.body.current').attr('id');
    next($(this));
    if($('.body.current').attr('id') == 'wizard1-p-4'){
        $('.actions ul li a[href="#next"]').hide();
    }
});

$('.actions ul li a[href="#previous"]').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    prev($(this));
    if($('.body.current').attr('id') != 'wizard1-p-4'){
        $('.actions ul li a[href="#next"]').show();
    }
});

$('.actions ul li a[href="#finish"]').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    // Complete Order Here
    
    var data = [];
    $.each($('.tableRow'),function(index,item){
        data.push([
            $(item).data('cols'), // id
            $(item).data('type'), // type
            $(item).children('td').find('.font-weight-semibold.text-uppercase').text(), // name
            parseInt($(item).data('dur')), // period
            $(item).children('td').find('span.start_date').text(), // start_date,
            $(item).children('td').find('span.end_date').text(), // end_date,
            $(item).children('td.prices').find('span.price').text(), // total
            $(item).children('td').find('select[name="quantity"]').val(), // qunatity
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
    if(totals.length && data.length && $('input[name="payType"]').val()){
        $('.completeOrder').submit();
    }
});

function prev(elem) {
    if(!elem.parent('li').hasClass('disabled')){
        elem.parents('.actions').siblings('.steps').find('.current').not('.first').removeClass('current').addClass('done').prev('li').addClass('current');
        if($('.steps li.current').hasClass('first')){
            $('.actions ul li.prev').addClass('disabled');
        }

        if(!$('.steps li.current').hasClass('last')){
            $('.actions ul li.next').show();
            $('.actions ul li.finish').hide();
        }
        $('section[role="tabpanel"].body.current').hide().prev('.title.current').removeClass('current').hide();
        $('section[role="tabpanel"].body.current').prev().prev().prev('.title').addClass('current').show();
        $('section[role="tabpanel"].body.current').removeClass('current').hide().prev().prev('section.body').addClass('current').show();
    }
}


function next(elem){
    if(!elem.parent('li').hasClass('disabled')){
        elem.parents('.actions').siblings('.steps').find('.current').not('.last').removeClass('current').addClass('done').next('li').removeClass('disabled').addClass('current');
        if(!$('.steps li.current').hasClass('first')){
            $('.actions ul li.prev').removeClass('disabled');
        }

        if($('.steps li.current').hasClass('last')){
            $('.actions ul li.next').hide();
            $('.actions ul li.finish').show();
        }

        $('section[role="tabpanel"].body.current').hide().siblings('.title.current').removeClass('current').hide();
        $('section[role="tabpanel"].body.current').next('.title').addClass('current').show();
        $('section[role="tabpanel"].body.current').removeClass('current').hide().next().next('section.body').addClass('current').show();
    }
}

$('.payments.rounded').on('click',function(){
    var paymentType = $(this).data('area');
    if(paymentType == 1){
        $('.transfer').removeClass('d-hidden');
        $('.ePayment').addClass('d-hidden');
    }else{
        $('.transfer').addClass('d-hidden');
        $('.ePayment').removeClass('d-hidden');
    }
    $('.ePayment input[type="radio"]').prop('checked',false);
    $('.noon').addClass('d-hidden');
    $('input[name="payType"]').val(paymentType);
});

$(document).on('change','input[name="billingOptions"]',function(){
    if($(this).is(":checked")){
        var paymentType = $(this).data('area');
        if(paymentType == 2){
            $('.transfer').addClass('d-hidden');
            $('.noon').addClass('d-hidden');
        }else if(paymentType == 3){
            $('.transfer').addClass('d-hidden');
            $('.noon').removeClass('d-hidden');
            $('.ePayment').addClass('d-hidden');
            $('.noon input[type="radio"]').prop('checked',false);
        }
        $('input[name="payType"]').val(paymentType);
    }
});

$('select[name="quantity"]').on('change',function(){
    var oldValue = $(this).data('tabs');
    var elemPrice = $(this).data('area');
    var printElem = $(this).parents('tr').children('td.prices').find('span.price');
    var oldPrice = printElem.text();
    var newValue = $(this).val();
    var newPrice = newValue * elemPrice;

    var diff = newPrice - oldPrice;


    printElem.text(newPrice);
    $(this).attr('data-tabs',newValue);
    $('span.grandTotal').text(parseInt($('span.grandTotal').html()) + parseInt(diff));
    $('span.total').text(parseInt($('span.total').html()) + parseInt(diff));
});

$('a.rmv').on('click',function(){
    var elemPrice = $(this).parents('tr').children('td.prices').find('span.price').text();
    $('span.grandTotal').text(parseInt($('span.grandTotal').html()) - parseInt(elemPrice));
    $('span.total').text(parseInt($('span.total').html()) - parseInt(elemPrice));

    $(this).parents('tr').remove();
});