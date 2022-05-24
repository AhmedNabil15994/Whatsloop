$(function(){
	$('a.screen').on('click',function(e){
	    e.preventDefault();
	    e.stopPropagation();
	    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
	    $.ajax({
	        type: 'get',
	        url: myURL+'/screenshot',
	        data:{'_token': $('meta[name="csrf-token"]').attr('content'),},
	        success:function(data){
	            if(data.image){
	            	$('#full-width-modal img').attr('src',data.image);
	            	$('#full-width-modal').modal('show');
	            }else{
	                errorNotification(data.status.message);
	            }
	        },
	    });
	});

	$(document).on('change','.custom-switch-input',function(){
        var key = $(this).data('area');
        var status = 0;
        if($(this).is(':checked')){
            status = 1;
        }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: myURL+'/updateChannelSettings',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'key' : key,
                'value': status,
            },
            success:function(data){
                if(data.status.status == 1){
                    successNotification(data.status.message);
                }else{
                    errorNotification(data.status.message);
                }
            },
        });
    });

    var lang = $('html').attr('lang');
	if(lang == 'en'){
	    var title = "This procedure will erase all the data in your account";
	    var confirmButton = "Yes";
	    var cancelButton = "No";
	    var deleteText = "and it cannot be recovered after that";
	    var success1 = "Deleted Successfully!";
	    var success2 = "The operation was successful";
	    var cancel1 = "Cancelled";
	    var cancel2 = "Canceled successfully";
	    var langPref = 'en';
	    var rtlMode = false;
	}else{
	    var title = "هذا الاجراء سيقوم بمسح جميع البيانات الموجودة في حسابك";
	    var confirmButton = "نعم";
	    var cancelButton = "لا";
	    var deleteText = " ولا يمكن استرجاعها بعد ذلك";
	    var success1 = "تم الحذف بنجاح!";
	    var success2 = "تمت العملية بنجاح";
	    var cancel1 = "تم الالغاء";
	    var cancel2 = "تم الالغاء بنجاح";
	    var langPref = 'ar_AR';
	    var rtlMode = true;
	}


    $('.MeasuresText.restoreAccountSettings').on('click',function(e){
    	e.preventDefault();
    	e.stopPropagation();
    	var URL = $(this).attr('href');

    	swal({
	        title: title,
	        text: deleteText,
	        type: "warning",
	        showCancelButton: true,
	        confirmButtonText: confirmButton,
	        confirmButtonClass: 'btn btn-success mt-2',
	        cancelButtonText: cancelButton,
	        cancelButtonClass: 'btn-danger ml-2 mt-2',
	        closeOnConfirm: false,
	        buttonsStyling:!1
	    },
	    function(isConfirm) {
	        if (isConfirm) {
	            window.location.href = URL
	        } else {
	            swal(
	                cancel1,
	                cancel2,
	                "error"
	            )
	            swal("Cancelled", "Your imaginary file is safe :)", "error");
	        }
	    });
    });
});	