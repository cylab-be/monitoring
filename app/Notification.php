<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Notification extends Model
{

    protected $dateFormat = 'U';


    public function server()
    {
        return $this->belongsTo('\App\Server');
    }

    public function saveAndSend()
    {
        $this->save();
        $change = $this->change();

        $mail = new \App\Mail\StatusChanged($change);
        if ($this->type == "bouncing") {
            $mail = new \App\Mail\StatusBouncing($change);
        }

        foreach ($this->server->organization->users as $user) {
            Mail::to($user)->send($mail);
        }
    }

    public function change() : StatusChange
    {
        return StatusChange::find($this->change_id);
    }

    public static function findForServer(int $server_id, int $since = 0)
    {
        return self::where('server_id', $server_id)
                ->where('created_at', '>', $since);
    }
}
