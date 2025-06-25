<?php

namespace App;

use App\Jobs\RunAgent;
use App\Server;
use App\Sensor\StatusChangeDetector;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
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
     * @var Collection<Sensor>
     */
    private $sensors;

    private function __construct()
    {
        $this->sensors = $this->autodiscover();
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
     * @return Collection<Sensor>
     */
    public function sensors() : Collection
    {
        return $this->sensors;
    }

    /**
     *
     * @return Collection<Sensor>
     */
    public function autodiscover() : Collection
    {
        $ROOT = __DIR__ . "/Sensor/";
        return Collection::make(File::allFiles($ROOT))->map(function (SplFileInfo $file) {

            $interface_name = "\App\Sensor";
            $class_name = '\App\Sensor\\' . $file->getFilenameWithoutExtension();
            if (!is_a($class_name, $interface_name, true)) {
                return;
            }

            $reflection = new \ReflectionClass($class_name);
            if ($reflection->isAbstract()) {
                return;
            }

            return new $class_name;
        })->filter();
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

    public function throttlingTreshold() : int
    {
        return max(2, Server::count()) * 2 *  $this->sensors()->count();
    }

    // ------------------ SCHEDULING of agents

    public function notify(Record $record)
    {
        if (Queue::size() > $this->throttlingTreshold()) {
            return;
        }

        foreach ($this->agentsForLabel($record->label) as $agent) {
            /** @var Sensor $agent */
            RunAgent::dispatch($agent, $record);
        }
    }

    /**
     * Get analysis agents that must be triggered by this label.
     *
     * @param string $label
     * @return Collection<Sensor>
     */
    public function agentsForLabel(string $label) : Collection
    {
        return $this->sensors->filter(fn(Sensor $sensor) => $sensor->config()->trigger_label == $label);
    }

    public function agent(string $id) : Sensor
    {
        return $this->sensors->first(fn(Sensor $sensor) => $sensor->id() == $id);
    }

    public function notifySummary(ReportSummary $summary)
    {
        (new StatusChangeDetector())->analyze($summary);
    }

    public function notifyStatusChange(StatusChange $change)
    {
        (new Sensor\ChangeAlert())->analyze($change);
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
