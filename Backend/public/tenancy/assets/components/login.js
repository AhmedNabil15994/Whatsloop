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
        
        var password = $('input[name="password"]').val();
        if (!iti.isValidNumber() && phone != '+966123456789') {
            if(lang == 'en'){
                errorNotification("This Phone Number Isn't Valid!");
            }else{
                errorNotification("هذا رقم الجوال غير موجود");
            }
        }

        if(password && phone){
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $.ajax({
                type: 'POST',
                url: '/login',
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'password': password,
                    'phone': phone,
                },
                success:function(data){
                    if(data.status.status == 1){
                        successNotification(data.status.message);
                        if(data.status.code == 205){
                            // location.reload();
                            window.location.href = data.data;
                        }else{
                            $('.codes').removeClass('hidden');
                            $('button.loginBut').addClass('check');
                            $('button.loginBut.check').removeClass('loginBut');
                        }                        
                    }else{
                        errorNotification(data.status.message);
                    }
                },
            });
        }

    });

    $(document).on('click','button.check',function(e){
        e.preventDefault();
        e.stopPropagation();
        var code = $('input[name="code"]').val();        
        if(code){
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $.ajax({
                type: 'POST',
                url: '/checkByCode',
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'code': code,
                },
                success:function(data){
                    if(data.status.status == 1){
                        window.location.href = "/menu";
                    }else{
                        errorNotification(data.status.message);
                    }
                },
            });
        }
    });

});