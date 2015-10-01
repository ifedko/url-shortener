<?php

namespace AppCore;

class Container
{
    /**
     * @var array
     */
    private $container;

    public function __construct()
    {
        $this->container = [];
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function get($name)
    {
        return (isset($this->container[$name])) ? $this->container[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function set($name, $value)
    {
        $this->container[$name] = $value;
    }
}
