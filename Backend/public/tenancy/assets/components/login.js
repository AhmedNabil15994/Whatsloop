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
        var password = $('input[name="password"]').val();

        if (!$("#telephone").intlTelInput("isValidNumber")) {
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
                            location.reload();
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
                        window.location.href = "/dashboard";
                    }else{
                        errorNotification(data.status.message);
                    }
                },
            });
        }
    });

});