$(document).off('.datepicker');
var myURL = window.location.href;
if(myURL.indexOf("#") != -1){
    myURL = myURL.replace('#','');
}
var lang = $('html').attr('lang');
if(lang == 'en'){
    var title = "Are you sure about this deletion?";
    var confirmButton = "Confirm";
    var cancelButton = "Cancel";
    var deleteText = "You cannot undo this step!";
    var success1 = "Deleted Successfully!";
    var success2 = "The operation was successful";
    var cancel1 = "Cancelled";
    var cancel2 = "Canceled successfully";
    var langPref = 'en';
    var rtlMode = false;
}else{
    var title = "هل متأكد من هذا الحذف ؟";
    var confirmButton = "تأكيد";
    var cancelButton = "الغاء";
    var deleteText = "لا يمكنك التراجع عن هذه الخطوة!";
    var success1 = "تم الحذف بنجاح!";
    var success2 = "تمت العملية بنجاح";
    var cancel1 = "تم الالغاء";
    var cancel2 = "تم الالغاء بنجاح";
    var langPref = 'ar_AR';
    var rtlMode = true;
}

if($('.datepicker').length){
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        language: langPref,
        rtl: rtlMode
    });  
}

function deleteItem($id) {
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
            $.get(myURL+'/delete/' + $id,function(data) {
                if (data.status.original && data.status.original.status.status == 1) {
                    successNotification(data.status.original.status.message);
                    swal(success1, success2, "success");
                    setTimeout(function(){
                        $('#kt_datatable').DataTable().ajax.reload();
                    },2500)
                } else {
                    errorNotification(data.status.message);
                }
            });
        } else {
            swal(
                cancel1,
                cancel2,
                "error"
            )
            swal("Cancelled", "Your imaginary file is safe :)", "error");
        }
    });
}

function deleteStorageFile($url) {
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
            swal(success1, success2, "success");
            $.get($url,function(data) {
                if (data.status.original.status.status == 1) {
                    successNotification(data.status.original.status.message);
                    window.location.href = '/storage';
                } else {
                    errorNotification(data.status.original.status.message);
                }
            });
        } else {
            swal(
                cancel1,
                cancel2,
                "error"
            )
            swal("Cancelled", "Your imaginary file is safe :)", "error");
        }
    });
}


$('select[data-toggle="select2"]').select2();
$('select#selectize-select').selectize({
    create:!0,
    sortField:{
        field:"text",
        direction:"asc"
    },
    dropdownParent:"body"
});
// $('select[data-toggle="selectize-select"]').selectize();

$(".teles").intlTelInput({
    initialCountry: $('input[name="countriesCode"]').val(),
    preferredCountries: ["sa","ae","bh","kw","om","eg"],
});

$('.quickEdit').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();

    $(this).toggleClass('opened');
    var myDataObjs = [];
    var i = 190;
    $(document).find('table tbody tr td.edits').each(function(index,item){
        var oldText = '';
        if($('.quickEdit').hasClass('opened')){
            var myText = $(item).find('a.editable').text();
            $(item).find('a.editable').hide();
            var myElem = '<span qe="scope">'+
                            '<span>'+
                                '<input type="text" class="form-control" qe="input" value="'+myText+'"/>'+
                            '</span>'+
                        '</span>';
            if($(this).hasClass('selects')){
                var selectOptions = '';
                var selectName = $(this).children('a.editable').data('col');
                var elem = $("select[name='"+selectName+"'] option");
                elem.each(function(){
                    var selected = '';
                    if($(this).text() == myText){
                        selected = ' selected';
                    }
                    if($(this).val() >= 0){
                        selectOptions+= '<option value="'+$(this).val()+'" '+selected+'>'+$(this).text()+'</option>';
                    }
                });
                myElem = '<span qe="scope">'+
                            '<span>'+
                                '<select class="form-control">'+
                                    selectOptions+
                                '</select>'+
                            '</span>'+
                        '</span>';
            }
            if($(this).hasClass('dates')){
                myElem = '<span qe="scope">'+
                            '<span>'+
                                '<input type="text" class="form-control datetimepicker-input" id="kt_datetimepicker_'+i+'" value="'+myText+'" data-toggle="datetimepicker" data-target="#kt_datetimepicker_'+i+'"'+
                            '</span>'+
                        '</span>';
            }
            if(!$(item).find('a.dis').length){
                $(item).append(myElem);
            }
            oldText = myText;
            i++;
        }else{
            var myText = '';
            var newVal = 0;
            if($(this).hasClass('selects')){
                myText = $(item).find('select option:selected').text();
                newVal = $(item).find('select option:selected').val();
            }else{
                myText = $(item).find('input.form-control').val();
            }
            $(item).children('span').remove();
            oldText = $(item).find('a.editable').text();
            $(item).find('a.editable').text(myText);
            $(item).find('a.editable').show();

            if(myText != oldText){
                var myCol = $(item).find('a.editable').data('col');
                if($(this).hasClass('selects')){
                    var myValue = newVal;
                }else{
                    var myValue = myText;
                }
                var myId = $(item).find('a.editable').data('id');
                myDataObjs.push([myId,myCol,myValue]);
            }

        }
    });

    $('td.dates span span input.datetimepicker-input').datepicker({
        enableTime:!0,
        dateFormat:"Y-m-d H:i:s",
    });
    
    if(myDataObjs[0] != null){
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: myURL+'/fastEdit',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'data': myDataObjs,
            },
            success:function(data){
                if(data.status.status == 1){
                    successNotification(data.status.message);
                    $('#kt_datatable').DataTable().ajax.reload();
                }else{
                    errorNotification(data.status.message);
                    $('#kt_datatable').DataTable().ajax.reload();
                }
            },
        });
    }
});

