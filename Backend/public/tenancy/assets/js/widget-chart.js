/* Chart Donut */
    if ($("#chart-donut").length) {
            var doughnutChartCanvas = $("#chart-donut").get(0).getContext("2d");
            var doughnutPieData = {
                datasets: [{
                    data: [70, 30,40],
                    backgroundColor: ['#5066e0', '#fa5c7c','#02d7ff'],
                    borderColor: ['#5066e0', '#fa5c7c','#02d7ff'],
                }],
                labels: [
                    'Local',
                    'Domestic',
                    'International',
                ]
            };
            var doughnutPieOptions = {
                cutoutPercentage: 80,
                animationEasing: "easeOutBounce",
                animateRotate: true,
                animateScale: false,
                responsive: true,
                maintainAspectRatio: true,
                showScale: true,
                legend: {
                    display: false
                },
                layout: {
                    padding: {
                        left: 0,
                        right: 0,
                        top: 0,
                        bottom: 0
                    }
                }
            };
            var doughnutChart = new Chart(doughnutChartCanvas, {
                type: 'doughnut',
                data: doughnutPieData,
                options: doughnutPieOptions
            });
        }