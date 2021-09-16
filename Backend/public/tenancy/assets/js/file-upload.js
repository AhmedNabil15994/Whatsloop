$(document).on('change', ':file', function() {
	var input = $(this),
		numFiles = input.get(0).files ? input.get(0).files.length : 1,
		label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		input.trigger('fileselect', [numFiles, label]);
	});

	// We can watch for our custom `fileselect` event like this
	$(document).ready( function() {
	  	$(':file').on('fileselect', function(event, numFiles, label) {

		  	var input = $(this).parents('.input-group').find(':text'),
			    log = numFiles > 1 ? numFiles + ' files selected' : label;

		  	if(input.length){
			  	input.val(log);
		  	}else{
			  	if(log){
			  		var formData = new FormData();
				    var $file = document.getElementById('dropify010');
				    if ($file.files.length > 0) {
				       for (var i = 0; i < $file.files.length; i++) {
				            formData.append('transfer_image', $file.files[i]);
				       }
				    }

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

				    formData.append('fileName', log);
				    formData.append('total', $('span.total').html());
				    formData.append('name', $('input[name="name"]').val());
				    formData.append('company_name', $('input[name="company_name"]').val());
				    formData.append('address', $('input[name="address"]').val());
				    formData.append('address2', $('input[name="address2"]').val());
				    formData.append('country', $('select[name="country"] option:selected').val());
				    formData.append('region', $('select[name="region"] option:selected').val());
				    formData.append('city', $('input[name="city"]').val());
				    formData.append('postal_code', $('input[name="postal_code"]').val());
				    formData.append('tax_id', $('input[name="tax_id"]').val());

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
			  	}
		  	}
	  	});
	});