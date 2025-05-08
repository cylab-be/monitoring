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
}
