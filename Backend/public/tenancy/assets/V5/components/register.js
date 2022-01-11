$(function(){
   
    var lang = $('html').attr('lang');

    $("input[name='company']").on('keyup', function (event) {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'GET',
            url: '/translate',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'company': $(this).val(),
            },
            success:function(data){
                $('input[name="domain"]').empty();
                if(data.status.status == 1){
                    $('input[name="domain"]').val(data.data);
                }
            },
        });
    });

    $('input[name="domain"]').on('focusout',function(){
        if(!$(this).val()){
            $(this).siblings('span.test').empty();
            $('span.test').css('opacity',0);
            $(this).css('fontSize','unset');
        }else{
            $(this).siblings('span.test').html($(this).val()+'.' +'wloop.net');
            $('span.test').css('opacity',1);
            $(this).css('fontSize','0');
        }
    });

    $('input[name="domain"]').on('focusin',function(){
        $(this).siblings('span.test').empty();
        $('span.test').css('opacity',0);
        $(this).css('fontSize','unset');
    });

    $('input[name="domain"]').keypress(function(event){
        var ew = event.which;
        if(48 <= ew && ew <= 57)
            return true;
        if(65 <= ew && ew <= 90)
            return true;
        if(97 <= ew && ew <= 122)
            return true;
        return false;
    });

    $('input[name="terms"]').on('change',function(){
        if($(this).is(":checked")){
            $('.loginBut').attr('disabled',false);
        }else{
            $('.loginBut').attr('disabled',true);
        }
    });

    // $(document).on('click','button.loginBut',function(e){
    //     e.preventDefault();
    //     e.stopPropagation();

    //     var phone =  $("#telephone").intlTelInput("getNumber");
    //     var errors = 1;
    //     if (!$("#telephone").intlTelInput("isValidNumber")) {
    //         if(lang == 'en'){
    //             errorNotification("This Phone Number Isn't Valid!");
    //         }else{
    //             errorNotification("هذا رقم الجوال غير موجود");
    //         }
    //         errors = 0;
    //     }

    //     if(phone && errors == 1){
    //         $('input[name="phone"]').val(phone);
    //         $(this).parent('form').submit();
    //     }

    // });

});