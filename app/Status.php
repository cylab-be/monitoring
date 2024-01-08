<?php

namespace App;

/**
 * Wrapper around a status code
 */
class Status
{
    const UNKNOWN = -1;
    const OK = 0;
    const WARNING = 10;
    const ERROR = 20;

    private $code;

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function __toString() : string
    {
        switch ($this->code) {
            case 0:
                return "OK";
            case 10:
                return "WARNING";
            case 20:
                return "ERROR";
            default:
                return "Unknown";
        }
    }

    public function code() : int
    {
        return $this->code;
    }

    public function badge() : string
    {
        switch ($this->code) {
            case 0:
                return '<span class="badge badge-success">OK</span>';
            case 10:
                return '<span class="badge badge-warning">WARNING</span>';
            case 20:
                return '<span class="badge badge-danger">ERROR</span>';
            default:
                return '<span class="badge badge-secondary">Unknown</span>';
        }
    }

    public function color() : string
    {
        switch ($this->code) {
            case 0:
                return 'success';
            case 10:
                return 'warning';
            case 20:
                return 'danger';
            default:
                return 'secondary';
        }
    }
    
    /**
     *
     * @param array<HasStatus> $items
     * @return Status
     */
    public static function max(array $items) : Status
    {
        if (count($items) == 0) {
            return Status::unknown();
        }
        
        return max(array_map(
            function (HasStatus $item) {
                return $item->status();
            },
            $items
        ));
    }
    
    public static function ok() : Status
    {
        return new Status(0);
    }
    
    public static function warning() : Status
    {
        return new Status(10);
    }
    
    public static function error() : Status
    {
        return new Status(20);
    }
    
    public static function unknown() : Status
    {
        return new Status(-1);
    }
}
