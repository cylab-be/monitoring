<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @property Organization $organization
 */
class Subnet extends Model
{
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
    
    
    /**
     * Servers connected to this subnet.
     * @return Collection<Server>
     */
    public function servers() : Collection
    {
        $connected = new Collection();
        
        $servers = $this->organization->servers;
        foreach ($servers as $server) {
            $addresses = $server->info->addresses;
            foreach ($addresses as $address) {
                if ($this->hasAddress($address)) {
                    $connected->push($server);
                }
            }
        }
        
        return $connected;
    }
    
    /**
     * Test if the address belongs to this subnet.
     * @param string $ip
     * @return bool
     */
    public function hasAddress(string $ip) : bool
    {
        $ip = ip2long($ip);
        $subnet = ip2long($this->address);
        $mask = -1 << (32 - $this->mask);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }
    
    public function url() : string
    {
        return route("subnets.show", ["subnet" => $this]);
    }
    
    public function toCytoscape() : array
    {
        $r = [];
        
        foreach ($this->servers() as $server) {
            $r[] = ["data" => [
                    "id" => rand(),
                    "source" => $server->cytoId(),
                    "target" => $this->cytoId()]];
        }
        
        $r[] = [
            "data" => [
                "id" => $this->cytoId(),
                "label" => $this->name,
                "url" => $this->url(),
                "type" => "subnet"],
            ];
        
        return $r;
    }
    
    /**
     * Get a unique ID usable in Cytoscape.
     * @return string
     */
    public function cytoId() : string
    {
        return "#subnet-" . $this->id;
    }
}
