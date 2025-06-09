<canvas id="memory-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {
        let ctx = document.getElementById('memory-chart').getContext('2d');
        let config = {
            type: 'line',
            data: {
                datasets: @json($datasets)
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
                    annotations: [
                    {
                        type: 'line',
                        mode: 'horizontal',
                        scaleID: 'y-axis-0',
                        value: {{ $total_memory }},
                        borderColor: 'red',
                        borderWidth: 2
                    },
                    {
                        type: 'line',
                        mode: 'horizontal',
                        scaleID: 'y-axis-0',
                        value: {{ 0.9 * $total_memory}},
                        borderColor: 'orange',
                        borderWidth: 2
                    }]
                }
            }
        };
        let chart = new Chart(ctx, config);
    });
</script>
