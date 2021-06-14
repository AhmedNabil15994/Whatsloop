$(function(){
   
    var lang = $('html').attr('lang');

    $("#telephone").intlTelInput({
        initialCountry: $('input[name="country_code"]').val(),
        preferredCountries: ["sa","ae","bh","kw","om","eg"],
    });

    $(document).on('click','button.loginBut',function(e){
        e.preventDefault();
        e.stopPropagation();

        var phone =  $("#telephone").intlTelInput("getNumber");
        var errors = 1;
        if (!$("#telephone").intlTelInput("isValidNumber")) {
            if(lang == 'en'){
                errorNotification("This Phone Number Isn't Valid!");
            }else{
                errorNotification("هذا رقم الجوال غير موجود");
            }
            errors = 0;
        }

        if(phone && errors == 1){
            $('input[name="phone"]').val(phone);
            $(this).parent('form').submit();
        }

    });

});