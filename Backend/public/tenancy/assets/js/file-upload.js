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
				    formData.append('fileName', log);

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