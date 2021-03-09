"use strict";

const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

// Class definition
var KTWidgets = function () {
    // Private properties
    var lang = $('html').attr('lang');

    var chartData1 = [];
    var chartData1Labels = [];
    var chartData2 = [];
    var chartData2Labels = [];
    var chartData3 = [];
    var chartData3Labels = [];
    var chartData4 = [];
    var chartData4Labels = [];
    // var chartData5 = [];
    // var chartData5Labels = [];

    var chartData1Arr = JSON.parse($('input[name="chartData1"]').val());
    var chartData2Arr = JSON.parse($('input[name="chartData2"]').val());
    var chartData3Arr = JSON.parse($('input[name="chartData3"]').val());
    var chartData4Arr = JSON.parse($('input[name="chartData4"]').val());
    var counts = JSON.parse($('input[name="counts"]').val());
            
    $.each(chartData1Arr[0],function(index,item){
        chartData1Labels.push(item);
    });

    $.each(chartData1Arr[1],function(index,item){
        chartData1.push(item);
    });

    $.each(chartData2Arr[0],function(index,item){
        chartData2Labels.push(item);
    });

    $.each(chartData2Arr[1],function(index,item){
        chartData2.push(item);
    });

    $.each(chartData3Arr[0],function(index,item){
        chartData3Labels.push(item);
    });

    $.each(chartData3Arr[1],function(index,item){
        chartData3.push(item);
    });

    $.each(chartData4Arr[0],function(index,item){
        chartData4Labels.push(item);
    });

    $.each(chartData4Arr[1],function(index,item){
        chartData4.push(item);
    });

    // General Controls
    var _initDaterangepicker = function () {
        if ($('#kt_dashboard_daterangepicker').length == 0) {
            return;
        }

        var picker = $('#kt_dashboard_daterangepicker');
        var start = moment();
        var end = moment();

        function cb(start, end, label) {
            var title = '';
            var range = '';

            if ((end - start) < 100 || label == 'Today') {
                title = 'Today:';
                range = start.format('MMM D');
            } else if (label == 'Yesterday') {
                title = 'Yesterday:';
                range = start.format('MMM D');
            } else {
                range = start.format('MMM D') + ' - ' + end.format('MMM D');
            }

            $('#kt_dashboard_daterangepicker_date').html(range);
            $('#kt_dashboard_daterangepicker_title').html(title);
        }
        if(lang == 'en'){
            var direc = 'ltr';
        }else{
            var direc = 'rtl';
        }
        picker.daterangepicker({
            direction: direc,
            startDate: start,
            endDate: end,
            opens: 'left',
            applyClass: 'btn-primary',
            cancelClass: 'btn-light-primary',
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end, '');
    }

    var _demo3 = function (addNo) {
        const apexChart = "#chart_3";
        var options = {
            series: [{
                name: addNo,
                data: chartData1
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    endingShape: 'rounded'
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: chartData1Labels,
            },
            yaxis: {
                title: {
                    text: ''
                }
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val
                    }
                }
            },
            colors: [primary, success, warning]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }
    
    var _demo1 = function (editNo) {
        const apexChart = "#chart_1";
        var options = {
            series: [{
                name: editNo,
                data: chartData2
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {   
                enabled: false
            },
            stroke: {
                curve: 'straight'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: chartData2Labels,
            },
            colors: [primary]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    var _demo2 = function (fastEditNo) {
        const apexChart = "#chart_2";
        var options = {
            series: [{
                name: fastEditNo,
                data: chartData3
            }],
            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: chartData3Labels
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
            colors: [primary, success]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    var _demo5 = function (deleteNo) {
        const apexChart = "#chart_5";
        var options = {
            series: [{
                name: deleteNo,
                type: 'column',
                data: chartData4
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: [1, 1, 4]
            },
            title: {
                text: '',
                align: 'left',
                offsetX: 110
            },
            xaxis: {
                categories: chartData4Labels,
            },
            yaxis: [
                {
                    axisTicks: {
                        show: true,
                    },
                    axisBorder: {
                        show: true,
                        color: primary
                    },
                    labels: {
                        style: {
                            colors: primary,
                        }
                    },
                    title: {
                        text: "",
                        style: {
                            color: primary,
                        }
                    },
                    tooltip: {
                        enabled: true
                    }
                },
            ],
            tooltip: {
                fixed: {
                    enabled: true,
                    position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
                    offsetY: 30,
                    offsetX: 60
                },
            },
            legend: {
                horizontalAlign: 'left',
                offsetX: 40
            }
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    var _demo13 = function (totalNo,myLabels) {
        const apexChart = "#chart_13";
        var options = {
            series: counts,
            chart: {
                height: 350,
                type: 'radialBar',
            },
            plotOptions: {
                radialBar: {
                    dataLabels: {
                        name: {
                            fontSize: '22px',
                        },
                        value: {
                            fontSize: '16px',
                            formatter: function(val) {
                              return parseInt(val);
                            },  
                        },
                        total: {
                            show: true,
                            label: totalNo,
                            formatter: function (w) {
                                // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                return counts[0]+counts[1]+counts[2]+counts[3]
                            }
                        }
                    }
                }
            },
            labels: myLabels,
            colors: [success, primary, warning, danger]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    // Public methods
    return {
        init: function () {
            // General Controls
            _initDaterangepicker();
            if(lang == 'en'){
                var addNo = 'Number Of Adding';
                var editNo = "Number Of Editing";
                var fastEditNo = 'Number Of Fast Editing';
                var deleteNo = 'Number Of Deleting';
                var totalNo = 'Total Operations';
                var myLabels = ['Number Of Adding', 'Number Of Editing', 'Number Of Fast Editing', 'Number Of Deleting'];
            }else{
                var addNo = 'عدد عمليات اضافة';
                var editNo = "عدد عمليات التعديل";
                var fastEditNo = 'عدد عمليات التعديل السريع';
                var deleteNo = 'عدد عمليات الحذف';
                var totalNo = 'اجمالي العمليات';
                var myLabels = ['عدد عمليات الاضافة', 'عدد عمليات التعديل', 'عدد عمليات التعديل السريع', 'عدد عمليات الحذف'];
            }
            _demo1(editNo);
            _demo2(fastEditNo);
            _demo3(addNo);
            _demo5(deleteNo);
            _demo13(totalNo,myLabels);
        }
    }
}();

// Webpack support
if (typeof module !== 'undefined') {
    module.exports = KTWidgets;
}

jQuery(document).ready(function () {
    KTWidgets.init();

    var start = moment().subtract(29, 'days');
    var end = moment();

    $('#kt_daterangepicker_6').daterangepicker({
        buttonClasses: ' btn',
        applyClass: 'btn-success',
        cancelClass: 'btn-default',

        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        customRangeLabel: 'Custom',
        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    });

    $('#kt_daterangepicker_6').on('apply.daterangepicker', function(ev, picker) {
        var from = picker.startDate.format('YYYY-MM-DD');
        var to = picker.endDate.format('YYYY-MM-DD');
        $('.chart-form input[name="from"]').val(from)
        $('.chart-form input[name="to"]').val(to)
        $('.chart-form').submit();    
    });




});
