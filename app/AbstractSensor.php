<?php

namespace App;

/**
 * Description of AbstractSensor
 *
 * @author tibo
 */
abstract class AbstractSensor implements Sensor
{

    public function name() : string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public static function getColorForStatus(int $status) : string
    {
        switch ($status) {
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
}
