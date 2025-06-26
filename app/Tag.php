<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 *
 * @property Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Server[] $servers
 */
class Tag extends Model
{
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class);
    }

    /**
     * Create Ansible inventory, in INI format
     * @return string
     */
    public function toAnsibleInventory() : string
    {
        // ansible does not allow - . or space in group names
        // https://docs.ansible.com/ansible/latest/playbook_guide/playbooks_variables.html#valid-variable-names
        $inv = "[" . Str::slug($this->name, "_") . "]\n";
        foreach ($this->servers as $server) {
            /** @var Server $server */
            $inv .= "# " . $server->name . "\n";
            foreach ($server->addresses() as $ip) {
                $inv .= $ip . "\n";
            }
        }
        return $inv;
    }
}
