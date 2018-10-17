<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"></script>
<p>Current load: {{ $current_load }}</p>

<canvas id="load-chart" width='400' height='300'></canvas>
<script>
    var ctx = document.getElementById('load-chart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                label: 'Load',
                data: {!! json_encode($points) !!},
            }]
        },
        options: {
            legend: {
                display: false,
            },
            scales: {
                xAxes: [{
                    type: 'time',
                    display: true,
                    scaleLabel: {
                            display: true,
                            labelString: 'Time'
                    }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Load'
                    }
                }]
            },
            annotation: {
		// Defines when the annotations are drawn.
		// This allows positioning of the annotation relative to the other
		// elements of the graph.
		//
		// Should be one of: afterDraw, afterDatasetsDraw, beforeDatasetsDraw
		// See http://www.chartjs.org/docs/#advanced-usage-creating-plugins
		drawTime: 'afterDatasetsDraw', // (default)

		// Array of annotation configuration objects
		// See below for detailed descriptions of the annotation options
		annotations: [{
			drawTime: 'afterDraw', // overrides annotation.drawTime if set
                        type: 'line',
                        mode: 'horizontal',
                        scaleID: 'y-axis-0',
                        value: '{{ $server->cpuinfo()["threads"] }}',
                        borderColor: 'red',
                        borderWidth: 2
                }]
            }
        }
    });
</script>
