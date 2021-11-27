$(function(){
    $('.btnNext:not(.btnPrev):not(.finish)').on('click',function(e){
        e.preventDefault();
        e.stopPropagation();
        // nextStep($(this));
        var id = $(this).parents('.setSteps.active').attr('id');
        fireAjaxRequest(id,$(this));
    });

    $('.btnPrev').on('click',function(e){
        e.preventDefault();
        e.stopPropagation();
        prevStep($(this));
    });

    function prevStep(elem) {
        var currentStepDiv = elem.parents('.setSteps.active');
        var prevStepDiv = currentStepDiv.prev();
        var currentStepItem = $('.step.active');
        var prevStepItem = $('.step.active').prev();

        currentStepDiv.removeClass('active');
        currentStepItem.removeClass('active');

        prevStepDiv.addClass('active');
        prevStepItem.addClass('active');
    }


    function nextStep(elem){
        var currentStepDiv = elem.parents('.setSteps.active');
        var nextStepDiv = currentStepDiv.next();
        var currentStepItem = $('.step.active');
        var nextStepItem = $('.step.active').next();

        currentStepDiv.removeClass('active');
        currentStepItem.removeClass('active');

        nextStepDiv.addClass('active');
        nextStepItem.addClass('active');

        if($('img.qrImage').length){
            if($('img.qrImage').data('area') == 1 && $('#step2').hasClass('active')){
                nextStep($('#step2').find('.btnNext:not(.btnPrev):not(.finish)'));
            }
        }
    }

    $('.finish').on('click',function(e){
        e.preventDefault();
        e.stopPropagation();
        var modID = $('input[name="modID"]').val();
        window.location.href = "/QR/finish/"+modID;
    });

    function fireAjaxRequest(id,elem){
        var tabsLength = $('.setSteps').length;
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        if(id == 'step1'){ // First Step
            var oldName = $('input[name="oldName"]').val();
            var newName = $('input[name="channelName"]').val();
            if(oldName != newName){
                // Fire Update Name Request
                $.ajax({
                    type: 'POST',
                    url: '/QR/updateName',
                    data:{
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'name': newName,
                    },
                    success:function(data){
                        if(data.status.status == 1){
                            successNotification(data.status.message);
                            nextStep(elem);
                        }else{
                            errorNotification(data.status.message);
                        }
                    },
                });
            }else{
                if(tabsLength == 3){
                    // getQRCode(elem);
                    setTimeout(function(){
                        nextStep(elem);
                        if($('img.qrImage').data('area') == 1){
                            nextStep(elem);
                        }
                    }, 2500);
                }else{
                    nextStep(elem);
                }
            }
        }else if(id == 'step4'){ 
            if(tabsLength == 5){
                var modID = $('input[name="modID"]').val();
                if(modID == 4){
                    $.ajax({
                        type: 'POST',
                        url: '/profile/updateZid',
                        data:{
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'store_token': $('input[name="store_token"]').val(),
                            'store_id': $('input[name="store_id"]').val(),
                        },
                        success:function(data){
                            if(data.status.status == 1){
                                successNotification(data.status.message);
                                nextStep(elem);
                            }else{
                                errorNotification(data.status.message);
                            }
                        },
                    });
                }else if(modID == 5){
                    $.ajax({
                        type: 'POST',
                        url: '/profile/updateSalla',
                        data:{
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'store_token': $('input[name="store_tokens"]').val(),
                        },
                        success:function(data){
                            if(data.status.status == 1){
                                successNotification(data.status.message);
                                nextStep(elem);
                            }else{
                                errorNotification(data.status.message);
                            }
                        },
                    });
                }
            }
        }else{
            if(id == 'step3' && tabsLength == 5){
                // getQRCode(elem);
                setTimeout(function(){
                    nextStep(elem);
                    if($('img.qrImage').data('area') == 1){
                        nextStep(elem);
                    }
                }, 2500);

            }else{
                nextStep(elem);
            }
        }
    }

    $(document).on('change','.custom-switch-input',function(){
        var templateID = $(this).data('area');
        var status = 0;
        if($(this).is(':checked')){
            status = 1;
        }

        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: '/QR/editTemplate',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'id' : templateID,
                'status': status,
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
});