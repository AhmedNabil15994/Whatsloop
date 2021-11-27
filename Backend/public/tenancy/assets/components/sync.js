$(function(){
	var myData = [];
	$('.sync-item .card').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).toggleClass('selected');
		var oldNumber = parseInt($('span.num').text());
		if($(this).hasClass('selected')){
			oldNumber++;
			myData.push($(this).data('area'));
		}else{
			oldNumber--;
			var item = $(this).data('area');
			var index = myData.indexOf(item);
			if (index !== -1) {
			  myData.splice(index, 1);
			}
		}
		$('span.num').html(oldNumber);
		$('input[name="data"]').val(JSON.stringify(myData));
	});
});