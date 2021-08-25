$(function() {
	'use strict'
	var options = {
		series: [{
			name: 'Online Revenue',
			data: [1, 40, 18, 51, 22, 109, 0]
		}, {
			name: 'Offline Revenue',
			data: [1, 20, 55, 12, 84, 22, 4]
		}],
		chart: {
			height: 350,
			type: 'area'
		},
		colors: ['#5066e0', '#00d48f'],
		dataLabels: {
			enabled: false
		},
		stroke: {
			curve: 'smooth'
		},
		xaxis: {
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun','Jul'],
			yAxisIndex: 0,
		},
		yaxis: {
			labels: {
				formatter: function(val, index) {
					return Math.round(val);
				}
			}
		},
		grid: {
			show: true,
			borderColor: 'rgba(224, 227, 247, 0.5)',
			strokeDashArray: 0,
		},
		legend: {
          show: false,
		},
		markers: {
			size: 0,
			style: 'hollow',
		},
	};
	var chart = new ApexCharts(document.querySelector("#chart"), options);
  if($('#chart').length){
    chart.render();
  }
	
	var options1 = {
          series: [76, 67, 61, 90],
          chart: {
            height:350,
            type: 'radialBar',
        },
        plotOptions: {
          radialBar: {
            offsetY: 0,
            startAngle: -120,
            endAngle: 120,
            hollow: {
              margin: 10,
              size: '50%',
              background: 'transparent',
              image: undefined,
            },
			track: {
              show: true,
              startAngle: undefined,
              endAngle: undefined,
              background: '#dce0f8',
              strokeWidth: '50%',
              opacity: 1,
              margin: 5, 
			},
            dataLabels: {
              name: {
                show: false,
              },
              value: {
                show: false,
              }
            }
          }
        },
		stroke: {
    lineCap: "round",
  },
        colors: ['#5066e0', '#00d48f', '#ff8c00', '#2d97ff'],
        labels: ['Travel', 'Presentation', 'Business', 'Others'],
        legend: {
          show: false,
          floating: true,
          fontSize: '16px',
          position: 'left',
          offsetX: 10,
          offsetY: 15,
          labels: {
            useSeriesColors: true,
          },
          markers: {
            size: 0
          },
          itemMargin: {
            vertical: 3
          }
        },
        responsive: [{
          breakpoint: 480,
          options2: {
            legend: {
                show: false
            }
          }
        }]
        };

        var chart1 = new ApexCharts(document.querySelector("#chart2"), options1);
        if($('#chart2').length){
          chart1.render();
        }
});