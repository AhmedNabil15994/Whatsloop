$(function(){
	var lang = $('html').attr('lang');
	var table = $('#kt_datatable');
	var designElems = JSON.parse($('input[name="designElems"]').val());
	var tableData = designElems.tableData;
	var columnsDef = [];
	var columnsVar = [];
	var columnDefsVar = [];

	var urlParams;
	(window.onpopstate = function () {
	    var match,
	        pl     = /\+/g,  // Regex for replacing addition symbol with a space
	        search = /([^&=]+)=?([^&]*)/g,
	        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
	        query  = window.location.search.substring(1);

	    urlParams = {};
	    while (match = search.exec(query))
	       urlParams[decode(match[1])] = decode(match[2]);
	})();

	function getIndex(key,val) {
		var i = 0;
		var x ;
		$.each(tableData,function(index, el) {
			if(index === key && val === el){
				x = i;
			}else{
				i++;
			}
		});
		return x;
	}

	if(lang == 'en'){
		var showCols = "Show Columns <i class='fa fas fa-angle-down'></i>";
		var direction = 'ltr';
		var search = ' Search ';
		var info = 'showing items from  _START_ to _END_ (total _TOTAL_ )';
		var lengthMenu = 'Showing _MENU_ items';
		var emptyTable = "No records found";
		var processing = "Processing";
		var infoEmpty = "No Results found";
		var rows1 = "You've choosed %d items";
		var rows2 = "You've choosed only one item";
		var prev = "<";
		var next = ">";
		var first = "First";
		var last = "Last";
		var editText = 'Edit';
		var copyText = 'Copy';
		var deleteText = 'Delete';
		var showText = 'View Contacts';
		var exportText = 'Export Contacts';
		var actionsVar = 'Actions';
		var detailsText = 'Details';
	}else{
		var showCols = "<i class='fa fas fa-angle-down'></i> عرض الأعمدة";
		var direction = 'rtl';
		var search = ' البحث: ';
		var info = 'يتم العرض من  _START_ الي _END_ (العدد الكلي للسجلات _TOTAL_ )';
		var lengthMenu = 'عرض _MENU_ سجلات';
		var emptyTable = "لا يوجد نتائج مسجلة";
		var processing = "جاري التحميل";
		var infoEmpty = "لا يوجد نتائج مسجلة";
		var rows1 = "لقد قمت باختيار %d عناصر";
		var rows2 = "لقد قمت باختيار عنصر واحد";
		var prev = "<";
		var next = ">";
		var first = "الاول";
		var last = "الاخير";
		var editText = 'تعديل';
		var copyText = 'تكرار';
		var deleteText = 'حذف';
		var showText = 'عرض الارقام';
		var exportText = 'استيراد جهات الارسال';
		var actionsVar = 'الاجراءات';
		var detailsText = 'التفاصيل';
	}

	$.each(tableData,function(index,item){
		if(index != 'actions'){
			columnsDef.push(index);
			if(item['type'] == 'date'){
				columnsVar.push({'data': index, 'type' : item['type'],});
			}else{
				columnsVar.push({'data': index,});
			}
			if(index == 'id'){
				columnDefsVar.push({
					'targets': 0,
					'title' : item['label'],
					'orderable':false,
				});
			}else{
				columnDefsVar.push({
					'targets': getIndex(index,item),
					'title' : item['label'],
					'className': item['className'],
					render: function(data, type, full, meta) {
						var labelClass = '';
						if(getIndex(index,item) == 2){
							labelClass = full.labelClass;
						}
						if(index == 'statusIDText'){
							if(full.status == 1){
								labelClass = 'badge text-muted';
							}else{
								labelClass = 'badge badge-danger';
							}
						}
						if(index == 'statusText'){
							if(full.statusText == 'مسترجع' || full.statusText == 'ملغي' || full.statusText == 'تم الالغاء'){
								labelClass = 'badge badge-danger';
							}
							if(full.statusText == 'تم الشحن' || full.statusText == 'تم التنفيذ' || full.statusText == 'جديد'){
								labelClass = 'badge badge-success';
							}
							if(full.statusText == 'تم التوصيل' || full.statusText == 'قيد التنفيذ'){
								labelClass = 'badge badge-warning';
							}
							if(full.statusText == 'جاري التوصيل' || full.statusText == 'بإنتظار المراجعة' || full.statusText == 'جاهز'){
								labelClass = 'badge badge-primary';
							}
							if(full.statusText == 'بإنتظار الدفع' || full.statusText == 'جاري التجهيز'){
								labelClass = 'badge badge-info';
							}
							if(full.statusText == 'ترحيب بالعميل' || full.statusText == 'جارى التوصيل' ){
								labelClass = 'badge badge-secondary';
							}
						}
						return '<a class="'+item['anchor-class']+' '+labelClass+'" data-col="'+item['data-col']+'" data-id="'+full.id+'">'+data+'</a>';
					},
				});
			}
		}else{
			columnDefsVar.push({
				targets: -1,
				title: actionsVar,
				orderable: false,
				render: function(data, type, full, meta) {
					var editButton = '';
					var copyButton = '';
					var showButton = '';
					var exportButton = '';
					var deleteButton = '';
					if($('input[name="data-area"]').val() == 1){
						editButton = '<a href="/'+designElems.mainData.url+'/edit/'+data+'" class="action-icon btn btn-xs btn-success"> <i class="mdi mdi-square-edit-outline"></i> '+editText+'</a>';
					}

					if($('input[name="data-tabs"]').length && $('input[name="data-tabs"]').val() == 1){
						copyButton = '<a href="/'+designElems.mainData.url+'/copy/'+data+'" class="action-icon btn btn-xs btn-info"> <i class="mdi mdi-square-edit-outline"></i> '+copyText+'</a>';
					}

					if(designElems.mainData.url == 'groupNumbers'){
						showButton = '<a href="/contacts?group_id='+full.id+'" class="action-icon btn btn-xs btn-info"> <i class="mdi mdi-eye"></i> '+showText+'</a>';
						if($('input[name="data-tests"]').length && $('input[name="data-tests"]').val() == 1){
							exportButton = '<a href="/contacts/export/'+data+'" class="action-icon btn btn-xs btn-secondary"> <i class="mdi mdi-microsoft-excel"></i> '+exportText+'</a>';
						}
					}

					if($('input[name="data-cols"]').val() == 1){
						deleteButton = '<a onclick="deleteItem('+data+')" class="action-icon btn btn-xs btn-danger"> <i class="mdi mdi-delete"></i> '+deleteText+'</a>'
					}

					if(designElems.mainData.url == 'groupMsgs' && $('input[name="data-tab"]').val() == 1){
						showButton = '<a href="/groupMsgs/view/'+full.id+'" class="action-icon btn btn-xs btn-info"> <i class="mdi mdi-eye"></i> '+detailsText+'</a>';
						editButton = '';
						deleteButton = '';
					}

					return editButton + copyButton + showButton + exportButton + deleteButton;
				},
			});
		}
	});

	if(Object.keys(tableData)[Object.keys(tableData).length-1] == 'actions'){
		columnsVar.push({'data': 'id', 'responsivePriority': -1});
	}
	
	// begin first table
	var DataTable = table.DataTable({
		// DOM Layout settings
		dom:'Bfrtip',
		dom:
			"<'row'<'col-xs-12 col-sm-6 col-md-6'l><'col-xs-12 col-sm-6 col-md-6 text-right'B>>" +
			"<'row'<'col-xs-6 col-sm-6 col-md-6'i>> " +
			"<'row'<'col-sm-12 'tr>>" +
			"<'row'<'col-xs-6 col-sm-6 col-md-6 'i><'col-xs-6 col-sm-6 col-md-6 'p>>", // read more: https://datatables.net/examples/basic_init/dom.html
        buttons: [
            {
                extend: 'colvis',
                columns: ':not(.noVis)',
                text: showCols,
            },
            {
             	extend: 'print',
             	customize: function (win) {
                   $(win.document.body).css('direction', direction);     
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
			sSearch: search,
			sInfo: info,
			sLengthMenu: lengthMenu,
			sEmptyTable: emptyTable,
			sProcessing: processing,
			sInfoEmpty: infoEmpty,
			select:{
				rows: {
                	_: rows1,
                    0: "",
                    1: rows2
                }
			},
			oPaginate: {
		      	sPrevious: prev,
		      	sNext: next,
		      	sFirst: first,
		      	sLast: last,
		    },
		},
		drawCallback: function () {
			$('.page-item').addClass('pagination-rounded');
		},
		responsive: true,
		searchDelay: 500,
		processing: true,
		serverSide: true,
		ajax: {
			url: '/'+designElems.mainData.url,
			type: 'GET',
			data:function(dtParms){
				$.each($('.m-form--fit select'),function(index,item){
			       	dtParms[$(item).attr('name')] = $(item).val();
				});
				$.each($('.m-form--fit input.datetimepicker-input'),function(index,item){
			       	dtParms[$(item).attr('name')] = $(item).val();
				});
				$.each($('.m-form--fit input.datepicker'),function(index,item){
			       	dtParms[$(item).attr('name')] = $(item).val();
				});
				if(designElems.mainData.url == 'contacts'){
					$.each(urlParams,function(index,item){
						dtParms[index] = item;
					});
				}
		        dtParms.columnsDef = columnsDef;
		        return dtParms
		    }
		},
		columns: columnsVar,
		columnDefs: columnDefsVar,
	});

	if ($("#m_search")[0]) {
	    $("#m_search").on("click", function (t) {
	        t.preventDefault();
	        var e = {};
	        $(".m-input").each(function () {
	            var a = $(this).data("col-index");
	            e[a] ? e[a] += "|" + $(this).val() : e[a] = $(this).val();
	        }), $.each(e, function (t, e) {
	            DataTable.column(t).search(e || "", !1, !1);
	        }), DataTable.table().draw(),Ladda.stopAll();
	    });
	}
	if ($("#m_reset")[0]) {
	    $("#m_reset").on("click", function (t) {
	        t.preventDefault(); 
	        $(".m-input").each(function () {
	            $(this).val(""); 
	            DataTable.column($(this).data("col-index")).search("", !1, !1);
	        });
	        $(".m-form--fit select").each(function () {
	            $(this).val(''); 
	            DataTable.column($(this).data("col-index")).search("", !1, !1);
		        $('.m-form--fit select').selectpicker('refresh')
	        });
	        DataTable.table().draw();
	    });
	}

});