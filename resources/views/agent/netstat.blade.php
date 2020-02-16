<canvas id="netstat-chart" width='400' height='300'></canvas>
<script>
    window.addEventListener('load', function() {
        var element = document.getElementById('netstat-chart');
        var ctx = element.getContext('2d');
        var config = {
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
                                labelString: '%'
                        }
                    }]
                },
                annotation: {
                    annotations: []
                }
            }
        };
        window.netstatChart = new Chart(ctx, config);
        if (typeof window.monitorURL === 'undefined') {
            window.monitorURL = "https://monitor.web-d.be";
        }
        var api_url = window.monitorURL + "/api/sensor/"
                + window.monitorServerID + "/" + window.monitorServerToken
                + "/netstat";
        $.getJSON(api_url, function(data) {

            $.each(data, function(key, dataset){
                // console.log(dataset);
                var new_color_name = window.colorNames[key];
                var new_color = window.chartColors[new_color_name];
                var new_dataset = {
                    label: dataset.name,
                    backgroundColor: "rgba(255, 255, 255, 0.0)", // transparent
                    borderColor: new_color,
                    data: dataset.points
                };
                config.data.datasets.push(new_dataset);
            });

            window.netstatChart.update();
        });
    });
</script>