$('.search-mode').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('#AdvancedSearchHelp').modal('toggle');
});

// Prevent Dropzone from auto discovering this element:
Dropzone.options.myAwesomeDropzone = false;
Dropzone.autoDiscover = false;
$('#kt_dropzone_1').dropzone({
    url: myURL + "/uploadImage", // Set the url for your upload script location
    paramName: "file", // The name that will be used to transfer the file
    maxFiles: 1,
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    accept: function(file, done) {
        if (file.name == "justinbieber.jpg") {
            done("Naha, you don't.");
        } else {
            done();
        }
    },
    success:function(file,data){
        if(data){
            if(data.status.status != 1){
                errorNotification(data.status.message);
            }
        }
    },
});  

// $('#kt_dropzone_100').dropzone({
//     url: myURL + "/uploadImage", // Set the url for your upload script location
//     paramName: "photos", // The name that will be used to transfer the file
//     maxFiles: 10,
//     maxFilesize: 5, // MB
//     addRemoveLinks: true,
//     accept: function(file, done) {
//         if (file.name == "justinbieber.jpg") {
//             done("Naha, you don't.");
//         } else {
//             done();
//         }
//     },
//     success:function(file,data){
//         if(data){
//             if(data.status.status != 1){
//                 errorNotification(data.status.message);
//             }
//         }
//     },
// });

$('#kt_dropzone_11').dropzone({
    url: myURL + "/editImage", // Set the url for your upload script location
    paramName: "file", // The name that will be used to transfer the file
    maxFiles: 1,
    maxFilesize: 10, // MB
    addRemoveLinks: true,
    accept: function(file, done) {
        if (file.name == "justinbieber.jpg") {
            done("Naha, you don't.");
        } else {
            done();
        }
    },
    success:function(file,data){
        if(data){
            if(data.status.status != 1){
                errorNotification(data.status.message);
            }
        }
    },
});

$('a.DeletePhoto').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).data('area');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
        type: 'POST',
        url: myURL+'/deleteImage',
        data:{
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id,
        },
        success:function(data){
            if(data.status.status == 1){
                successNotification(data.status.message);
                $('#my-preview').remove();
            }else{
                errorNotification(data.status.message);
            }
        },
    });
});

$('select[name="country"]').on('change',function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).val();
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
        type: 'get',
        url: '/getCities',
        data:{
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id,
        },
        success:function(data){
            if(data.status.status == 1){
                $('select[name="region"] option.data').remove();
                var elemString = '';
                $.each(data.regions,function(index,item){
                    elemString+= '<option value="'+index+'" class="data">'+item.name+'</option>'
                });
                $('select[name="region"]').append(elemString)
            }else{
                errorNotification(data.status.message);
            }
        },
    });
});


$('.print-but').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.buttons-print')[0].click();
});

$('.copy-but').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.buttons-copy')[0].click();
});

$('.excel-but').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.buttons-excel')[0].click();
});

$('.csv-but').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.buttons-csv')[0].click();
});

$('.pdf-but').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('.buttons-pdf')[0].click();
});

$('#SubmitBTN,.SaveBTN').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('input[name="status"]').val(1);
    var phone =  $(".teles").intlTelInput("getNumber");
    if (!$(".teles").intlTelInput("isValidNumber") && !$('.teles').parents('.form-group.row').hasClass('hidden') &&(
            ($('input[name="vType"]').length && $('input[name="vType"]').val() == 2) || !$('input[name="vType"]').length)){
        if(lang == 'en'){
            errorNotification("This Phone Number Isn't Valid!");
        }else{
            errorNotification("هذا رقم الجوال غير موجود");
        }
    }else{
        $('input.teles').val(phone);
        $(this).parents('form').submit();
    }
});

