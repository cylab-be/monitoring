<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Helper class to handle the array field of a Laravel model.
 *
 * Handles methods like get() and set()
 *
 * @author tibo
 */
class ArrayField
{

    /**
     *
     * @var Model
     */
    private $model;
    
    /**
     *
     * @var string
     */
    private $field;

    public function __construct(Model $model, string $field)
    {
        $this->model = $model;
        $this->field = $field;
    }
    
    /**
     * Get the array from the field, checking it's not null...
     * @return array
     */
    private function array() : array
    {
        $field = $this->field;
        return $this->model->$field ?? [];
    }
    
    /**
     * Get a value from the array, return null if key does not exist.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->getOrDefault($key, null);
    }
    
    public function getOrDefault(string $key, mixed $default)
    {
        if (! isset($this->array()[$key])) {
            return $default;
        }
        return $this->array()[$key];
    }
}
