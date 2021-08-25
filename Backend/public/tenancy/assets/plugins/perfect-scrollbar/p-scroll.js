(function($) {
	"use strict";	
	if($('.Notification-scroll').length){
		//P-scrolling
		const ps3 = new PerfectScrollbar('.Notification-scroll', {
		  useBothWheelAxes:true,
		  suppressScrollX:true,
		});
	}	
})(jQuery);