<?php

namespace DeimosTest;

use Deimos\Builder\Builder;
use Deimos\Config\Config;

class TestsSetUp extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Config
     */
    protected $config;
    protected $hConfig;

    public function setUp()
    {
        $builder = new Builder();
        $this->config = new Config(__DIR__ . '/config', $builder);

        $builder = new HBuilder();
        $this->hConfig = new Config(__DIR__ . '/config', $builder);
    }

}