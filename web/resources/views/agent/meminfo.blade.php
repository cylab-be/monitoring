
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
            }
        }
    });
</script>