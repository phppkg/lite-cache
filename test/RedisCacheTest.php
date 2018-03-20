<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/16 0016
 * Time: 20:11
 */

namespace Inhere\LiteCache\Tests;

use Inhere\LiteCache\RedisCache;
use PHPUnit\Framework\TestCase;

/**
 * Class RedisCacheTest
 * @package Inhere\LiteCache\Tests
 */
class RedisCacheTest extends TestCase
{
    const CONFIG = [
        'host' => 'redis',
        'port' => 6379,
    ];

    /** @var RedisCache */
    private $rds;

    public function setUp()
    {
        $this->rds = new RedisCache(self::CONFIG);
    }

    public function testConnect()
    {
        $rds = new RedisCache(self::CONFIG);
        $rds->connect();

        $this->assertInstanceOf(\Redis::class, $rds->getRedis());

        $rds->disconnect();
    }

    public function testSimpleGetSet()
    {
        $rds = $this->rds;
        $rds->clear();

        $ret = $rds->get('test');
        $this->assertNull($ret);

        $ret = $rds->get('test', false);
        $this->assertFalse($ret);

        $val = 'value';
        $ret = $rds->set('test', $val);
        $this->assertTrue($ret);

        $ret = $rds->get('test');
        $this->assertSame($ret, $val);
    }

    public function testGetSetArray()
    {
        $key = 'testArray';
        $rds = $this->rds;
        $rds->clear();

        $ret = $rds->get($key);
        $this->assertNull($ret);

        $ret = $rds->get($key, false);
        $this->assertFalse($ret);

        $val = [34, 'value'];
        $ret = $rds->set($key, $val);
        $this->assertTrue($ret);

        $ret = $rds->get($key);
        $this->assertCount(2, $ret);
        $this->assertSame($ret, $val);
    }
}
