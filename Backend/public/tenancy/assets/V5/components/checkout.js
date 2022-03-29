$('.btnNext:not(.btnPrev):not(.invoice)').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    next($(this));
});

$('.invoice').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var divToPrint=document.getElementById('helpPage');
    var head = $('head').text();
    var newWin=window.open('','Print-Window');

    newWin.document.open();

    newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');

    newWin.document.close();

    setTimeout(function(){newWin.close();},10);
});

$('.btnPrev').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    prev($(this));
});

function prev(elem) {
    var currentStepDiv = elem.parents('.paySteps.active');
    var prevStepDiv = currentStepDiv.prev();
    var currentStepItem = $('.step.active');
    var prevStepItem = $('.step.active').prev();

    currentStepDiv.removeClass('active');
    currentStepItem.removeClass('active');

    prevStepDiv.addClass('active');
    prevStepItem.addClass('active');
}


function next(elem){
    var currentStepDiv = elem.parents('.paySteps.active');
    var nextStepDiv = currentStepDiv.next();
    var currentStepItem = $('.step.active');
    var nextStepItem = $('.step.active').next();

    currentStepDiv.removeClass('active');
    // currentStepItem.removeClass('active');

    nextStepDiv.addClass('active');
    nextStepItem.addClass('active');
}

function getData(){
    var data = [];
    $.each($('.tableRow'),function(index,item){
        data.push([
            $(item).data('cols'), // id
            $(item).data('type'), // type
            $(item).find('.details').children('.title').text(), // name
            parseInt($(item).data('dur')), // period
            $(item).children('td').find('span.start_date').text(), // start_date,
            $(item).children('td').find('span.end_date').text(), // end_date,
            $(item).children('td.prices').find('span.price').text(), // total
            $(item).find('td.quantity').text(), // qunatity
        ]);
    });

    var totals = [
        $('b.grandTotal').html(),
        0, // discount
        $('b.estimatedTax').html(),
        $('b.total').html(),
    ];

    return [data,totals];
}

function setData(allData) {
    var data = allData[0];
    var totals = allData[1];

    $('input[name="data"]').val(JSON.stringify(data));
    $('input[name="totals"]').val(JSON.stringify(totals));
    
    if(totals.length && data.length && $('input[name="payType"]').val()){
        $('.completeOrder').submit();
    }
}

$('.selectPayment .paymentStyle').on('click',function(){
    var paymentType = $(this).data('area');
    $('input[name="payType"]').val(paymentType);

    if(paymentType != 1){
        $('input[name="payType"]').val(paymentType);
        var allData = getData();
        setData(allData);
    }
});

function calcTaxes(oldGrandTotal){
    var oldTotal = oldGrandTotal.toFixed(2);
    var estimatedTax = oldTotal * (15/115);

    estimatedTax = estimatedTax.toFixed(2);
    oldEstimatedTax = parseFloat(oldGrandTotal) - parseFloat(estimatedTax);
    oldEstimatedTax = oldEstimatedTax.toFixed(2);

    $('b.grandTotal').text(oldEstimatedTax);
    $('b.estimatedTax').text(estimatedTax);
    $('b.total').text(oldTotal);
}

$('a.rmv').on('click',function(){
    var elemPrice = $(this).parents('tr').children('td.prices').find('span.price').text();
    calcTaxes(parseFloat($('b.total').html()) - parseFloat(elemPrice));

    $(this).parents('tr').remove();
});

$(document).on('change', '.labelUpload input[type="file"]', function() {
    var log = $(this).val().split('\\').pop();
    var allData = getData();
    data = allData[0];
    totals = allData[1];

    var formData = new FormData();
    formData.append('transfer_image', $(this)[0].files[0]);

    formData.append('fileName', log);
    formData.append('total', $('b.total').html());
    formData.append('name', $('input[name="name"]').val());
    formData.append('company_name', $('input[name="company_name"]').val());
    formData.append('address', $('input[name="address"]').val());
    formData.append('address2', $('input[name="address2"]').val());
    formData.append('country', $('select[name="country"] option:selected').val());
    formData.append('region', $('select[name="region"] option:selected').val());
    formData.append('city', $('input[name="city"]').val());
    formData.append('postal_code', $('input[name="postal_code"]').val());
    formData.append('tax_id', $('input[name="tax_id"]').val());
    formData.append('invoice_id', $('input[name="invoice_id"]').val());

    formData.append('data', JSON.stringify(data));
    formData.append('totals', JSON.stringify(totals));

    $.ajax({
        type:'POST',
        url: '/checkout/bankTransfer',
        data:formData,
        cache:false,
        contentType: false,
        processData: false,
        success:function(data){
            setTimeout(function(){
                window.location.href = data.data;
            }, 2500);
        },
        error: function(data){
            errorNotification(data.status.message);
            // location.reload();
        }
    });

});


$(document).on('change', ':file', function() {
    var input = $(this),
    numFiles = input.get(0).files ? input.get(0).files.length : 1,
    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
});


$('.finish').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    // Complete Order Here
    
    var allData = getData();
    setData(allData);
});

$('.addCoupon:not(.newCoupon)').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();

    var inputItem = $(this).parents('.coupon').children('input');
    var couponVal  = inputItem.val();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
        type: 'POST',
        url: '/coupon',
        data:{
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'coupon': couponVal,
        },
        success:function(data){
            if(data.status.status == 1){
                successNotification(data.status.message);
                // inputItem.val(' ');
                var discount_type = data.data.discount_type;
                var discount_value = data.data.discount_value;

                var total = $('b.total').text();
                var grandTotal = $('b.grandTotal').text();
                var discount = discount_type == 1 ? discount_value : (discount_value*grandTotal)/100;
                var taxDiscount = ((discount * 15) / 100).toFixed(2); 
                //Calc New Prices
                $('b.discount').text((parseFloat($('b.discount').text())  +  parseFloat(discount)).toFixed(2));
                $('b.grandTotal').text((parseFloat($('b.grandTotal').text())  -  parseFloat(discount)).toFixed(2)); 
                $('b.estimatedTax').text((parseFloat($('b.estimatedTax').text())  -  parseFloat(taxDiscount)).toFixed(2)); 
                $('b.total').text((parseFloat($('b.total').text())  -  parseFloat(discount) - parseFloat(taxDiscount)).toFixed(2)); 
                $('.addCoupon').attr('disabled',true);
                $('.addCoupon').addClass('newCoupon');
            }else{
                errorNotification(data.status.message);
            }
        },
    });
});

$('#step2 input[name="terms"]').on('change',function(){
    if($(this).is(":checked")){
        $('.myNext').attr('disabled',false);
        $('#termsModal').modal('show');
    }else{
        $('.myNext').attr('disabled',true);
    }
});