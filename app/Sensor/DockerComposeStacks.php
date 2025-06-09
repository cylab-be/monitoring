<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;

/**
 * Description of Reboot
 *
 * @author tibo
 */
class DockerComposeStacks implements Sensor
{
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "docker_compose_stacks",
            "docker_compose_stacks",
            "List deployed docker compose stacks"
        );
    }

    public function analyze(Record $record): Report
    {
        $report = (new Report())->setTitle("Docker Compose Stacks");

        // only informative, allways ok
        $report->setStatus(Status::ok());

        //
        // [{"Name":"monitoring","Status":"running(7)","ConfigFiles":"/home/monitoring/docker-compose.yml"}]
        $stacks = json_decode($record->data);
        $report->setHTML(view("sensor.dockercomposestacks", ["stacks" => $stacks]));

        return $report;
    }
}
