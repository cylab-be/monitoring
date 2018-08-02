<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js"></script>
<p>Current load: {{ $current_load }}</p>
<canvas id="myChart" width='400' height='300'></canvas>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
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
                    }
                }]
            }
        }
    });
</script>
