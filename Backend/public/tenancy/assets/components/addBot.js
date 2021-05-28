$(function(){
	// $('.summernotes').summernote({
	//     height: 200,
	//    toolbar: [
	//        // [groupName, [list of button]]
	//        ['style', ['bold', 'italic', 'underline', 'clear']],
	//        ['font', ['strikethrough', 'superscript', 'subscript']],
	//        ['fontsize', ['fontsize']],
	//        ['color', ['color']],
	//        ['para', ['ul', 'ol', 'paragraph']],
	//        ['height', ['height']]
	//      ]
	// });

	Dropzone.options.myAwesomeDropzone = false;
	Dropzone.autoDiscover = false;
	
	$.each($('.reply .kt_dropzone_1'),function(index,item){
		var dateID = $(this).parents('.reply').data('id');
		$(item).dropzone({
		    url: myURL + "/uploadImage/"+dateID,
		    paramName: "file", // The name that will be used to transfer the file
		    maxFiles: 1,
		    maxFilesize: 10, // MB
		    addRemoveLinks: true,
		    // previewTemplate: $('#uploadPreviewTemplate').html(),
		    accept: function(file, done) {
		        if (file.name == "justinbieber.jpg") {
		            done("Naha, you don't.");
		        } else {
		            done();
		        }
		    },
		    success:function(file,data){
		        if(data){
		            if(data.status.status != 1){
		                errorNotification(data.status.message);
		            }
		        }
		    },
		});

	});

	$.each($('.reply .editDropZone'),function(index,item){
		url = myURL.substr(0,myURL.lastIndexOf('/'))
		var dateID = $(this).parents('.reply').data('id');
		$(item).dropzone({
		    url: url + "/editImage/"+dateID,
		    paramName: "file", // The name that will be used to transfer the file
		    maxFiles: 1,
		    maxFilesize: 10, // MB
		    addRemoveLinks: true,
		    accept: function(file, done) {
		        if (file.name == "justinbieber.jpg") {
		            done("Naha, you don't.");
		        } else {
		            done();
		        }
		    },
		    success:function(file,data){
		        if(data){
		            if(data.status.status != 1){
		                errorNotification(data.status.message);
		            }
		        }
		    },
		});

	});


	$('a.DeletePhotoN').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    var id = $(this).data('area');
	    var myType = $(this).data('type');
	    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'POST',
	        url: myURL+'/deleteImage',
	        data:{
	            '_token': $('meta[name="csrf-token"]').attr('content'),
	            'id': id,
	            'type': myType,
	        },
	        success:function(data){
	            if(data.status.status == 1){
	                successNotification(data.status.message);
	                $('#my-preview').remove();
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

	$('select[name="reply_type"]').on('change',function(){
		var dateID = $(this).val();
		$('input[name="reply"]').val('');
		$('.reply[data-id="'+dateID+'"]').children('.hidden').removeClass('hidden');
		$('.reply[data-id="'+dateID+'"]').siblings('.reply').children('.form-group.row').addClass('hidden');
	});

})