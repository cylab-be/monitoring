<?php

namespace App\Sensor;

use App\StatusChange;
use App\Notification;

/**
 * Description of ChangeAlert
 *
 * @author tibo
 */
class ChangeAlert
{
    /**
     * Maximum number of notifications sent per day, for a single server.
     */
    const NOTIFICATIONS_PER_DAY = 3;
    
    public function analyze(StatusChange $change)
    {
        // Only send a notification if server changes to status "error"
        if ($change->status < \App\Status::ERROR) {
            return;
        }
        
        $server = $change->server;
        $server_id = $server->id;

        $onedayago = time() - 24 * 3600;
        $sent_notifications_count = Notification::findForServer($server_id, $onedayago)->count();
        
        if ($sent_notifications_count > self::NOTIFICATIONS_PER_DAY) {
            // nothing to do!
            return;
        }

        $notification = new Notification();
        $notification->server()->associate($server);
        $notification->change_id = $change->id;
        
        if ($sent_notifications_count < self::NOTIFICATIONS_PER_DAY) {
            $notification->type = "change";
        } elseif ($sent_notifications_count == self::NOTIFICATIONS_PER_DAY) {
            $notification->type = "bouncing";
        }
        
        $notification->saveAndSend();
    }
}
