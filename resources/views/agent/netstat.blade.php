<canvas id="netstat-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {
        
        let ctx = document.getElementById('netstat-chart').getContext('2d');
        let config = {
            type: 'line',
            data: {
                datasets: [@json($dataset)]
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
                                labelString: '%'
                        }
                    }]
                },
                annotation: {
                    annotations: [{
                        type: 'line',
                        mode: 'horizontal',
                        scaleID: 'y-axis-0',
                        value: 10, // 10%
                        borderColor: 'red',
                        borderWidth: 2
                    }]
                }
            }
        };
        
        let chart = new Chart(ctx, config);
    });
</script>
