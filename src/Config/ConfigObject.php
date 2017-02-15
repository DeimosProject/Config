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
    protected $config;

    /**
     * @var bool
     */
    protected $loaded;

    /**
     * @var array
     */
    protected $storage = array();

    /**
     * @var self
     */
    protected $parameters;

    /**
     * ConfigObject constructor.
     *
     * @param Builder      $builder
     * @param array|string $config
     * @param self         $parameters
     */
    public function __construct($builder, $config, self $parameters = null)
    {
        $this->parameters = $parameters;
        $this->config     = $config;
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
     * @param string $path
     *
     * @return static
     *
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     * @throws \InvalidArgumentException
     */
    public function slice($path)
    {
        $config = $this->getRequired($path);

        if (!is_array($config))
        {
            throw new \InvalidArgumentException('Data is not array');
        }

        return new static($this->builder, $config, $this->parameters);
    }

    /**
     * @param $path
     *
     * @return array|mixed
     *
     * @throws \Deimos\Helper\Exceptions\ExceptionEmpty
     * @throws \InvalidArgumentException
     */
    public function getRequired($path)
    {
        if (!$this->loaded)
        {
            $this->rewind();
        }

        return $this->helper()->arr()
            ->getRequired($this->storage, $path);
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
     * walk parameters
     *
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    protected function walk()
    {
        if ($this->parameters instanceof self)
        {
            return array_walk_recursive($this->storage, function (&$value)
            {
                if ($value{0} === '%' && $value{strlen($value) - 1} === '%')
                {
                    $key = substr($value, 1, -1);

                    $value = $this->parameters->get($key);
                }
            });
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        if (!$this->loaded)
        {
            $this->loaded = true;

            if (is_string($this->config))
            {
                $this->storage = require $this->config;
            }
            else if (is_array($this->config))
            {
                $this->storage = $this->config;
            }
            else
            {
                throw new \InvalidArgumentException(__METHOD__);
            }

            $this->walk();
        }

        reset($this->storage);
    }

}