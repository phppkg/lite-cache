<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/16 0016
 * Time: 19:10
 */

namespace Inhere\LiteCache\Tests;

use Inhere\LiteCache\MemCache;
use PHPUnit\Framework\TestCase;

/**
 * Class MemCacheTest
 * @package Inhere\LiteCache\Tests
 */
class MemCacheTest extends TestCase
{
    const CONFIG = [
        'servers' => [
            'test' => [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 0,
                // 'timeout' => 1,
            ]
        ]
    ];

    /** @var MemCache */
    private $mem;

    public function setUp()
    {
        $this->mem = new MemCache(self::CONFIG);
    }

    public function testConnect()
    {
        $mem = new MemCache(self::CONFIG);

        if ($mem->isMemcached()) {
            $this->assertInstanceOf(\Memcached::class, $mem->getDriver());
        } else {
            $this->assertInstanceOf(\Memcache::class, $mem->getDriver());
        }
// var_dump($mem);die;
        $mem->disconnect();
    }

    public function testSimpleGetSet()
    {
        // $mem = new MemCache(self::CONFIG);
        $mem = $this->mem;
        $mem->clear();

        $ret = $mem->get('test');
        $this->assertNull($ret);

        $ret = $mem->get('test', false);
        $this->assertFalse($ret);

        $val = 'value';
        $ret = $mem->set('test', $val);
        $this->assertTrue($ret);

        $ret = $mem->get('test');
        $this->assertSame($ret, $val);
    }
}
