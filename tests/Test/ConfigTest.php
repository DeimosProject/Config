<?php

namespace Test;

use DeimosTest\TestsSetUp;

class ConfigTest extends TestsSetUp
{

    public function testConfig($helper = false)
    {
        $config = $helper ? $this->hConfig : $this->config;

        $db = $config->get('db');

        $this->assertEquals(
            'conn',
            $db->get('dsn')
        );
        $this->assertEquals(
            'root',
            $db->get('login')
        );
        $this->assertEquals(
            '',
            $db->get('password')
        );
        $options = $db->get('options');
        $this->assertEquals(
            'world',
            $options['hello']
        );
    }

    public function testMagicGet($helper = false)
    {
        $config = $helper ? $this->hConfig : $this->config;

        $this->assertEquals(
            $config->db->dsn,
            $config->get('db:dsn')
        );
    }

    /**
     * @expectedException \BadFunctionCallException
     */
    public function testMagicSetException()
    {
        $this->config->db = 'failed';
    }

    /**
     * @expectedException \BadFunctionCallException
     */
    public function testMagicSetException2()
    {
        $this->config->db->dsn = 'failed';
    }

    /**
     * @expectedException \BadFunctionCallException
     */
    public function testMagicSetExceptionBuilder()
    {
        $this->hConfig->db = 'failed';
    }

    /**
     * @expectedException \BadFunctionCallException
     */
    public function testMagicSetException2Builder()
    {
        $this->hConfig->db->dsn = 'failed';
    }

    public function testTree($helper = false)
    {
        $config = $helper ? $this->hConfig : $this->config;

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

    public function testExist($helper = false)
    {
        $config = $helper ? $this->hConfig : $this->config;

        $this->assertTrue(isset($config->tree));
        $this->assertFalse(isset($config->missing));
        $this->assertTrue($config->exists('tree'));
        $this->assertFalse(isset($config->db->missing));
        $this->assertTrue(isset($config->db->dsn));

        $this->assertEquals(
            [
                'ゼロ',
                '一',
                '二'
            ],
            $config->get('small')->get()
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

    public function testHelperBuilder()
    {
        $this->testConfig(true);
        $this->testMagicGet(true);
        $this->testTree(true);
        $this->testExist(true);
    }

}
