<?php

namespace DeimosTest;

use Deimos\Builder\Builder;
use Deimos\Config\Config;

class TestsSetUp extends \TestCase
{

    /**
     * @var Config
     */
    protected $config;

    public function setUp()
    {
        $builder = new Builder();
        $helper  = new \Deimos\Helper\Helper($builder);

        $this->config = new Config($helper, __DIR__ . '/config');
    }

}