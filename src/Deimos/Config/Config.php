<?php

namespace Deimos\Config;

use Deimos\Builder\Builder;

class Config extends \ArrayIterator
{

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var array
     */
    protected $configure = [];

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * ConfigObject constructor.
     *
     * @param         $rootDir
     * @param Builder $builder
     */
    public function __construct($rootDir, Builder $builder)
    {
        $this->rootDir = rtrim($rootDir, '/') . '/';
        $this->builder = $builder;

        parent::__construct();
    }

    /**
     * @param $name
     *
     * @return array
     */
    protected function slice($name)
    {
        return explode(':', $name);
    }

    /**
     * @param $name
     *
     * @return ConfigObject|mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws \BadFunctionCallException
     */
    public function __set($name, $value)
    {
        throw new \BadFunctionCallException(__METHOD__);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return $this->exists($name);
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function exists($name)
    {
        return file_exists($this->getPath($name));
    }

    /**
     * @param $name string
     *
     * @return string
     */
    protected function getPath($name)
    {
        return $this->rootDir . 'config/' . $name . '.php';
    }

    /**
     * @param $name
     *
     * @return ConfigObject|mixed
     */
    public function get($name)
    {
        list ($configName, $path) = $this->slice($name);

        if (!isset($this->configure[$configName]))
        {
            $builder    = $this->builder;
            $pathString = $this->getPath($configName);

            $this->configure[$configName] = new ConfigObject($builder, $pathString);
        }

        /**
         * @var $configObject ConfigObject
         */
        $configObject = $this->configure[$configName];

        if ($path !== null)
        {
            return $configObject->get($path);
        }

        return $configObject;
    }

}