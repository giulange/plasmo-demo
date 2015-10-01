    $(function () {
       /**
        * In order to synchronize tooltips and crosshairs, override the 
        * built-in events with handlers defined on the parent element.
        */
        $('#container').bind('mousemove touchmove', function (e) {
          var chart,
            point,
            i;

          for (i = 0; i < Highcharts.charts.length; i++) {
            chart = Highcharts.charts[i];
            e = chart.pointer.normalize(e); // Find coordinates within the chart
            point = chart.series[0].searchPoint(e, true); // Get the hovered point

            if (point) {
                point.onMouseOver(); // Show the hover marker
                chart.tooltip.refresh(point); // Show the tooltip
                chart.xAxis[0].drawCrosshair(e, point); // Show the crosshair
            }
          }
        });
       /**
        * Override the reset function, we don't need to hide the tooltips and crosshairs.
        */
        Highcharts.Pointer.prototype.reset = function () {};
       /**
        * Synchronize zooming through the setExtremes event handler.
        */
        function syncExtremes(e) {
          var thisChart = this.chart;

          Highcharts.each(Highcharts.charts, function (chart) {
            if (chart !== thisChart) {
                if (chart.xAxis[0].setExtremes) { // It is null while updating
                    chart.xAxis[0].setExtremes(e.min, e.max);
                }
            }
          });
        }

        // Get the data. The contents of the data file can be viewed at 
        // https://github.com/highslide-software/highcharts.com/blob/master/samples/data/activity.json
        //$.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=activity.json&callback=?', function (activity) {
        //$.getJSON('http://143.225.214.136/wapp/json_orig.php', function (activity) {
        $.getJSON('http://143.225.214.136/wapp/wnodplot/json_idata.php', function (activity) {
          $.each(activity.datasets, function (i, dataset) {

            // Add X values
            dataset.data = Highcharts.map(dataset.data, function (val, i) {
                return [activity.xData[i], val];
            });

            $('<div class="chart">')
                .appendTo('#container')
                .highcharts({
                    chart: {
                        marginLeft: 40, // Keep all charts left aligned
                        spacingTop: 20,
                        spacingBottom: 20
                        //zoomType: 'x',
                        // pinchType: null // Disable zoom on touch devices
                    },
                    title: {
                        text: dataset.name,
                        align: 'left',
                        margin: 0,
                        x: 30
                    },
                    credits: {
                        enabled: false// prints Highcharts.com I have to change to geoprocservice.com
                    },
                    legend: {
                        enabled: false
                    },
                    xAxis: {
                        crosshair: true,
                        events: {
                            setExtremes: syncExtremes
                        },
/*
                        type: 'datetime',
                        dateTimeLabelFormats: { // don't display the dummy year
                            //day: '%H:%M, %a %d of %b, %Y',
                            day: '%d of %b %y',
                            //month: '%b %e, %Y',//'%e. %b',
                            year: '%Y'
                        },
*/

                       labels: {
                            format: '{value}'
                        },

                        title: {
                            text: 'Date'
                        }
                    },
                    yAxis: {
                        title: {
                            text: null
                        }
                    },
                    tooltip: {
                        positioner: function () {
                            return {
                                x: this.chart.chartWidth - this.label.width, // right aligned
                                y: -1 // align to title
                            };
                        },
                        borderWidth: 0,
                        backgroundColor: 'none',
                        pointFormat: '{point.y}',
                        headerFormat: '',
                        shadow: false,
                        style: {
                            fontSize: '18px'
                        },
                        valueDecimals: dataset.valueDecimals
                    },
                    series: [{
                        data: dataset.data,
                        name: dataset.name,
                        type: dataset.type,
                        color: Highcharts.getOptions().colors[i],
                        fillOpacity: 0.3,
                        tooltip: {
                            valueSuffix: ' ' + dataset.unit
                        },
                        //pointInterval: 24 * 3600 * 1000
                    }]
                });//.highcharts({
          });//$.each
        });//$.getJSON
    });//$(function () {