$('.SaveBTNs').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    $('input[name="status"]').val(1);
    var errors = 0;
    $.each($('input.teles'),function(index,item){
        var phone =  $(item).intlTelInput("getNumber");
        if (!$(item).intlTelInput("isValidNumber") && $(item).attr("name") == 'phone' && !$(item).parents('.form-group').hasClass('hidden')){
            errors+= 1;
        }else{
            $(item).val(phone);
        }
    });
    
    if(errors == 0){
        $(this).parent().parents('form').submit();
    }else{
        if(lang == 'en'){
            errorNotification("This Phone Number Isn't Valid!");
        }else{
            errorNotification("هذا رقم الجوال غير موجود");
        }
    }
});

$('#SaveBTN').on('click',function(){
    $('input[name="status"]').val(0);
    var phone =  $(".teles").intlTelInput("getNumber");
    if (!$(".teles").intlTelInput("isValidNumber") && !$('.teles').parents('.form-group.row').hasClass('hidden') &&(
            ($('input[name="vType"]').length && $('input[name="vType"]').val() == 2) || !$('input[name="vType"]').length)){
        if(lang == 'en'){
            errorNotification("This Phone Number Isn't Valid!");
        }else{
            errorNotification("هذا رقم الجوال غير موجود");
        }
    }else{
        $('input.teles').val(phone);
        $('form').submit();
    }
});

