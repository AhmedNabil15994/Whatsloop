$('.actions ul li a[href="#next"]').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $('.body.current').attr('id');
    fireAjaxRequest(id,$(this));
});

$('.actions ul li a[href="#previous"]').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    prev($(this));
});

$('.actions ul li a[href="#finish"]').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var modID = $('input[name="modID"]').val();
    window.location.href = "/QR/finish/"+modID;
});

function next(elem){
    if(!elem.parent('li').hasClass('disabled')){
        elem.parents('.actions').siblings('.steps').find('.current').not('.last').removeClass('current').addClass('done').next('li').removeClass('disabled').addClass('current');
        if(!$('.steps li.current').hasClass('first')){
            $('.actions ul li.prev').removeClass('disabled');
        }

        if($('.steps li.current').hasClass('last')){
            $('.actions ul li.next').hide();
            $('.actions ul li.finish').show();
        }

        $('section[role="tabpanel"].body.current').hide().siblings('.title.current').removeClass('current').hide();
        $('section[role="tabpanel"].body.current').next('.title').addClass('current').show();
        $('section[role="tabpanel"].body.current').removeClass('current').hide().next().next('section.body').addClass('current').show();
    }
}

function prev(elem) {
    if(!elem.parent('li').hasClass('disabled')){
        elem.parents('.actions').siblings('.steps').find('.current').not('.first').removeClass('current').addClass('done').prev('li').addClass('current');
        if($('.steps li.current').hasClass('first')){
            $('.actions ul li.prev').addClass('disabled');
        }

        if(!$('.steps li.current').hasClass('last')){
            $('.actions ul li.next').show();
            $('.actions ul li.finish').hide();
        }
        $('section[role="tabpanel"].body.current').hide().prev('.title.current').removeClass('current').hide();
        $('section[role="tabpanel"].body.current').prev().prev().prev('.title').addClass('current').show();
        $('section[role="tabpanel"].body.current').removeClass('current').hide().prev().prev('section.body').addClass('current').show();
    }
}

function fireAjaxRequest(id,elem){
    var tabsLength = $('section.body').length;
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    if(id == 'wizard1-p-0'){ // First Step
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
                        next(elem);
                    }else{
                        errorNotification(data.status.message);
                    }
                },
            });
        }else{
            if(tabsLength == 3){
                getQRCode(elem);
                setTimeout(function(){
                    next(elem);
                }, 2500);
            }else{
                next(elem);
            }
        }
    }else if(id == 'wizard1-p-1'){ 
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
                        'merchant_token': $('textarea[name="merchant_token"]').val(),
                    },
                    success:function(data){
                        if(data.status.status == 1){
                            successNotification(data.status.message);
                            next(elem);
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
                            next(elem);
                        }else{
                            errorNotification(data.status.message);
                        }
                    },
                });
            }
        }
    }else{
        if(id == 'wizard1-p-2' && tabsLength == 5){
            getQRCode(elem);
            setTimeout(function(){
                next(elem);
            }, 2500);
        }else{
            next(elem);   
        }
    }
}

function getQRCode(elem){
    if($('img.qrImage').attr('src') == '#'){
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: '/QR/getQR',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
            },
            success:function(data){
                if(data.status.status == 1){
                    $('img.qrImage').attr('src',data.data.qrImage);
                    setTimeout(function(){
                        newjobs();
                    }, 45000);
                }else{
                    errorNotification(data.status.message);
                }
            },
        });
    }
}


function newjobs(){
    var inputjob = $.ajax({
        url: "/QR/getQR",
        type: 'POST',
        data:{'_token': $('meta[name="csrf-token"]').attr('content'),},
    });
    inputjob.done(function(data) {  
        if($('img.qrImage').attr('src') != '#'){
            $('img.qrImage').attr('src',data.data.qrImage);
        }
        setTimeout(newjobs, 45000); // recursion
    });
};

$(document).on('click','.main-toggle',function(){
    var templateID = $(this).data('area');
    var status = 0;
    if($(this).hasClass('on')){
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