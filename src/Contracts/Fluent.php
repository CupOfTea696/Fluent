<?php

namespace CupOfTea\Support\Contracts;

use ArrayAccess;
use JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

interface Fluent extends ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    /**
     * Create a new Fluent Container instance.
     *
     * @param  array|object  $attributes
     * @return $this
     */
    public static function create($attributes = []);
    
    /**
     * Fill the container with the given attributes.
     *
     * @param  array|object  $attributes
     * @return $this
     */
    public function fill($attributes = []);
    
    /**
     * Check if an item or items exist in the array.
     * 
     * @param  string|array $key
     * @param  bool  $dotNotation
     * @return bool
     */
    public function has($key, $dotNotation = true);
    
    /**
     * Get an attribute from the container.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null);
    
    /**
     * Set an attribute on the container.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  bool  $dotNotation
     * @return $this
     */
    public function set($key, $value = true, $dotNotation = true);
    
    /**
     * Remove one or many items from the container.
     * 
     * @param  array|string  $keys
     * @param  bool  $dotNotation
     * @return void
     */
    public function remove($keys, $dotNotation);
    
    /**
     * Get the attributes from the container.
     *
     * @return array
     */
    public function getAttributes();
    
    /**
     * Set the attributes on the container.
     *
     * @param  array|object  $attributes
     * @return $this
     */
    public function setAttributes($attributes = []);
}
