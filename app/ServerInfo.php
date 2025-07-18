<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * @property Server $server
 * @property int $uptime
 * @property string $uuid
 * @property string $lsb
 * @property string $manufacturer
 * @property string $product
 * @property int $memory in kB
 * @property string $client_version
 * @property array $cpuinfo
 * @property array $addresses
 * @property string $kernel
 */
class ServerInfo extends Model
{
    protected $casts = [
        'cpuinfo' => 'array',
        'addresses' => 'array',
    ];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function cpuName() : string
    {
        return $this->cpuinfo["name"] ?? "unknown";
    }

    public function vCores() : int
    {
        return $this->cpuinfo["threads"] ?? 0;
    }

    public function memoryTotalForHumans() : string
    {
        return round($this->memory / 1024 / 1000) . " GB";
    }

    public function uptimeForHumans() : string
    {
        return Carbon::now()->subSeconds($this->uptime)->diffForHumans(null, CarbonInterface::DIFF_ABSOLUTE);
    }

    /**
     * Full list of IP addresses : manual IPs + autodetected
     * @return array
     */
    public function addresses() : array
    {
        $addresses = $this->addresses;

        // append manually defined IP addresses
        foreach ($this->server->ips as $ip) {
            $addresses[] = $ip->ip;
        }
        return $addresses;
    }
}
