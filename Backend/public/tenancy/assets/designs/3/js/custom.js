const splash = 	document.querySelector(".splash");

document.addEventListener("DOMContentLoaded",(e)=> {
	setTimeout(()=> {
		splash.classList.add("displayNone");
	})
});

$(function(){

	
	$(document).on('click',"#selectCountry li,#formSelect2 li,#selectForm3 li",function() {
		$(this).siblings().removeClass("active")
		if($(this).hasClass("active")) {
			$(this).removeClass("active");
		} else {
			$(this).addClass("active");
		}
	});
	
	$('#selectForm3 .checkStyle input').on('change', function() {
	    $('.checkStyle input').not(this).prop('checked', false);  
	    $(this).parent().addClass("active").siblings().removeClass("active");
	});
	
	
	$(".selectForm .save,.inputStyle .selectCircle li").click(function() {
		var idParent = $(this).parent(".selectForm").attr("id");
		
		if($("#"+ idParent+" li").hasClass("active")) {
			var text = $("#"+ idParent+" li.active .checkStyle .text").text();
			
			$("."+ idParent+" input").val(text);
		} else {
			var text = "";
			$("."+ idParent+" input").val(text);
		}
	});
	
	$(".openSelect").click(function() {
		$(".formInfo .inputStyle .selectCircle").slideToggle();
	});
	
	
	$('body,html').on('click', function(e) {
		var container = $(".openSelect,.formInfo .inputStyle .selectCircle"),
		Sub = $(".formInfo .inputStyle .selectCircle");
		

	    if( !$(e.target).is(container)  ){
	        Sub.slideUp();
	    }

	});


	  var OwlBrands = $('#OwlBrands');
	 
	  OwlBrands.owlCarousel({
	    stagePadding:0,
	    loop:true,
	    margin:0,
	    nav:false,
	    dots:false,
	    autoplay:true,
	    responsive:{
	        0:{
	            items:3,
	            stagePadding: 20,
	            margin:10
	        }
	    }
	  });
	
	
    $(".Count .plus").click(function () {
    	var idParent = $(this).parents(".Count").attr("id");
    	var x = $("#"+ idParent +" strong").text();
    	
    	x++;
    	$("#"+ idParent +" strong").text(x);
    });
    
    $(".Count .minus").click(function () {
		var idParent = $(this).parents(".Count").attr("id");
		var x = $("#"+ idParent +" strong").text();
		
		if(x > 1) {
			x--;
			$("#"+ idParent +" strong").text(x);
		}
    });
	
	
	$(".products .items .item .dates .openDateTitle").click(function() {
		$(this).toggleClass("active");
		$(this).siblings().slideToggle();
	});
	
  $(".selectTime li").click(function() {
		if($(this).hasClass("active")) {
			$(this).removeClass("active");
		} else {
			$(this).addClass("active");
		}
  });
	
	if($("#openDateFirst")[0]) {
		 $('#openDateFirst').dateRangePicker({
			inline:true,
			container: '#date-range-container',
			alwaysOpen:true,
			singleMonth: true,
			singleDate : false,
			showTopbar: false
		});
	}	
	
	if($("#openDateFirst2")[0]) {
		 $('#openDateFirst2').dateRangePicker({
			inline:true,
			container: '#date-range-container2',
			alwaysOpen:true,
			singleMonth: true,
			singleDate : false,
			showTopbar: false
		});
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
});