window.monitorLoadChart = function(element) {

    var ctx = element.getContext('2d');
    var config = {
        type: 'line',
        data: {
            datasets: []
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
                    },
                    scaleLabel: {
                        display: true,
                        labelString: 'Load'
                    }
                }]
            },
            annotation: {
                annotations: []
            }
        }
    };

    window.loadChart = new Chart(ctx, config);

    if (typeof window.monitorURL === 'undefined') {
        window.monitorURL = "https://monitor.web-d.be";
    }
    var load_url = window.monitorURL + "/api/sensor/"
            + window.monitorServerID + "/" + window.monitorServerToken
            + "/load";
    $.getJSON(load_url, function( data ) {
        var new_dataset = {
                label: 'Load',
                data: data.points
            };
        config.data.datasets.push(new_dataset);

        var new_annotation = {
                drawTime: 'afterDraw', // overrides annotation.drawTime if set
                type: 'line',
                mode: 'horizontal',
                scaleID: 'y-axis-0',
                value: data.max,
                borderColor: 'red',
                borderWidth: 2
        };
        config.options.annotation.annotations.push(new_annotation);
        window.loadChart.update();
    });
};