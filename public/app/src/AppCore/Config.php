<?php

namespace AppCore;


class Config
{
    /**
     * @var array
     */
    protected $parameters = [];

    public function __construct()
    {
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $this->parameters = parse_ini_file($documentRoot . '/../app/config/config.ini', true);
    }

    /**
     * @param $section
     * @return array
     */
    public function getSectionParameters($section)
    {
        return (isset($this->parameters[$section])) ? $this->parameters[$section] : [];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasParameter($name, $section = 'general')
    {
        return isset($this->parameters[$section][$name]);
    }

    /**
     * @param string $name
     * @param string $section
     * @param string $defaultValue
     * @return mixed
     */
    public function getParameter($name, $section = 'general', $defaultValue = '')
    {
        return ($this->hasParameter($name, $section)) ? $this->parameters[$section][$name] : $defaultValue;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->parameters;
    }

    /**
     * @param string $name
     * @param string $section
     * @param mixed $value
     */
    public function setParameter($name, $section, $value)
    {
        $this->parameters[$section][$name] = $value;
    }
}