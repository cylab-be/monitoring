window.monitorMemChart = function(element) {
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
                            labelString: 'Memory [MB]'
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
    var meminfo_url = window.monitorURL + "/api/sensor/"
            + window.monitorServerID + "/" + window.monitorServerToken
            + "/memory";
    $.getJSON(meminfo_url, function(data) {
        var new_dataset = {
                label: 'Used',
                backgroundColor: 'rgba(0, 178, 0, 0.3)',
                borderColor: 'rgba(0, 178, 0, 0.3)',
                data: data.used
            };
        config.data.datasets.push(new_dataset);

        new_dataset = {
                label: 'Cached',
                backgroundColor: 'rgba(255, 165, 0, 0.3)',
                borderColor: 'rgba(255, 165, 0, 0.3)',
                data: data.cached
            };
        config.data.datasets.push(new_dataset);

        var new_annotation = {
                drawTime: 'afterDraw', // overrides annotation.drawTime if set
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: data.total,
                borderColor: 'red',
                borderWidth: 2
        };
        config.options.annotation.annotations.push(new_annotation);

        window.memChart.update();
    });
};