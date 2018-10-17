
<canvas id="memory-chart" width='400' height='300'></canvas>
<script>
    var ctx = document.getElementById('memory-chart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                label: 'Used',
                data: {!! json_encode($used) !!},
            },{
                label: 'Cached',
                data: {!! json_encode($used_cached) !!},
            }]
        },
        options: {
            legend: {
                display: true,
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
                            labelString: 'Memory [MB]'
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
                        value: '{{ $server->memoryTotal() / 1000 }}',
                        borderColor: 'red',
                        borderWidth: 2
                }]
            }
        }
    });
</script>