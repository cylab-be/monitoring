<?php

namespace App;

use App\Jobs\RunAgent;

use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Implements sensor auto discovery and registration.
 *
 * Implements singleton pattern.
 *
 * @author tibo
 */
class AgentScheduler
{
    
    /**
     *
     * @var LazyCollection<Sensor>
     */
    private $sensors;
    
    // associative array
    // trigger_label => array<Sensor>
    private array $triggers;
    
    private function __construct()
    {
        $this->sensors = $this->autodiscover();
        $this->triggers = $this->register($this->sensors);
    }
    
    private static $instance;
    
    public static function get() : AgentScheduler
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     *
     * @return LazyCollection<Sensor>
     */
    public function sensors() : LazyCollection
    {
        return $this->sensors;
    }
    
    /**
     *
     * @return LazyCollection<Sensor>
     */
    public function autodiscover() : LazyCollection
    {
        $ROOT = __DIR__ . "/Sensor/";
        return LazyCollection::make(File::allFiles($ROOT))->map(function (SplFileInfo $file) {
            
            $interface_name = "\App\Sensor";
            $class_name = '\App\Sensor\\' . $file->getFilenameWithoutExtension();
            if (!is_a($class_name, $interface_name, true)) {
                return;
            }
            
            return new $class_name;
        })->filter();
    }
    
    /**
     *
     * @param LazyCollection<Sensor> $sensors
     * @return array
     */
    public function register(LazyCollection $sensors) : array
    {
        $triggers = [];
        foreach ($sensors as $sensor) {
            /** @var Sensor $sensor */
            $conf = $sensor->config();
            $trigger_label = $conf->trigger_label;
            $triggers[$trigger_label][] = $sensor;
        }
        return $triggers;
    }
    
    /**
     * Get the list of defined agent labels.
     *
     * @return array
     */
    public function agentLabel() : array
    {
        return $this->sensors->map(function (Sensor $sensor) {
            return $sensor->config()->label;
        })->toArray();
    }
    
    // ------------------ SCHEDULING of agents
    
    public function notify(Record $record)
    {
        $trigger_label = $record->label;
        
        if (! isset($this->triggers[$trigger_label])) {
            return;
        }
        
        $records = $record->server->lastRecords($trigger_label);
        if ($records->isEmpty()) {
            return;
        }
        
        foreach ($this->triggers[$trigger_label] as $agent) {
            RunAgent::dispatch($agent, $record);
        }
    }
    
    public function notifyReport(Report $report)
    {
        $server = $report->server;
        $reports = $server->lastReports();
        
        $summary = new ReportSummary();
        $summary->time = time();
        $summary->server_id = $server->id;
        $summary->setReports($reports);
        $summary->status_code = Status::max($reports)->code();
        $summary->save();
    }
}
