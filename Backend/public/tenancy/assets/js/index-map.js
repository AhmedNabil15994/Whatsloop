! function($) {
	"use strict";

	var VectorMap = function() {
	};

	VectorMap.prototype.init = function() {
		//various examples
		$('#vmap').vectorMap({
			map : 'world_mill_en',
			scaleColors : ['#e8e9f5', '#e8e9f5'],
			normalizeFunction : 'polynomial',
			hoverOpacity : 0.7,
			hoverColor : false,
			regionStyle : {
				initial : {
					fill : '#e8e9f5'
				}
			},
			 markerStyle: {
                initial: {
                    r: 9,
                    'fill': '#353d9f',
                    'fill-opacity': 0.7,
                    'stroke': '#fff',
                    'stroke-width' : 9,
                    'stroke-opacity': 0.5
                },

                hover: {
                    'stroke': '#fff',
                    'fill-opacity': 1,
                    'stroke-width': 1.5
                }
            },
			backgroundColor : 'transparent',
			markers : [
			{
				latLng: [35.861660, 104.195397],
				name: 'China',
				visits: 1000,
				fill:'#f00',
			},
			{
				latLng: [37.090240, -95.712891],
				name: 'USA(Neda Shine)',
				visits: 1000
			},
			{
				latLng: [52.130366, -92.346771],
				name: 'Canada',
				visits: 1000
			},
			{
				latLng: [-25.274398, 133.775136],
				name: 'Austrlia(Neda Shine)',
				visits: 1000
			},
			{
				latLng: [51.165691, 10.451526],
				name: 'Germany',
				visits: 1000
			},
			{
				latLng: [26.02, 50.55],
				name: 'Bahrain',
				visits: 1000
			},
			{
				latLng: [-3, -61.38],
				name: 'Brazil',
				visits: 1000
			}, {
				latLng : [0.33, 6.73],
				name : 'SÃ£o TomÃ© and PrÃ­ncipe'
			}]
		});

	},
	//init
	$.VectorMap = new VectorMap, $.VectorMap.Constructor =
	VectorMap;
	$.VectorMap.init();
}(window.jQuery);