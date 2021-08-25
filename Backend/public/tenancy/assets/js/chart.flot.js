$(function() {
	'use strict';
	$.plot('#flotBar1', [{
		data: [
			[0, 4],
			[1, 6],
			[2, 5],
			[3, 5],
			[4, 15],
			[5, 8],
			[6, 4],
			[7, 8],
			[8, 10],
			[9, 10]
		]
	}], {
		series: {
			bars: {
				show: true,
				lineWidth: 0,
				fillColor: '#5066e0',
				barWidth: .5
			},
			highlightColor: '#5066e0'
		},
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)',
			hoverable: true
		},
		yaxis: {
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				color: '#5f6d7a',
				size: 10
			}
		},
		xaxis: {
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				color: '#5f6d7a',
				size: 10
			}
		}
	});
	$.plot('#flotBar2', [{
		data: [
			[0, 6],
			[2, 18],
			[4, 8],
			[6, 9],
			[8, 15],
			[10, 6],
			[12, 4],
			[14, 15]
		],
		bars: {
			show: true,
			lineWidth: 0,
			fillColor: '#5066e0',
			barWidth: .8
		}
	}, {
		data: [
			[1, 15],
			[3, 7],
			[5, 10],
			[7, 17],
			[9, 9],
			[11, 8],
			[13, 7],
			[15, 5]
		],
		bars: {
			show: true,
			lineWidth: 0,
			fillColor: '#ff8c00',
			barWidth: .8
		}
	}], {
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)'
		},
		yaxis: {
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				color: '#666',
				size: 10
			}
		},
		xaxis: {
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				color: '#666',
				size: 10
			}
		}
	});
	var newCust = [
		[0, 4],
		[1, 6],
		[2, 8],
		[3, 9],
		[4, 11],
		[5, 13],
		[6, 16]
	];
	var retCust = [
		[0, 3],
		[1, 5],
		[2, 7],
		[3, 7],
		[4, 10],
		[5, 12],
		[6, 13]
	];
	var plot = $.plot($('#flotLine1'), [{
		data: newCust,
		label: 'New Customer',
		color: '#ff8c00'
	}, {
		data: retCust,
		label: 'Returning Customer',
		color: '#5066e0'
	}], {
		series: {
			lines: {
				show: true,
				lineWidth: 2
			},
			shadowSize: 0
		},
		points: {
			show: false,
		},
		legend: {
			noColumns: 1,
			position: 'nw'
		},
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)',
			hoverable: true
		},
		yaxis: {
			min: 0,
			max: 15,
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		},
		xaxis: {
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		}
	});
	var plot = $.plot($('#flotLine2'), [{
		data: newCust,
		label: 'New Customer',
		color: '#ff8c00'
	}, {
		data: retCust,
		label: 'Returning Customer',
		color: '#5066e0'
	}], {
		series: {
			lines: {
				show: true,
				lineWidth: 2
			},
			shadowSize: 0
		},
		points: {
			show: true,
		},
		legend: {
			noColumns: 1,
			position: 'ne'
		},
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)',
			hoverable: true
		},
		yaxis: {
			min: 0,
			max: 15,
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		},
		xaxis: {
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		}
	});
	var plot = $.plot($('#flotArea1'), [{
		data: newCust,
		label: 'New Customer',
		color: '#5066e0'
	}, {
		data: retCust,
		label: 'Returning Customer',
		color: '#ff8c00'
	}], {
		series: {
			lines: {
				show: true,
				lineWidth: 1,
				fill: true,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.8
					}]
				}
			},
			shadowSize: 0
		},
		points: {
			show: false,
		},
		legend: {
			noColumns: 1,
			position: 'nw'
		},
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)',
			hoverable: true
		},
		yaxis: {
			min: 0,
			max: 15,
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		},
		xaxis: {
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		}
	});
	var plot = $.plot($('#flotArea2'), [{
		data: newCust,
		label: 'New Customer',
		color: '#5066e0'
	}, {
		data: retCust,
		label: 'Returning Customer',
		color: '#ff8c00'
	}], {
		series: {
			lines: {
				show: true,
				lineWidth: 1,
				fill: true,
				fillColor: {
					colors: [{
						opacity: 0
					}, {
						opacity: 0.3
					}]
				}
			},
			shadowSize: 0
		},
		points: {
			show: true,
		},
		legend: {
			noColumns: 1,
			position: 'nw'
		},
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)',
			hoverable: true
		},
		yaxis: {
			min: 0,
			max: 15,
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		},
		xaxis: {
			color: '#eee',
			tickColor: 'rgba(171, 167, 167,0.2)',
			font: {
				size: 10,
				color: '#999'
			}
		}
	});
	/**************** PIE CHART *******************/
	var piedata = [{
		label: 'Series 1',
		data: [
			[1, 90]
		],
		color: '#5066e0'
	}, {
		label: 'Series 2',
		data: [
			[1, 60]
		],
		color: '#ff8c00'
	}, {
		label: 'Series 3',
		data: [
			[1, 20]
		],
		color: '#00d48f '
	}, {
		label: 'Series 4',
		data: [
			[1, 50]
		],
		color: '#ffc107'
	}, {
		label: 'Series 5',
		data: [
			[1, 40]
		],
		color: '#02d7ff'
	}];
	$.plot('#flotPie1', piedata, {
		series: {
			pie: {
				show: true,
				radius: 1,
				label: {
					show: true,
					radius: 2 / 3,
					formatter: labelFormatter,
					threshold: 0.1
				}
			}
		},
		grid: {
			hoverable: true,
			clickable: true
		}
	});
	$.plot('#flotPie2', piedata, {
		series: {
			pie: {
				show: true,
				radius: 1,
				innerRadius: 0.5,
				label: {
					show: true,
					radius: 2 / 3,
					formatter: labelFormatter,
					threshold: 0.1
				}
			}
		},
		grid: {
			borderWidth: 1,
			borderColor: 'rgba(171, 167, 167,0.2)',
			hoverable: true
		},
	});

	function labelFormatter(label, series) {
		return '<div style="font-size:8pt; text-align:center; padding:2px; color:white;">' + label + '<br/>' + Math.round(series.percent) + '%</div>';
	}
});