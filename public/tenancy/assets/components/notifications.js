/**
 * Notification JS
 */


function successNotification(message) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "preventDuplicates": true,
        "newestOnTop": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "2000",
        // "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "icon" : false,
    };
    toastr.success('واتس لووب | Whats Loop', message);
}

function errorNotification(message) {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "progressBar": true,
        "preventDuplicates": true,
        "newestOnTop": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "2000",
        // "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "icon" : false,
    };
    toastr.error('واتس لووب | Whats Loop', message);
}
