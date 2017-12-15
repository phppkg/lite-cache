<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 16:48
 */

namespace Inhere\LiteCache\Traits;

use Inhere\LiteCache\ConnectionException;
use Inhere\LiteCache\InvalidArgumentException;

/**
 * Trait BasicRedisAwareTrait
 * @package Inhere\LiteCache\Traits
 */
trait BasicRedisAwareTrait
{
    use ConfigAndEventAwareTrait;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var array
     */
    protected $config = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 0.0,
        'database' => 0,
        'prefix' => 'RDS_',

        'password' => null,
        'persistent' => false,

        'options' => [],
    ];

    /**
     * @param array $config
     * @return static
     * @throws \RuntimeException
     */
    public static function make(array $config = [])
    {
        return new static($config);
    }

    /**
     * @param array $config
     * @throws \RuntimeException
     */
    public function __construct(array $config = [])
    {
        if (!self::isSupported()) {
            throw new \RuntimeException("The php extension 'redis' is required.");
        }

        $this->setConfig($config);
    }

    /**
     * __destruct
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @return bool
     */
    public static function isSupported()
    {
        return class_exists(\Redis::class, false);
    }

    /**
     * @return $this
     * @throws ConnectionException
     */
    public function connect()
    {
        if ($this->redis) {
            return $this;
        }

        try {
            $config = $this->config;
            $client = new \Redis();
            $client->connect($config['host'], (int)$config['port'], $config['timeout']);

            if ($config['password'] && !$client->auth($config['password'])) {
                throw new \RuntimeException('Auth failed on connect to the redis server.');
            }

            if ($config['database'] >= 0) {
                $client->select((int)$config['database']);
            }

            $options = $config['options'] ?? [];

            foreach ($options as $name => $value) {
                $client->setOption($name, $value);
            }

            $this->redis = $client;
        } catch (\Throwable $e) {
            throw new ConnectionException("Connect redis server error: {$e->getMessage()}");
        }

        $this->onConnect();

        return $this;
    }

    protected function onConnect()
    {
        // $this->fire(self::CONNECT, [$this]);
    }

    protected function onDisconnect()
    {
        // $this->fire(self::DISCONNECT, [$this]);
    }

    protected function onBeforeExecute($method, $args)
    {
        // $this->fire(self::BEFORE_EXECUTE, [$method, $args]);
    }

    protected function onAfterExecute($method, $args, $ret)
    {
        // $this->fire(self::AFTER_EXECUTE, [$method, $args, $ret]);
    }

    /**
     * reconnect
     */
    public function reconnect()
    {
        $this->redis = null;
        $this->connect();
    }

    /**
     * disconnect
     */
    public function disconnect()
    {
        $this->onDisconnect();
        $this->redis = null;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, array $args)
    {
        return $this->execute($method, ...$args);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function execute($method, ...$args)
    {
        $this->connect();
        $upperMethod = strtoupper($method);

        // exists
        if (\method_exists($this->redis, $upperMethod)) {
            // trigger before event (read)
            $this->onBeforeExecute($method, $args);

            $ret = $this->redis->$upperMethod(...$args);

            // trigger after event (read)
            $this->onAfterExecute($method, $args, $ret);

            return $ret;
        }

        throw new InvalidArgumentException("Call the redis command method [$method] don't exists!");
    }

    /**
     * get Connection
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

}
