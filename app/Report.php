<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Status report
 *
 * Properties set by scheduler:
 * @property integer $time
 * @property integer $server_id
 * @property Server $server
 * @property string $label
 *
 * Properties set by analysis agent:
 * @property int $status_code
 * @property string $title
 * @property string $html
 *
 * @author tibo
 */
class Report extends Model implements HasStatus
{

    public $timestamps = false;


    public function __construct(array $attributes = [])
    {
        // defautl value
        $this->status_code = -1;

        parent::__construct($attributes);
    }

    public function save(array $options = [])
    {
        if (parent::save($options)) {
            AgentScheduler::get()->notifyReport($this);
            return true;
        }

        return false;
    }

    public function record()
    {
        return $this->belongsTo(Record::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function getHtmlAttribute(string $value) : string
    {
        try {
            return gzinflate(base64_decode($value));
        } catch (\Exception $ex) {
            return "Oops :-(";
        }
    }

    public function setHtmlAttribute(string $value)
    {
        $this->attributes['html'] = base64_encode(gzdeflate($value));
    }

    // simple getter/setter helpers

    public function setTitle(string $title) : Report
    {
        $this->title = $title;
        return $this;
    }

    public function setStatus(Status $status) : Report
    {
        $this->status_code = $status->code();
        return $this;
    }

    public function setHTML(string $html) : Report
    {
        $this->html = $html;
        return $this;
    }

    public function status() : Status
    {
        return new Status($this->status_code);
    }

    public function title() : string
    {
        return $this->title;
    }

    public function time() : string
    {
        return date("Y-m-d H:i:s", $this->time);
    }
}
