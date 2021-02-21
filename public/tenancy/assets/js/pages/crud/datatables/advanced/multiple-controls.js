"use strict";
var KTDatatablesAdvancedMultipleControls = function() {

	var init = function() {
		var table = $('#kt_datatable');

		// begin first table
		table.DataTable({
			// DOM Layout settings
			dom:'Bfrtip',
			dom:
				"<'row'<'col-sm-12 col-md-9'l><'col-sm-12 col-md-3 text-right'Bf>>" +
				"<'row'<'col-sm-12 col-md-6'i><'col-sm-12 col-md-6'p>> " +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-12 col-md-9'l><'col-sm-12 col-md-3 text-right'f>>" +
				"<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>", // read more: https://datatables.net/examples/basic_init/dom.html
	        buttons: [
	            {
	                extend: 'colvis',
	                columns: ':not(.noVis)',
	                text: "<i class='fa fas fa-angle-down'></i> عرض الأعمدة",
	            },
	            {
	             	extend: 'print',
	             	customize: function (win) {
                       $(win.document.body).css('direction', 'rtl');     
                    },
 					'exportOptions': {
				    	columns: ':not(:last-child)',
				  	},
	         	},
	         	{
	             	extend: 'copy',
 					'exportOptions': {
				    	columns: ':not(:last-child)',
				  	},
	         	},
	         	{
	             	extend: 'excel',
 					'exportOptions': {
				    	columns: ':not(:last-child)',
				  	},
	         	},
	         	{
	             	extend: 'csv',
 					'exportOptions': {
				    	columns: ':not(:last-child)',
				  	},
	         	},
	         	{
	             	extend: 'pdf',
 					'exportOptions': {
				    	columns: ':not(:last-child)',
				  	},
	         	},
	        ],
	        oLanguage: {
				sSearch: "  البحث: ",
				sInfo: 'يتم العرض من  _START_ الي _END_ (العدد الكلي للسجلات _TOTAL_ )',
				sLengthMenu: 'عرض _MENU_ سجلات',
			}
		});
	};

	return {
		//main function to initiate the module
		init: function() {
			init();
		}
	};

}();

jQuery(document).ready(function() {
	KTDatatablesAdvancedMultipleControls.init();

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


});
