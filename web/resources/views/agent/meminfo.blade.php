
<canvas id="memory-chart" width='400' height='300'></canvas>
<script>
    window.chartColors = {
	red: 'rgba(255, 99, 132, 0.2)',
	orange: 'rgba(255, 165, 0, 0.3)',
        yellow: 'rgba(255, 205, 86, 0.2)',
        green: 'rgba(0, 178, 0, 0.3)',
        blue: 'rgba(54, 162, 235, 0.2)',
        purple: 'rgba(153, 102, 255, 0.2)',
        grey: 'rgba(201, 203, 207, 0.2)'
    };
    var ctx = document.getElementById('memory-chart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: [{
                label: 'Used',
                backgroundColor: window.chartColors.green,
		borderColor: window.chartColors.green,
                data: {!! json_encode($used) !!},
            },{
                label: 'Cached',
                backgroundColor: window.chartColors.orange,
		borderColor: window.chartColors.orange,
                data: {!! json_encode($cached) !!},
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
                    stacked: true,
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