<?php

namespace App;

use App\Jobs\RunAgent;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Collection;
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
            /** @var Sensor $agent */
            logger()->info("Dispatch agent " . $agent->config()->label .
                    " for server #" . $record->server_id);
            RunAgent::dispatch($agent, $record);
            logger()->info("Queue size: " . Queue::size());
        }
    }
    
    public function notifyReport(Report $report)
    {
        $server = $report->server;
        $reports = $this->lastReportsOf($server);
        
        $summary = new ReportSummary();
        $summary->time = time();
        $summary->server_id = $server->id;
        $summary->setReports($reports);
        $summary->status_code = Status::max($reports)->code();
        $summary->save();
    }
    
    /**
     * Get the last report for each label.
     *
     * @return Collection<Report> last report for each label
     */
    public function lastReportsOf(Server $server) : Collection
    {
        $reports = new Collection();
        foreach ($this->agentLabel() as $label) {
            $reports->push($this->lastReportOf($server, $label));
        }
        return $reports->filter();
    }
    
    public function lastReportOf(Server $server, string $label) : ?Report
    {
        $start = time() - 24 * 3600;
        return $server->reports()
                ->where("label", $label)
                ->where("time", ">", $start)
                ->orderByDesc("id")
                ->first();
    }
}
