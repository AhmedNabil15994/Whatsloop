$(function(){

    var lang = $('html').attr('lang');

    var input = document.querySelector("#telephone");
    var iti = window.intlTelInput(input,{
        initialCountry: "auto",
        geoIpLookup: function (success, failure) {
            $.get("https://ipinfo.io", function () {
            }, "jsonp").always(function (resp) {
                var countryCode = (resp && resp.country) ? resp.country : "";
                success(countryCode);
            });
        },
        preferredCountries: ["sa","ae","bh","kw","om","eg"],
    });
    
    $(document).on('click','button.loginBut',function(e){
        e.preventDefault();
        e.stopPropagation();

        var phone =  iti.getNumber();

        if (!iti.isValidNumber()) {
            if(lang == 'en'){
                errorNotification("This Phone Number Isn't Valid!");
            }else{
                errorNotification("هذا رقم الجوال غير موجود");
            }
        }

        if(phone){
            $('input[name="phone"]').val(phone);
            $('form.formLogin').submit();
        }

    });


});