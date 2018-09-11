<?php
namespace CupOfTea\Support;

use Countable;
use ArrayIterator;
use IteratorAggregate;
use Illuminate\Support\Arr;
use CupOfTea\Package\Package;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Fluent as IlluminateFluent;
use CupOfTea\Support\Contracts\Fluent as FluentContract;

class Fluent extends IlluminateFluent implements FluentContract, Countable, IteratorAggregate
{
    use Package;
    
    /**
     * Package Name.
     *
     * @const string
     */
    const PACKAGE = 'CupOfTea/Fluent';
    
    /**
     * Package Version.
     *
     * @const string
     */
    const VERSION = '1.3.2';
    
    /**
     * All of the attributes set on the container.
     *
     * @var array
     */
    protected $attributes = [];
    
    /**
     * Create a new Fluent Container instance.
     *
     * @param  array|object  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        $this->fill($attributes);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function create($attributes = [])
    {
        return new static($attributes);
    }
    
    /**
     * {@inheritdoc}
     */
    public function fill($attributes = [])
    {
        $this->attributes = [];
        
        if ($attributes instanceof Arrayable) {
            $attributes = $attributes->toArray();
        }
        
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function has($key, $dotNotation = true)
    {
        if ($dotNotation) {
            return Arr::has($this->attributes, $key);
        }
        
        return Arr::exists($this->attributes, $key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null, $dotNotation = true)
    {
        if ($dotNotation) {
            return Arr::get($this->attributes, $key, $default);
        }
        
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        return value($default);
    }
    
    /**
     * {@inheritdoc}
     */
    public function set($key, $value = true, $dotNotation = true)
    {
        if ($dotNotation) {
            Arr::set($this->attributes, $key, $value);
        } else {
            $this->attributes[$key] = $value;
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove($keys, $dotNotation = true)
    {
        if ($dotNotation) {
            Arr::forget($this->attributes, $keys);
            
            return;
        }
        
        $keys = (array) $keys;
        
        if (count($keys) === 0) {
            return;
        }
        
        foreach ($keys as $key) {
            unset($this->attributes[$key]);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setAttributes($attributes = [])
    {
        return $this->fill($attributes);
    }
    
    /**
     * Count the attributes set in the Fluent instance.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->attributes);
    }
    
    /**
     * Convert the Fluent instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }
    
    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
    
    /**
     * Convert the Fluent instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
    
    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }
    
    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }
    
    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }
    
    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }
    
    /**
     * Get the external iterator.
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->attributes);
    }
    
    /**
     * Handle dynamic calls of the container to get attribute.
     *
     * @param  string  $attribute
     * @return mixed
     */
    public function __invoke($key, $default = null)
    {
        return $this->get($key, $default);
    }
    
    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return $this
     */
    public function __call($key, $args)
    {
        $value = count($args) > 0 ? $args[0] : true;
        $dotNotation = count($args) > 1 ? $args[1] : true;
        
        return $this->set($key, $value, $dotNotation);
    }
    
    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }
    
    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }
    
    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->has($key);
    }
    
    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        $this->remove($key);
    }
    
    /**
     * Return the properties that should be serialized.
     *
     * @return array
     */
    public function __sleep()
    {
        return ['attributes'];
    }
    
    /**
     * Data shown in var_dump.
     *
     * @return array
     */
    public function __debugInfo()
    {
        return $this->toArray();
    }
}
