<?php

namespace App\Sensor;

use App\Sensor;
use App\SensorConfig;
use App\Status;
use App\Report;
use App\Record;
use App\Server;

/**
 * Description of UnavailableCommands
 *
 * @author tibo
 */
class UnusedCommands extends Sensor
{
    #[\Override]
    public function config(): SensorConfig
    {
        return new SensorConfig(
            "unused-commands",
            "version",
            "List sensor commands that don't produce data on this device"
        );
    }
    
    //put your code here
    #[\Override]
    public function analyze(Record $record): ?Report
    {
        $report = (new Report())->setTitle("Unused commands");
        $report->setStatus(Status::ok());
        
        $unused_commands = $this->detectUnusedCommandsFor($record->server);
        $report->setHTML(view('sensor.unused_commands', ["unused_commands" => $unused_commands]));

        return $report;
    }
    
    public function detectUnusedCommandsFor(Server $server) : array
    {
        require __DIR__ . "/../Http/Controllers/Client/parameters.php";
        
        $unused_commands = [];
        # @phpstan-ignore variable.undefined
        foreach ($COMMANDS as $label => $command) {
            $count = $server->records()->where("label", $label)->count();
            
            if ($count == 0) {
                $unused_commands[$label] = $command;
            }
        }
        
        return $unused_commands;
    }
}
