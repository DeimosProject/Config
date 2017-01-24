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
     * @var ConfigObject
     */
    protected $parameters;

    /**
     * Config constructor.
     *
     * @param string  $rootDir
     * @param Builder $builder
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($rootDir, Builder $builder)
    {
        $this->rootDir = rtrim($rootDir, '\\/') . '/';
        $this->builder = $builder;

        parent::__construct();

        if ($this->exists('_deimos'))
        {
            $this->parameters = $this->get('_deimos');
        }
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
     * @param string $name
     *
     * @return ConfigObject
     */
    protected function configure($name)
    {
        if (!isset($this->configure[$name]))
        {
            $this->configure[$name] = new ConfigObject(
                $this->builder,
                $this->getPath($name),
                $this->parameters
            );
        }

        return $this->configure[$name];
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

        $configure = $this->configure($configName);

        if ($path !== false)
        {
            return $configure->get($path);
        }

        return $configure;
    }

}