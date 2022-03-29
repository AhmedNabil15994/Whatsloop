$(function(){
	var lang = $('html').attr('lang');
	var table = $('#kt_datatable');
	var designElems = $('input[name="designElems"]').length ?  JSON.parse($('input[name="designElems"]').val()) : [];
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
		var info = 'Showing items from  _START_ to _END_ (total _TOTAL_ )';
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
		var viewText = 'View';
		var exportText = 'Export Contacts';
		var actionsVar = 'Actions';
		var detailsText = 'Details';
		var enableText = 'Enable';
		var disableText = 'Disable';
		var refreshText = 'Refresh';
	}else{
		var showCols = " عرض الأعمدة <i class='fa fas fa-angle-down'></i>";
		var direction = 'rtl';
		var search = ' البحث: ';
		var viewText = 'عرض';
		var info = 'يتم العرض من  _START_ إلى _END_ (العدد الكلي للسجلات _TOTAL_ )';
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
		var enableText = 'تفعيل';
		var disableText = 'تعطيل';
		var refreshText = 'تحديث';
	}

	var iCounter = 1;
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
					// 'orderable':false,
					render: function(data, index) {
						return iCounter++;
					}
				});
			}else{
				columnDefsVar.push({
					'targets': getIndex(index,item),
					'title' : item['label'],
					'className': item['className'],
					render: function(data, type, full, meta) {
						var labelClass = '';
						if(getIndex(index,item) == 1){
							labelClass = full.labelClass;
						}
						if(index == 'statusIDText'){
							if(full.status == 1){
								labelClass = 'label badge label-success';
							}else{
								labelClass = 'label badge label-danger';
							}
						}
						if(index == 'statusText'){
							if(full.statusText == 'مسترجع' || full.statusText == 'ملغي' || full.statusText == 'تم الالغاء'){
								labelClass = 'label badge label-light-danger';
							}
							if(full.statusText == 'تم الشحن' || full.statusText == 'تم التنفيذ' || full.statusText == 'جديد' || full.statusText == 'ترحيب بالعميل'){
								labelClass = 'label badge label-light-success';
							}
							if(full.statusText == 'تم التوصيل' || full.statusText == 'قيد التنفيذ'){
								labelClass = 'label badge label-light-warning';
							}
							if(full.statusText == 'جاهز'){
								labelClass = 'label badge label-light-primary';
							}
							if(full.statusText == 'جاري التجهيز'){
								labelClass = 'label badge label-light-info';
							}
							if(full.statusText == 'بإنتظار الدفع' ){
								labelClass = 'label badge label-default';
							}
							if(full.statusText == 'جاري التوصيل' || full.statusText == 'جارى التوصيل' || full.statusText == 'بإنتظار المراجعة'){
								labelClass = 'label badge label-light-info';
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
                            // <a class="dropdown-item" href="#"><i class="fe fe-plus mr-2"></i> Add</a>
						editButton = '<a href="/'+designElems.mainData.url+'/edit/'+data+'" class="action-icon btn btn-block btn-outline-success"> <i class="si si-note"></i> '+editText+'</a>';
					}

					if($('input[name="data-tabs"]').length && $('input[name="data-tabs"]').val() == 1){
						copyButton = '<a href="/'+designElems.mainData.url+'/copy/'+data+'" class="action-icon btn btn-block btn-outline-info"> <i class="si si-layers"></i> '+copyText+'</a>';
						showButton = '<a href="/'+designElems.mainData.url+'/changeStatus/'+data+'" class="action-icon btn btn-block btn-outline-warning"> <i class="si si-note"></i> '+(full.status == 1 ? disableText : enableText)+'</a>';
					}

					if(designElems.mainData.url == 'groupNumbers'){
						showButton = '<a href="/contacts?group_id='+full.id+'" class="action-icon btn btn-block btn-outline-info"> <i class="si si-eye"></i> '+showText+'</a>';
						if($('input[name="data-tests"]').length && $('input[name="data-tests"]').val() == 1){
							exportButton = '<a href="/contacts/export/'+data+'" class="action-icon btn btn-block btn-outline-secondary"> <i class="si si-cloud-download"></i> '+exportText+'</a>';
						}
					}

					if($('input[name="data-cols"]').val() == 1){
						deleteButton = '<a onclick="deleteItem('+data+')" class="action-icon btn btn-block btn-outline-danger"> <i class="si si-trash"></i> '+deleteText+'</a>'
					}

					if(designElems.mainData.url == 'groupMsgs' && $('input[name="data-tab"]').val() == 1){
						showButton = '<a href="/groupMsgs/view/'+full.id+'" class="action-icon btn btn-block btn-outline-info"> <i class="si si-eye"></i> '+detailsText+'</a>';
						editButton = '<a href="/groupMsgs/refresh/'+full.id+'" class="action-icon btn btn-block btn-outline-success"> <i class="si si-refresh"></i> '+refreshText+'</a>';
						deleteButton = '';
					}

					if((designElems.mainData.url == 'transfers' || designElems.mainData.name == 'whatsapp-bankTransfers') && $('input[name="data-tab"]').val() == 1){
						showButton = '<a href="/'+designElems.mainData.url+'/view/'+full.id+'" class="action-icon btn btn-block btn-outline-info"> <i class="si si-eye"></i> '+detailsText+'</a>';
						editButton = '';
					}

					if((designElems.mainData.url == 'tickets' || designElems.mainData.url == 'clients' || designElems.mainData.url == 'invoices') && $('input[name="data-tab"]').val() == 1){
						showButton = '<a href="/'+designElems.mainData.url+'/view/'+full.id+'" class="action-icon btn btn-block btn-outline-info"> <i class="si si-eye"></i> '+viewText+'</a>';
					}

					if(designElems.mainData.url == 'tickets' && $('input[name="tenant"]').val()){
                    	editButton = '';
                    	deleteButton = '';
                    }

					return '<div class="btn-group mt-4 ml-3">'+ 
                        '<a class="btn-link option-dots" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" href="#">'+
                            '<i class="fe fe-more-horizontal"></i>'+
                        '</a>'+ 
                        '<div class="dropdown-menu">'+ 
                        	editButton + copyButton + showButton + exportButton + deleteButton+
                        '</div>'+ 
                    '</div>';
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
			"<'row mg-b-25'<'views'l><'searchTable'f><'listPDF'B>>" +
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
			if(designElems.mainData.url == 'msgsArchive'){
	        	var opts = '<option value="1000">1000</option><option value="50000">50000</option>';
	        	$('select[name="kt_datatable_length"]').append(opts);
	        }
		},
		responsive: false,
		searchDelay: 500,
		processing: true,
		serverSide: true,
		ajax: {
			url: '/'+designElems.mainData.url,
			type: 'GET',
			data:function(dtParms){
				iCounter =1;
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

	$('form.searchForm input[type="text"]').on('keyup',function(t){
		t.preventDefault();
		if($('.searchTable input[name="keyword"]').length){
			$('input[name="keyword"]').val($(this).val());
		}
		DataTable.search( this.value ).draw();
    });

});