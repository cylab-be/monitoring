

require('chart.js');
require('chartjs-plugin-annotation');

/*
 * Common stuff for all sensors
 */

window.chartColors = {
    red: 'rgba(255, 99, 132, 0.2)',
    orange: 'rgba(255, 165, 0, 0.3)',
    yellow: 'rgba(255, 205, 86, 0.2)',
    green: 'rgba(0, 178, 0, 0.3)',
    blue: 'rgba(54, 162, 235, 0.2)',
    purple: 'rgba(153, 102, 255, 0.2)',
    grey: 'rgba(201, 203, 207, 0.2)'
};

window.colorNames = Object.keys(window.chartColors);

/*
 * Disk evolution graphing
 */
window.loadDiskEvolutionChart = function(element) {
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
                        beginAtZero:true,
                        labelString: 'Usage [%]'
                    },
                }]
            },
            annotation: {
                annotations: []
            }
        }
    };
    window.diskEvolutionChart = new Chart(ctx, config);
    if (typeof window.monitorURL === 'undefined') {
        window.monitorURL = "https://monitor.web-d.be";
    }
    var api_url = window.monitorURL + "/api/sensor/"
            + window.monitorServerID + "/" + window.monitorServerToken
            + "/diskevolution";
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

        window.diskEvolutionChart.update();
    });
};
