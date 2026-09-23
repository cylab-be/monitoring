<table class="table table-striped table-sm">
    <tr>
        <th>Name</th>
        <th>Address</th>
    </tr>

    @foreach ($interfaces as $interface)
    <tr>
        <td>{{ $interface->name }}</td>
        <td>{{ implode(" ", $interface->addresses) }}</td>
    </tr>
    @endforeach
</table>

<canvas id="ifconfig-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {

        let ctx = document.getElementById('ifconfig-chart').getContext('2d');
        let config = {
            type: 'line',
            data: {
                datasets: []
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
                                labelString: '[Kbits / sec]'
                        }
                    }]
                },
                annotation: {
                    annotations: []
                }
            }
        };

        let chart = new Chart(ctx, config);
        let datasets = @json($points);
        datasets.forEach(function(dataset, key){
            config.data.datasets.push(dataset);
        });
        chart.update();
    });
</script>
