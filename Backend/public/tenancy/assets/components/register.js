$(function(){
   
    var lang = $('html').attr('lang');

    $("#telephone").intlTelInput({
        initialCountry: $('input[name="country_code"]').val(),
        preferredCountries: ["sa","ae","bh","kw","om","eg"],
    });

    $('input[name="domain"]').on('keyup',function(){
        if(!$(this).val()){
            $(this).siblings('p').empty();
        }else{
            $(this).siblings('p').html($(this).val()+'.' + 'wloop.net')
        }
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