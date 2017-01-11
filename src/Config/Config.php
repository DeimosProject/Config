<?php

namespace Deimos\Config;

use Deimos\Builder\Builder;
use Deimos\Helper\Traits\Helper;

class Config extends \ArrayIterator
{

    use Helper;

    /**
     * @var string
     */
    protected $rootDir;

    /**
     * @var array
     */
    protected $configure = [];

    /**
     * Config constructor.
     *
     * @param string  $rootDir
     * @param Builder $builder
     */
    public function __construct($rootDir, Builder $builder)
    {
        $this->rootDir = rtrim($rootDir, '\\/') . '/';
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
     *
     * @throws \InvalidArgumentException
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
     *
     * @throws \InvalidArgumentException
     */
    public function __isset($name)
    {
        return $this->exists($name);
    }

    /**
     * @param $name
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function exists($name)
    {
        $path = $this->getPath($name);

        return $this->helper()->file()->isFile($path);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getPath($name)
    {
        return $this->rootDir . $name . '.php';
    }

    /**
     * @param $name
     *
     * @return ConfigObject|mixed
     *
     * @throws \InvalidArgumentException
     */
    public function get($name)
    {
        $slice      = $this->slice($name);
        $configName = current($slice);
        $path       = next($slice);

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

        if ($path !== false)
        {
            return $configObject->get($path);
        }

        return $configObject;
    }

}