/**
 * Comments Js
 */


function deleteComment($id) {
    Swal.fire({
        title: title,
        text: deleteText,
        type: "warning",
        showCancelButton: true,
        confirmButtonText: confirmButton,
        confirmButtonClass: 'btn btn-success mt-2',
        cancelButtonText: cancelButton,
        cancelButtonClass: 'btn btn-danger ml-2 mt-2',
        closeOnConfirm: false,
        buttonsStyling:!1
    }).then(function(result){
        if (result.value) {
            Swal.fire(success1, success2, "success");
            $.get(myURL+'/removeComment/' + $id,function(data) {
                if (data.status.original.status.status == 1) {
                    successNotification(data.status.original.status.message);
                    $('#tableRaw' + $id).remove();
                    var count = parseInt($('span.total_comments').html());
                    $('span.total_comments').html(count-1);
                } else {
                    errorNotification(data.status.original.status.message);
                }
            });
        } else if (result.dismiss === "cancel") {
            Swal.fire(
                cancel1,
                cancel2,
                "error"
            )
        }
    });
}

function sendComment(reply){
    var url = window.location.href;
    if(url.indexOf("#") != -1){
        url = url.replace('#','');
    }
    var myURL = url+'/addComment';
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
        type:'post',
        url: myURL,
        data:{
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'comment': $('.comment').val(),
            'reply': reply,
        },
        success:function(data){
            if(data.status.status == 1){
                successNotification(data.status.message);
                location.reload();
            }else{
                errorNotification(data.status.message);
            }
        },
    });
}

$(document).on('click','a.reply',function(e){
    e.preventDefault();
    e.stopPropagation();
    var comment_id = $(this).attr('data-area');
    $('html, body').animate({
        scrollTop: $(".comment-area-box").offset().top
    }, 350);
    $('button.newComm').attr('data-area',comment_id);
});

$('button.newComm').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    sendComment($(this).attr('data-area'));
});
