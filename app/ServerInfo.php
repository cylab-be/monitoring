<?php

namespace App;

use Carbon\Carbon;

/**
 * Parse a record and store info about the server.
 */
class ServerInfo
{
    
    public $uptime;
    public $uuid;
    public $cpuinfo;
    public $memory_total;
    public $client_version;
    public $lsb;
    public $manufacturer;
    public $product_name;
    
    private $parser;
    private $record;
    
    /**
     * @param Record $record
     */
    public function __construct(Record $record)
    {
        $this->parser = new ServerInfoParser();
        $this->record = $record;
        
        $this->uptime = $this->parseUptime();
        $this->uuid = $this->parseUUID();
        $this->lsb = $this->parseLsb();
        $this->product_name = $this->parseProductName();
        $this->cpuinfo = $this->parseCpuinfo();
        $this->memory_total = $this->parseMemoryTotal();
        $this->client_version = $this->parseClientVersion();
    }
    /**
     * Human readable uptime.
     *
     * @return string
     */
    public function parseUptime() : string
    {
        if (! isset($this->record->data["upaimte"])) {
            return "unknown";
        }

        return $this->parser->parseUptime($this->record->data["upaimte"]);
    }
    
    public function uptime() : string
    {
        return $this->uptime;
    }

    public function parseUuid()
    {
        if (! isset($this->record->data["system"])) {
            return "unknown";
        }
        
        return $this->parser->parseUUID($this->record->data["system"]);
    }
    
    public function uuid() : string
    {
        return $this->uuid;
    }

    public function parseCpuinfo() : array
    {
        if (! isset($this->record->data["cpu"])) {
            return ["threads" => 0, "cpu" => "unknown"];
        }

        return $this->parser->parseCpuinfo($this->record->data["cpu"]);
    }
    
    public function cpuinfo()
    {
        return $this->cpuinfo;
    }

    public function memoryTotalForHumans()
    {
        return round($this->memoryTotal() / 1024 / 1024) . " GB";
    }

    /**
     *
     * @return int total memory (in KB) or 0 if not found...
     */
    public function parseMemoryTotal()
    {
        if (! isset($this->record->data["memory"])) {
            return 0;
        }

        return $this->parser->parseMeminfo($this->record->data["memory"]);
    }
    
    public function memoryTotal()
    {
        return $this->memory_total;
    }
    
    public function parseLsb()
    {
        if (! isset($this->record->data["lsb"])) {
            return "unknown";
        }

        return $this->parser->parseLsb($this->record->data["lsb"]);
    }
    
    public function lsb()
    {
        return $this->lsb;
    }


    public function parseManufacturer()
    {
        if (! isset($this->record->data["system"])) {
            return "unknown";
        }

        return $this->parser->parseManufacturer($this->record->data["system"]);
    }
    
    public function manufacturer()
    {
        return $this->manufacturer;
    }

    public function parseProductName()
    {
        if (! isset($this->record->data["system"])) {
            return "unknown";
        }

        return $this->parser->parseProductName($this->record->data["system"]);
    }
    
    public function productName()
    {
        return $this->product_name;
    }

    /**
     *
     * @return \Carbon\Carbon
     */
    public function lastRecordTime() : Carbon
    {
        return Carbon::createFromTimestamp($this->record->time);
    }

    public function parseClientVersion() : string
    {
        // $sensor = new \App\Sensor\ClientVersion();
        // return $sensor->installedVersion([$this->record]);
        return "";
    }
    
    public function clientVersion() : string
    {
        return $this->client_version;
    }
}