function initUploadFiles(id) {
        // set the dropzone container id
    // set the preview element template
    var previewNode = $(id + " .dropzone-item");
    previewNode.id = "";
    var previewTemplate = previewNode.parent('.dropzone-items').html();
    var uploadUrl = myURL + "/editImage";
    if(id != '#kt_dropzone_5'){
        previewNode.remove();
        var checkURL = $('#kt_dropzone_4').data('url');
        if(checkURL){
            uploadUrl = checkURL + "/uploadImage";
        }else{
            uploadUrl = myURL + "/uploadImage";
        }
    }

    var myDropzone5 = new Dropzone(id, { // Make the whole body a dropzone
        url: uploadUrl, // Set the url for your upload script location
        parallelUploads: 20,
        maxFilesize: 10, // Max filesize in MB
        previewTemplate: previewTemplate,
        previewsContainer: id + " .dropzone-items", // Define the container to display the previews
        clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
        paramName: "files",
        success:function(file,data){
            if(data){
                // data = JSON.parse(data);
                if(data.status.status != 1){
                    errorNotification(data.status.message);
                }
            }
        },
    });

    myDropzone5.on("addedfile", function(file) {
        // Hookup the start button
        $(document).find( id + ' .dropzone-item').css('display', '');
    });

    // Update the total progress bar
    myDropzone5.on("totaluploadprogress", function(progress) {
        $( id + " .progress-bar").css('width', progress + "%");
    });

    myDropzone5.on("sending", function(file) {
        // Show the total progress bar when upload starts
        $( id + " .progress-bar").css('opacity', "1");
    });

    // Hide the total progress bar when nothing's uploading anymore
    myDropzone5.on("complete", function(progress) {
        var thisProgressBar = id + " .dz-complete";
        setTimeout(function(){
            $( thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress").css('opacity', '0');
        }, 300)
    });
}

if($('#kt_dropzone_4').length){
    initUploadFiles('#kt_dropzone_4');
}

if($('#kt_dropzone_5').length){
    initUploadFiles('#kt_dropzone_5');
}

if($('.summernote').length){
    $('.summernote').summernote({
        fontNames: ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana', 'Roboto'],
        height: 300,
        toolbar: [
            // [groupName, [list of button]]
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript','Arial']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });
}

$('.Reset').on('click',function(){
    $('input').attr('value','');
    $('.summernote').summernote('code', '');
    $('textarea').val('');
    $('select').val('');
    $('input[type="checkbox"]').attr('checked',false);
});
$('.pageReset').on('click',function(){
    location.reload();
});
$('.dropzone-item.edited .DeleteFiles').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var elemParent = $(this).parents('.dropzone-item');
    var id = $(this).data('area');
    var name = $(this).data('name');
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    $.ajax({
        type: 'POST',
        url: myURL+'/deleteImage',
        data:{
            '_token': $('meta[name="csrf-token"]').attr('content'),
            'id': id,
            'name': name,
        },
        success:function(data){
            if(data.status.status == 1){
                successNotification(data.status.message);
                elemParent.remove();
            }else{
                errorNotification(data.status.message);
            }
        },
    });
});

gsap.set("#moon, .star", {opacity: 0});
gsap.set("#sun, #cloud, #moon", {x: 15});
gsap.set(".star", {x: 35, y: -5});

$("#day").click(function(){
  gsap.to("#sun", 1, {x: -157, opacity: 0, ease: Power1.easeInOut});
  gsap.to("#cloud", .5, {opacity: 0, ease: Power1.easeInOut});
  gsap.to("#moon", 1, {x: -157, rotate: -360, transformOrigin: "center", opacity: 1, ease: Power1.easeInOut});
  gsap.to(".star", .5, {opacity: 1, ease: Power1.easeInOut});
  gsap.to("#night", 1, {background: "#224f6d", borderColor: "#cad4d8", ease: Power1.easeInOut});
  gsap.to("#background", 1, {background: "#0d1f2b", ease: Power1.easeInOut});
  $(this).css({"pointer-events": "none"});
  $('body').addClass('dark-theme'); 
  
  setTimeout(function(){
    $("#night").css({"pointer-events": "all"})
  }, 1000);
});

$("#night").click(function(){
  gsap.to("#sun", 1, {x: 15, opacity: 1, ease: Power1.easeInOut});
  gsap.to("#cloud", 1, {opacity: 1, ease: Power1.easeInOut});
  gsap.to("#moon", 1, {opacity: 0, x: 35, rotate: 360, transformOrigin: "center", ease: Power1.easeInOut});
  gsap.to(".star", 1, {opacity: 0, ease: Power1.easeInOut});
  gsap.to("#night", 1, {background: "#9cd6ef", borderColor: "#65c0e7", ease: Power1.easeInOut});
  gsap.to("#background", 1, {background: "#d3edf8", ease: Power1.easeInOut});
  $(this).css({"pointer-events": "none"});
  $('body').removeClass('dark-theme'); 
  
  setTimeout(function(){
    $("#day").css({"pointer-events": "all"})
  }, 1000);
});


$('.ckbox:not(.prem) input[type="checkbox"]').on('change',function(){
    if($(this).is(":checked")){
        $(this).parent('label').parent('.col').siblings('.col').find('input[type="checkbox"]').prop('checked', false);
        window.location.href = myURL.split(/[?#]/)[0]+"?category_id="+ $(this).data('area');
    }
});

$('.emoji-img').on('click',function(){
    $(this).siblings().removeClass('selected');
    $(this).parent('.imgs').siblings('input[name="rate"]').val($(this).data('area'));
    $(this).toggleClass('selected');
});

$('.addRate').on('click',function(e){
    e.preventDefault();
    e.stopPropagation();
    var id = $(this).data('area');
    var rate = $(this).siblings('input[name="rate"]').val();
    var comment = $(this).siblings('textarea').val();
    var elem = $(this);
    if(rate && comment){
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
        $.ajax({
            type: 'POST',
            url: '/helpCenter/addRate',
            data:{
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'id': id,
                'rate': rate,
                'comment': comment,
            },
            success:function(data){
                if(data.status.status == 1){
                    successNotification(data.status.message);
                    elem.siblings('.imgs').children('.emoji-img.selected').removeClass('selected');
                    elem.siblings('input[name="rate"]').val(' ');
                    elem.siblings('textarea').val(' ');
                }else{
                    errorNotification(data.status.message);
                }
            },
        });
    }
});

if($('.changeDesign').length){
    $('.changeDesign').on('click',function (e) {
        e.preventDefault();
        e.stopPropagation();
        $('.sa.d-none').removeClass('d-none').siblings('.sa').addClass('d-none');
        if($(this).children('.si').hasClass('si-grid')){
            $(this).children('.si').removeClass('si-grid').addClass('si-list');
        }else{
            $(this).children('.si').removeClass('si-list').addClass('si-grid');
        }
    });
}

if($('.buttons-colviss').length){
    $('.buttons-colviss').on('click',function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).siblings('.dt-collection.d-hidden').toggleClass('d-hidden');
    });
}

$('.permission .card-header input[type="checkbox"]').on('change',function(e){
    e.preventDefault();
    e.stopPropagation();
    if($(this).is(':checked')){
        $(this).parents('.permission').children('.card-body').find('input[type="checkbox"]').prop('checked',1);
    }else{
        $(this).parents('.permission').children('.card-body').find('input[type="checkbox"]').prop('checked',0);
    }
});

$('select[name="valid_type"]').on('change',function(){
    $('input[name="valid_value"]').val('');
    if($(this).val() == 1){
        $('.datetimepicker-inputs').datetimepicker('remove');
    }else if($(this).val() == 2){
        $('.datetimepicker-inputs').datetimepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
    }
});