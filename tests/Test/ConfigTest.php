<?php

namespace Test;

use DeimosTest\TestsSetUp;

class ConfigTest extends TestsSetUp
{

    public function testConfig()
    {
        $config = $this->config;

        $db = $config->get('db');

        $this->assertEquals(
            'conn',
            $db->getRequired('dsn')
        );
        $this->assertEquals(
            'root',
            $db->getRequired('login')
        );
        $this->assertEquals(
            '',
            $db->getRequired('password')
        );
        $options = $db->getData('options');
        $this->assertEquals(
            'world',
            $options['hello']
        );
    }

    public function testMagicGet()
    {
        $config = $this->config;

        $this->assertEquals(
            $config->get('db')->dsn,
            $config->get('db:dsn')
        );
    }

    public function testTree()
    {
        $config = $this->config;

        $this->assertEquals(
            'conn',
            $config->get('db:dsn')
        );
        $this->assertEquals(
            'world',
            $config->get('db:options.hello')
        );
        $this->assertEquals(
            'value1',
            $config->get('tree:level1.level2.level3.0')
        );
        $this->assertEquals(
            'value',
            $config->get('tree:item')
        );
        $this->assertEquals(
            [
                'value1',
                'value2',
                'value3',
                'value4',
            ],
            $config->get('tree:level1.level2.level3'),
            '', 0.0, 10, true
        );
    }

    public function testExist()
    {
        $config = $this->config;

        $this->assertTrue($config->exists('tree'));
        $this->assertFalse($config->exists('tree123'));
        $this->assertFalse(isset($config->get('db')->missing));
        $this->assertTrue(isset($config->get('db')->dsn));

        $this->assertEquals(
            [
                'ゼロ',
                '一',
                '二'
            ],
            $config->get('small')->asArray()
        );

        $small = $config->get('small');

        $this->assertEquals(
            'ゼロ',
            $small->current()
        );

        $small->next();
        $this->assertEquals(
            '一',
            $small->current()
        );

        $small->next();
        $this->assertEquals(
            '2',
            $small->key()
        );

        $small->rewind();
        $this->assertEquals(
            '0',
            $small->key()
        );

        $small->next();
        $small->next();
        $this->assertEquals(
            '二',
            $small->current()
        );

        $this->assertTrue($small->valid());
        $small->next();
        $this->assertFalse($small->valid());
    }

}
