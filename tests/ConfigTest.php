<?php

namespace Tests;

use App\Support\Config;
use RuntimeException;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test__construct(): void
    {
        $config = new Config(dirname(__DIR__) . '/config', 'test', dirname(__DIR__));
        $this->assertInstanceOf(Config::class, $config);
    }

    public function test__costructError(): void
    {
        $this->expectException(RuntimeException::class);
        new Config('not-exists', 'test', 'not-exists-too');
    }

    public function testEnvironmentFileLoad(): void
    {
        $config = new Config(__DIR__ . '/_tests_data', 'test', dirname(__DIR__));
        $this->assertNull($config->get('templates')['cache']);
    }

    public function testGet(): void
    {
        $config = new Config(dirname(__DIR__) . '/config', 'test', dirname(__DIR__));

        $this->assertNotEmpty($config->get('slim'));
        $this->assertNotEmpty($config->get('templates'));

        $this->assertEquals(dirname(__DIR__) . '/template', $config->get('templates')['dir']);
    }
}
