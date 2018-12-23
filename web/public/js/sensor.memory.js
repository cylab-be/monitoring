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
    var meminfo_url = window.monitorURL + "/api/sensor/"
            + window.monitorServerID + "/" + window.monitorServerToken
            + "/memory";
    $.getJSON(meminfo_url, function(data) {
        console.log(data);
        var new_dataset = {
                label: 'Used',
                backgroundColor: window.chartColors.green,
                borderColor: window.chartColors.green,
                data: data.used
            };
        config.data.datasets.push(new_dataset);

        new_dataset = {
                label: 'Cached',
                backgroundColor: window.chartColors.orange,
                borderColor: window.chartColors.orange,
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