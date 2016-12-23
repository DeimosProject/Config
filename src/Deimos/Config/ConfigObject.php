<?php

namespace Deimos\Config;

use Deimos\Builder\Builder;
use Deimos\Helper\Helper;

class ConfigObject implements \Iterator
{

    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var bool
     */
    protected $loaded;

    /**
     * @var bool
     */
    protected $builder;

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
     */
    public function __isset($name)
    {
        return $this->get($name) !== null;
    }

    /**
     * @return Helper
     */
    public final function helper()
    {
        static $helper;

        if (!$helper)
        {
            if (method_exists($this->builder, 'helper'))
            {
                $helper = $this->builder->helper();
            }
            else
            {
                $builder = $this->builder;
                $helper  = new Helper($builder);
            }
        }

        return $helper;
    }

    /**
     * @param null|string $path
     * @param mixed       $default
     *
     * @return mixed
     */
    public function get($path = null, $default = null)
    {
        if (!$this->loaded)
        {
            $this->rewind();
        }

        return $this->helper()->arr()
            ->get($this->storage, $path, $default);
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->storage);
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        next($this->storage);
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->storage);
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
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