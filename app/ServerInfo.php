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
    public $addresses;
    public $last_record_time;
    
    private $parser;
    
    /**
     * 
     * @var Server
     */
    private $server;
    
    /**
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->parser = new ServerInfoParser();
        $this->server = $server;
        
        $this->uptime = $this->parseUptime();
        $this->uuid = $this->parseUUID();
        $this->lsb = $this->parseLsb();
        $this->manufacturer = $this->parseManufacturer();
        $this->product_name = $this->parseProductName();
        $this->cpuinfo = $this->parseCpuinfo();
        $this->memory_total = $this->parseMemoryTotal();
        $this->client_version = $this->parseClientVersion();
        $this->last_record_time = $this->parseLastRecordTime();
        
        $this->addresses = $this->parseAddresses();
    }
    /**
     * Human readable uptime.
     *
     * @return string
     */
    public function parseUptime() : string
    {
        $record = $this->server->lastRecord("upaimte");
        
        if (is_null($record)) {
            return "unknown";
        }

        return $this->parser->parseUptime($record->data);
    }
    
    public function uptime() : string
    {
        return $this->uptime;
    }

    public function parseUuid()
    {
        $record = $this->server->lastRecord("system");
        if (is_null($record)) {
            return "unknown";
        }
        
        return $this->parser->parseUUID($record->data);
    }
    
    public function uuid() : string
    {
        return $this->uuid;
    }

    public function parseCpuinfo() : array
    {
        $record = $this->server->lastRecord("cpu");
        if (is_null($record)) {
            return ["threads" => 0, "cpu" => "unknown"];
        }

        return $this->parser->parseCpuinfo($record->data);
    }
    
    public function cpuinfo()
    {
        return $this->cpuinfo;
    }
    
    public function vCores() : int
    {
        return $this->cpuinfo()["threads"];
    }

    public function memoryTotalForHumans()
    {
        return round($this->memoryTotal() / 1024 / 1000) . " GB";
    }

    /**
     *
     * @return int total memory (in KB) or 0 if not found...
     */
    public function parseMemoryTotal()
    {
        $record = $this->server->lastRecord("memory");
        if (is_null($record)) {
            return 0;
        }

        return $this->parser->parseMeminfo($record->data);
    }
    
    public function memoryTotal()
    {
        return $this->memory_total;
    }
    
    public function parseLsb()
    {
        $record = $this->server->lastRecord("lsb");
        if (is_null($record)) {
            return "unknown";
        }

        return $this->parser->parseLsb($record->data);
    }
    
    public function lsb()
    {
        return $this->lsb;
    }


    public function parseManufacturer()
    {
        $record = $this->server->lastRecord("system");
        if (is_null($record)) {
            return "unknown";
        }

        return $this->parser->parseManufacturer($record->data);
    }
    
    public function manufacturer()
    {
        return $this->manufacturer;
    }

    public function parseProductName()
    {
        $record = $this->server->lastRecord("system");
        if (is_null($record)) {
            return "unknown";
        }

        return $this->parser->parseProductName($record->data);
    }
    
    public function productName()
    {
        return $this->product_name;
    }
    
    public function parseAddresses() : array
    {
        $record = $this->server->lastRecord("ifconfig");
        if (is_null($record)) {
            return [];
        }
        
        return $this->parser->parseAddresses($record->data);
    }
    
    public function addresses() : array
    {
        return $this->addresses;
    }

    public function parseLastRecordTime() : Carbon
    {
        $record = $this->server->lastRecord("version");
        if (is_null($record)) {
            return new Carbon();
        }
        
        return Carbon::createFromTimestamp($record->time);
    }
    
    /**
     *
     * @return \Carbon\Carbon
     */
    public function lastRecordTime() : Carbon
    {
        return $this->last_record_time;
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
