<p>
    Current load: <b>{{ $current_load }}</b> |
    Max in the last 24h: <b>{{ $max_load }}</b>
</p>
<p>
    Warning threshold: <span class="text-warning font-weight-bold">{{ $warning_threshold }}</span> |
    Error threshold: <span class="text-danger font-weight-bold">{{ $error_threshold }}</span>
</p>

<canvas id="load-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {

        let ctx = document.getElementById('load-chart').getContext('2d');
        let config = {
            type: 'line',
            data: {
                datasets: [@json($dataset)]
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
                    annotations: []
                }
            }
        };
        let chart = new Chart(ctx, config);


        let annotation_warning = {
            drawTime: 'afterDraw',
            type: 'line',
            mode: 'horizontal',
            scaleID: 'y-axis-0',
            value: {{ $warning_threshold }},
            borderColor: 'orange',
            borderWidth: 2
        };
        config.options.annotation.annotations.push(annotation_warning);

        let annotation_error = {
            drawTime: 'afterDraw',
            type: 'line',
            mode: 'horizontal',
            scaleID: 'y-axis-0',
            value: {{ $error_threshold }},
            borderColor: 'red',
            borderWidth: 2
        };
        config.options.annotation.annotations.push(annotation_error);
        chart.update();
    });
</script>
