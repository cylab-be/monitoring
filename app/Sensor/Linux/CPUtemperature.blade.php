<table class='table table-sm'>
    <tr>
        <th>Name</th>
        <th class="text-right">Temperature (°C)</th>
        <th class="text-right">T°crit (°C)</th>
    </tr>

@foreach ($cpus as $CPU) 
    <tr>
        <td><b>CPU {{ $CPU->name }}</b></td>
        <td class="text-right"><b> {{ $CPU->value }}</b></td>
        <td class="text-right"><b>{{ $CPU->critvalue }}</b></td>
    </tr>
@endforeach

</table>

<canvas id="cpu-temperature-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {

        let ctx = document.getElementById('cpu-temperature-chart').getContext('2d');
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
                        ticks: {
                            beginAtZero:true
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Temperature [°C]'
                        }
                    }]
                },
                annotation: {
                    annotations: []
                }
            }
        };
        let chart = new Chart(ctx, config);

        let cpus = @json($cpus);
        let crit_values = [...new Set(cpus.map(c => c.critvalue))];
        for (let value of crit_values) {
            let annotation = {
                drawTime: 'afterDraw',
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: value,
                borderColor: 'red',
                borderWidth: 2
            };
            config.options.annotation.annotations.push(annotation);
        }
        chart.update();
    });
</script>
