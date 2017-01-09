<?php

namespace Deimos\Config;

use Deimos\Builder\Builder;
use Deimos\Helper\Helper as DeimosHelper;

trait Helper
{

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var DeimosHelper
     */
    private $helper;

    /**
     * @return DeimosHelper
     *
     * @throws \InvalidArgumentException
     */
    private function instanceHelper()
    {
        if (method_exists($this->builder, 'helper'))
        {
            return $this->builder->helper();
        }

        return new DeimosHelper($this->builder);
    }

    /**
     * @return DeimosHelper
     *
     * @throws \InvalidArgumentException
     */
    protected final function helper()
    {
        if (!$this->helper)
        {
            $this->helper = $this->instanceHelper();
        }

        return $this->helper;
    }

}