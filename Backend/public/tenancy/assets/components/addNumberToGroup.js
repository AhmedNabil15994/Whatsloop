$(function(){

	var modelProps = $('input[name="modelProps"]').val();
	var lang = $('html').attr('lang');
	if(lang == 'en'){
		var storeAs = 'Store As ';
		var choose = 'Choose';
	}else{
		var storeAs = 'حفظ ك ';
		var choose = 'حدد اختيارك';
	}
	modelProps = JSON.parse(modelProps);
	var options = '<option value="0">'+choose+'</option>';
	$.each(modelProps,function(index,item){
		options+='<option value="'+index+'">'+item+'</option>';
	});
	
	function handleFile(f) {
     //Loop through files
        var reader = new FileReader();
        var name = f.name;
        reader.onload = function (e) {
            var data = e.target.result;
            var result;
            var workbook = XLSX.read(data, { type: 'binary' });
            
            var sheet_name_list = workbook.SheetNames;
            sheet_name_list.forEach(function (y) { /* iterate through sheets */
                //Convert the cell value to Json
                var roa = XLSX.utils.sheet_to_json(workbook.Sheets[y]);
                if (roa.length > 0) {
                    result = roa;
                }
            });
           //Get the result
           	$('#colData').empty();
            var oneItem = result[0];
            var cols = [];
            $.each(oneItem,function(index,item){
            	if(index != '__rowNum__'){
            		cols.push(index);
            		var selectProps =   '<select class="selectpicker" data-style="btn-outline-primary">'+options+'</select>';
					selectProps =   '<div class="form-group row mb-2">'+
										'<label class="col-3 col-form-label">'+storeAs+' :</label>'+
										'<div class="col-9">'+selectProps+'</div>'+
									'</div>';
            		var colName = 	'<div class="col-xs-12 col-md-6 border-0 mb-3">'+
            							'<li data-cols="'+index+'">'+
            								selectProps+
            								'<div class="checkbox checkbox-blue checkbox-single float-left">'+
            									'<input type="checkbox" class="colsData" value="'+index+'">'+
            									'<label></label>'+
            								'</div>'+
            								'<p>'+index+'</p>'+
            								'<div class="clearfix"></div>'+
            								'<hr>'+
            							'</li>'+
            					  	'</div>';
            		$('#colData').append(colName);
            		$('#colData select').selectpicker();
            	}
            });
            $.each(result, function(index,item){
            	for (var i = 0; i < cols.length; i++) {
            		var newElement = 	'<div class="checkbox checkbox-blue vars checkbox-single float-left">' +
            								'<input type="checkbox" name="'+cols[i]+'[]" value="'+item[cols[i]]+'">'+
            								'<label></label>'+
            							'</div>'+
            							'<p>'+item[cols[i]]+'</p>'+
        								'<div class="clearfix"></div>'+
        								'<hr>';
            		$('#colData li[data-cols="'+cols[i]+'"]').append(newElement);
            	}
            });

        };
        reader.readAsArrayBuffer(f);
    }

    $(document).on('change','#colData .selectpicker',function(){
    	var value = $(this).val();
    	$(this).parents('.form-group.row').siblings('.checkbox.vars').find('input[type="checkbox"]').attr('name',value+'[]');
    });

    $(document).on('change','input.colsData',function(){
    	if($(this).is(':checked')){
    		$(this).parents('.checkbox').siblings('.checkbox').find('input[type="checkbox"]').attr('checked',true);
    	}else{
    		$(this).parents('.checkbox').siblings('.checkbox').find('input[type="checkbox"]').attr('checked',false);
    	}
    });

	$('.kt_dropzone_1').dropzone({
	    // url: myURL + "/uploadImage",
	    url: '/',
	    acceptedFiles: ".xlsx,.csv",
	    paramName: "file", // The name that will be used to transfer the file
	    maxFiles: 1,
	    maxFilesize: 10, // MB
	    addRemoveLinks: true,
	    accept: function(file, done) {
	        handleFile(file);
		   	this.removeFile(file);
	    },
	});

});