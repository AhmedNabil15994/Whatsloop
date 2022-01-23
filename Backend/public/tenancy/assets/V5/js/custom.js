$(function(){
resizeMenu();
	$(".loginPage .left").height($(".loginPage .right").height());
	
	$( ".loginPage .right .center .formLogin .inputStyle.telStyle input" ).focus(function() {
	  $(this).parent().siblings('i').css("color","#00BFB5");
	});
	$( ".loginPage .right .center .formLogin .inputStyle.telStyle input" ).focusout(function() {
	  $(this).parent().siblings('i').css("color","#6D7E9F");
	});

	$(".loginPage .right .center .formLogin .inputStyle input").keyup(function() {
	    if($(this).val().length > 0) {
	         $(this).parent().addClass("active");
	    } else {
	        $(this).parent().removeClass("active");
	    }
	});

	// if($(".phone")[0]) {
	//   var input = document.querySelector(".phone");
	//     window.intlTelInput(input, {
	//        //allowDropdown: false,
	//        //autoHideDialCode: false,
	//        //autoPlaceholder: "off",
	//        //dropdownContainer: document.body,
	//       // excludeCountries: ["sn"],
	//       // formatOnDisplay: false,
	//       // geoIpLookup: function(callback) {
	//       //   $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
	//       //     var countryCode = (resp && resp.country) ? resp.country : "";
	//       //     callback(countryCode);
	//       //   });
	//       // },
	//       // hiddenInput: "full_number",
	//       // initialCountry: "auto",
	//       // localizedCountries: { 'de': 'Deutschland' },
	//       // nationalMode: false,
	//       // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
	//        //placeholderNumberType: "none",
	//       // preferredCountries: ['cn', 'jp'],
	//       // separateDialCode: true,
	//     });
 //    }
    
/************************************************************************/
	$(".header .iconMenuPc").click(function () {

		$(".header .menuPc").slideToggle();

	});

	$('body,html').on('click', function (e) {
		var container = $(".header .iconMenuPc,.header .iconMenuPc *"),
			Sub = $(".header .menuPc");


		if (!$(e.target).is(container)) {
			Sub.slideUp();
		}

	});
	
	$(".iconMenuCpanel,.cpanelStyle.activeMenu .bgOpacity").click(function () {
		if($(".menuCpanel").hasClass("active")) {
			$(".menuCpanel .linksCpanel li .subMenu").css("display","none");
			$(".menuCpanel.active .linksCpanel li .subToggle").removeClass("active");
		}
		$(".menuCpanel").toggleClass("active");
		$(".cpanelStyle").toggleClass("activeMenu");
		$("body").toggleClass("overflowH");

	});
    
	$(".linksCpanel").niceScroll({
		cursorwidth: 6,
		cursorborder: 0,
		cursorcolor: '#707d94',
		zindex: 1500,
		horizrailenabled: false

	}).resize();
    
	//$(window).load(resizeMenu);
	
	function resizeMenu() {

		if ($(window).width() < 1200) {
			$(".menuCpanel .linksCpanel li .subMenu").css("display","none");
			$(".menuCpanel").removeClass("active");
			$(".cpanelStyle").removeClass("activeMenu");
			$("body").removeClass("overflowH");

		} else {
			$(".menuCpanel").addClass("active");
			$(".cpanelStyle").addClass("activeMenu");
			$("body").addClass("overflowH");
		}

	}
    
    $(".menuCpanel .linksCpanel li .subToggle").click(function() {
		if($(".menuCpanel").hasClass("active")) {
			$(".menuCpanel.active .linksCpanel li .subMenu").not($(this).siblings()).slideUp();
			$(".menuCpanel.active .linksCpanel li .subToggle").not($(this)).removeClass("active");
			$(this).siblings().slideToggle();
    		$(this).toggleClass("active");
			
		}
		setTimeout(function() {
			$(".linksCpanel").niceScroll({
				cursorwidth: 6,
				cursorborder: 0,
				cursorcolor: '#707d94',
				zindex: 1500,
				horizrailenabled: false
		
			}).resize();
		},300);
    });

    $(".menuCpanel .linksCpanel li").hover(function() {
    	var offset = $(this).position();
    	var titlehover = $(this).attr("titlehover");
    	$(".menuCpanel .hovers  #"+titlehover  +".titleHover").css("top",offset.top + 5);
    	$(".menuCpanel .hovers #"+titlehover  +".titleHover").addClass("active");
    },function() {
    	var id = $(this).attr("titlehover");
    	$(".menuCpanel .hovers #"+id  +".titleHover").removeClass("active");
    });
    
/***************************************************************************/    
    
    $(".openProfile").click(function() {
    	
    	$(".header .profile .profileStyle").slideToggle();
    	if ($(window).width() < 1200) {
    		$("html,body").addClass("overflowH")
    	}
    });
    
    $(".header .profile .profileStyle .head .iconClose").click(function() {
    	$("body,html").removeClass("overflowH");
    	$(".header .profile .profileStyle").slideUp()
    })
    
	$('body,html').on('click', function(e) {
		var container = $(".header .profile .openProfile,.header .profile .profileStyle *,.header .profile .profileStyle,.openProfile,.openProfile *"),
		Sub = $(".header .profile .profileStyle");
		

	    if( !$(e.target).is(container)  ){
	        Sub.slideUp();
	    }

	});
    
    $(".header .btnDark").click(function() {
    	$(this).toggleClass("active");
    });
    
    
 	$(".stats .listNumbers").niceScroll({
		cursorwidth: 6,
		cursorborder: 0,
		railalign:"left",
		cursorcolor: '#DEDEDE',
		zindex: 1500,
		horizrailenabled: false

	}).resize();
    
	/****** Start scroll Top ******/
	
	var scrollButton = $(".back-top");
	
	$(window).scroll(function () {
		$(this).scrollTop() >= 100 ? scrollButton.addClass("active") : scrollButton.removeClass("active");
	});
	
    scrollButton.click(function () {
		$("html,body").animate({scrollTop : 0}, 800);
	});
	
	/****** End scroll Top ******/
    
	/********************************************/
	var circle1Val = $('.circle1').attr("val-circle");
	$('.circle1').circleProgress({
		value:circle1Val,
		thickness:10
	}).on('circle-animation-progress', function (event, progress) {
		$(this).find('strong').html('<i>%</i>' + Math.round(100 * circle1Val));
	});
	
	$('.circle2').circleProgress({
		value: 1,
		thickness:10
	}).on('circle-animation-progress', function (event, progress) {
		$(this).find('strong').html('<i>%</i>' + Math.round(100 * progress));
	});

	/********************************************/
    
    $(".categories .link").click(function() {
    	$(".categories .link").not($(this)).removeClass("active");
    	$(".categories .linkStyle .subMenu").not($(this).siblings()).slideUp("active");
    	$(this).toggleClass("active");
    	$(this).siblings().slideToggle();
    	
    });
    
	$('body,html').on('click', function(e) {
		var container = $(".categories .link,.categories .link *"),
		Sub = $(".categories .subMenu");
		

	    if( !$(e.target).is(container)  ){
	        Sub.slideUp();
	    }

	});
    
    
    $( ".selectmenu" ).selectmenu();
    
    
    
    
    
 	/****** Start Tabs ******/
	
	$(".btnsTabs li").click(function () {
		
		var myButton = $(this).attr("id"),
			parent = $(this).parent().attr("id");
		
		$(this).addClass("active").siblings().removeClass("active");
		
		$("."+parent+" .tab").hide();
		
		$("."+parent+" ." + myButton).fadeIn();

		if(myButton == 'tab2'){
			$.each($("."+parent+" ." + myButton + " div .mainquta-card .card-back .btnStyle.card-link"),function(index,item){
				var newURL = $(item).attr('href');
				if(newURL.indexOf("annual") == -1){
         			newURL = newURL+'?annual=1';
      			}	

				
				$(item).attr('href',newURL);				
			});
		}
		

	});
	
	/****** End Tabs ******/
	
	
	$(".qutas-ards .mainquta-card .card-link").click(function() {
		$(this).parents(".mainquta-card").addClass("active")
	});
	
	
	$(".header .btnDark").click(function() {
		$("body").toggleClass("dark-mode");
	});
	
	if($('#Timer').length){

		var timeLeft = 30;
		    var elem = document.getElementById('Timer');
		    
		    var timerId = setInterval(countdown, 1000);
		    
		    function countdown() {
		      if (timeLeft == 0) {
		        clearTimeout(timerId);
		        doSomething();
		      } else {
		        elem.innerHTML = timeLeft;
		        timeLeft--;
		      }
		    }
		    
	    }
	
	
	/************ 29/10/2021 *************/
	
	$(".options .openOptions").click(function() {
		$(this).siblings().slideToggle();
	});
	
	$('body,html').on('click', function(e) {
		var container = $(".options .openOptions"),
		Sub = $(".options .optionsList");
		

	    if( !$(e.target).is(container)  ){
	        Sub.slideUp();
	    }

	});
	
	
	/****** Start accordion ******/
	
	$(".accordion.active .accordion-content").css("display","block");
	
	$(".accordion .accordion-title").click(function () {
		
		var accordId = $(this).parent().parent().attr("id");
				
		$(this).next().slideToggle(500);
		
		$("#"+accordId + " .accordion .accordion-content").not($(this).next()).slideUp(500);
		
		$(this).parent().toggleClass("active").siblings().removeClass("active");
				
	});
	
	/****** End accordion ******/
	
	
	$('.flat2.fa').on('click',function(e){
		e.preventDefault();
		e.stopPropagation();
		
		if($(this).hasClass('fa-eye-slash')){
			$(this).siblings('input[type="password"]').attr('type','password');
			$(this).removeClass('fa-eye-slash');
			$(this).addClass('fa-eye');
		}else{
			$(this).siblings('input[type="password"]').attr('type','text');
			$(this).removeClass('fa-eye');
			$(this).addClass('fa-eye-slash');
		}
	});

	function disableIt(){
		document.addEventListener('contextmenu', function(e) {
		  	e.preventDefault();
		});

		document.onkeydown = function (e) {
	        if (event.keyCode == 123) {
	            return false;
	        }
	        if (e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'i'.charCodeAt(0))) {
	            return false;
	        }
	        if (e.ctrlKey && e.shiftKey && (e.keyCode == 'C'.charCodeAt(0) || e.keyCode == 'c'.charCodeAt(0))) {
	            return false;
	        }
	        if (e.ctrlKey && e.shiftKey && (e.keyCode == 'J'.charCodeAt(0) || e.keyCode == 'j'.charCodeAt(0))) {
	            return false;
	        }
	        if (e.ctrlKey && (e.keyCode == 'U'.charCodeAt(0) || e.keyCode == 'u'.charCodeAt(0))) {
	            return false;
	        }
	        if (e.ctrlKey && (e.keyCode == 'S'.charCodeAt(0) || e.keyCode == 's'.charCodeAt(0))) {
	            return false;
	        }
	    }
	}

	// disableIt();
			
});
