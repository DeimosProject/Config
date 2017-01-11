<?php

namespace Deimos\Config;

use Deimos\Builder\Builder;
use Deimos\Helper\Traits\Helper;

class ConfigObject implements \Iterator
{

    use Helper;

    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var bool
     */
    protected $loaded;

    /**
     * @var array
     */
    protected $storage = array();

    /**
     * ConfigObject constructor.
     *
     * @param Builder $builder
     * @param string  $configPath
     */
    public function __construct($builder, $configPath)
    {
        $this->configPath = $configPath;
        $this->builder    = $builder;
        $this->loaded     = false;
    }

    /**
     * @param $path string
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function __get($path)
    {
        return $this->get($path);
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
        return $this->get($name) !== null;
    }

    /**
     * @param null|string $path
     * @param mixed       $default
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public function get($path = null, $default = null)
    {
        if (!$this->loaded)
        {
            $this->rewind();
        }

        if ($path === null)
        {
            return $this->storage;
        }

        return $this->helper()->arr()
            ->get($this->storage, $path, $default);
    }

    /**
     * @inheritdoc
     */
    public function current()
    {
        return current($this->storage);
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        next($this->storage);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return key($this->storage);
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        if (!$this->loaded)
        {
            $this->loaded  = true;
            $this->storage = require $this->configPath;
        }

        reset($this->storage);
    }

}