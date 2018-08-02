<?php

namespace App\Sensor;

use \App\AbstractSensor;


class Point {
    public $t = 0;
    public $y = 0;

    public function __construct($t, $y) {
        $this->t = $t;
        $this->y = $y;
    }
}

/**
 * Description of LoadAvg
 *
 * @author tibo
 */
class LoadAvg extends AbstractSensor {




    public function report() {

        $records = $this->getLastRecords("loadavg", 288);
        $points = [];

        foreach ($records as $record) {
            $points[] = new Point($record->time * 1000, $this->parse($record->loadavg));
        }



        return "<script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.min.js\"></script>"
            . "<p>Current load: " . $this->getLastValue() . "</p>"
            . "<canvas id=\"myChart\" width='400' height='300'></canvas>"
                . "<script>"
                . "var ctx = document.getElementById('myChart').getContext('2d');"
                . "var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        datasets: [{
            label: 'Load',
            data: " . json_encode($points) . ",
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
});"
                . "</script>";
    }

    public function status() {
        return self::STATUS_OK;
    }

    public function getLastValue() {
        $record = $this->getLastRecord("loadavg");
        if ($record == null) {
            return "no data...";
        }
        $field = $record->loadavg;
        return $this->parse($field);
    }

    function parse($string) {
        return current(explode(" ", $string));
    }

    public function getLastValues() {

    }
}
