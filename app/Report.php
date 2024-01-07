<?php

namespace App;

/**
 * Status report
 *
 * @author tibo
 */
class Report implements HasStatus
{

    private $name;
    private $status;
    private $html = "";
    
    /**
     * Name is mandatory.
     * Default status is "Unknown"
     */
    public function __construct(string $name, ?Status $status = null, ?string $html = "")
    {
        $this->name = $name;
        $this->status = $status;
        $this->html = $html;
        
        if ($status == null) {
            $this->status = Status::unknown();
        } else {
            $this->status = $status;
        }
    }
    
    public function setStatus(Status $status) : Report
    {
        $this->status = $status;
        return $this;
    }
    
    public function setHTML(string $html) : Report
    {
        $this->html = $html;
        return $this;
    }
    
    public function name() : string
    {
        return $this->name;
    }
    
    public function status() : Status
    {
        return $this->status;
    }
    
    public function html() : string
    {
        return $this->html;
    }
}
