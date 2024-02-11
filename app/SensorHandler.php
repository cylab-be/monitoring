<?php

namespace App;

use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Implements sensor auto discovery and registration.
 *
 * Implements singleton pattern.
 *
 * @author tibo
 */
class SensorHandler
{
    private array $sensors;
    
    private function __construct()
    {
        $this->sensors = $this->autodiscover();
    }
    
    private static $instance;
    
    public static function get() : SensorHandler
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    public function sensors() : array
    {
        return $this->sensors;
    }
    
    public function autodiscover() : array
    {
        $ROOT = __DIR__ . "/Sensor/";
        return LazyCollection::make(File::allFiles($ROOT))->map(function (SplFileInfo $file) {
            
            $interface_name = "\App\Sensor";
            $class_name = '\App\Sensor\\' . $file->getFilenameWithoutExtension();
            if (!is_a($class_name, $interface_name, true)) {
                return;
            }
            
            return new $class_name;
        })->filter()->toArray();
    }
}
