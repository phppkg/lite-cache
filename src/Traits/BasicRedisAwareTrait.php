<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 16:48
 */

namespace PhpComp\LiteCache\Traits;

use PhpComp\LiteCache\ConnectionException;
use PhpComp\LiteCache\InvalidArgumentException;

/**
 * Trait BasicRedisAwareTrait
 * @package PhpComp\LiteCache\Traits
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
    public static function isSupported(): bool
    {
        return \class_exists(\Redis::class, false);
    }

    /**
     * @return $this
     * @throws ConnectionException
     */
    public function connect(): self
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

    abstract protected function onConnect(): void;
    // {
    //     $this->fire(self::CONNECT, [$this]);
    // }

    abstract protected function onDisconnect(): void;
    //{
    //     $this->fire(self::DISCONNECT, [$this]);
    //}

    abstract protected function onBeforeExecute($method, $args): void;
    // {
    //     $this->fire(self::BEFORE_EXECUTE, [$method, $args]);
    // }

    abstract protected function onAfterExecute($method, $args, $ret): void;
    // {
    //     $this->fire(self::AFTER_EXECUTE, [$method, $args, $ret]);
    // }

    /**
     * reconnect
     */
    public function reconnect(): void
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
     * @throws InvalidArgumentException
     */
    public function execute(string $method, ...$args)
    {
        $this->connect();
        $upperMethod = \strtoupper($method);

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
    public function getRedis(): \Redis
    {
        return $this->redis;
    }

}
