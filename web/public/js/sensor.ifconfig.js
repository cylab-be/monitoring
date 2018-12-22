
window.monitorIfconfigChart = function(element) {
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
                    stacked: true,
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
    window.memChart = new Chart(ctx, config);

    if (typeof window.monitorURL === 'undefined') {
        window.monitorURL = "https://monitor.web-d.be";
    }
    var api_url = window.monitorURL + "/api/sensor/"
            + window.monitorServerID + "/" + window.monitorServerToken
            + "/ifconfig";
    $.getJSON(api_url, function(data) {

        $.each(data, function(key, dataset){
            // console.log(dataset);
            var new_color_name = window.colorNames[key];
            var new_color = window.chartColors[new_color_name];
            var new_dataset = {
                label: dataset.name,
                backgroundColor: new_color,
                borderColor: new_color,
                data: dataset.points
            };
            config.data.datasets.push(new_dataset);
        });

        window.memChart.update();
    });
};