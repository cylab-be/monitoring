<table class='table table-sm'>
<tr>
    <th>Disk</th>
    <th class="text-right">Activity</th>
</tr>
@foreach ($values as $disk => $value)
<tr>
    <td>{{ $disk }}</td>
    <td class="text-right">
        {{ $value }}%
    </td>
</tr>
@endforeach
</table>

<canvas id="diskactivity-chart" width='400' height='200'></canvas>
<script>
    window.addEventListener('load', function() {

        let ctx = document.getElementById('diskactivity-chart').getContext('2d');
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
                                labelString: 'Disk activity [%]'
                        }
                    }]
                },
                annotation: {
                    annotations: []
                }
            }
        };
        let chart = new Chart(ctx, config);
    });
</script>