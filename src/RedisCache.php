<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 12:59
 */

namespace Inhere\LiteCache;

use Inhere\LiteCache\Traits\BasicRedisAwareTrait;
use MyLib\DataParser\DataParserAwareTrait;
use Psr\SimpleCache\CacheInterface;

/**
 * Class RedisCache
 * @package Inhere\LiteCache
 * @method int exists($key)
 */
class RedisCache implements CacheInterface
{
    use BasicRedisAwareTrait, DataParserAwareTrait;

    // ARGS: ($name, $mode, $config)
    const CONNECT = 'redis.connect';
    // ARGS: ($name, $mode)
    const DISCONNECT = 'redis.disconnect';
    // ARGS: ($method, array $args)
    const BEFORE_EXECUTE = 'redis.beforeExecute';
    // ARGS: ($method, array $data)
    const AFTER_EXECUTE = 'redis.afterExecute';

    /** @var bool Refresh current request cache. */
    private $refresh = false;

    /**************************************************************************
     * basic method
     *************************************************************************/

    /**
     * @param string $key
     * @return string
     */
    public function getCacheKey($key): string
    {
        return $this->config['prefix'] . $key;
    }

    /**
     * redis 中 key 是否存在
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function hasKey($key): bool
    {
        return $this->execute('exists', $key);
    }

    /**************************************************************************
     * interface methods
     *************************************************************************/

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     */
    public function get($key, $default = null)
    {
        if (!$key || $this->isRefresh()) {
            return $default;
        }

        $key = $this->getCacheKey($key);

        if ($this->execute('exists', $key)) {
            $result = $this->execute('get', $key);

            return $this->getParser()->decode($result);
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     * @throws \Inhere\LiteCache\InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$key) {
            return false;
        }

        $key = $this->getCacheKey($key);
        $value = $this->getParser()->encode($value);

        return $this->execute('set', $key, $value, $ttl);
    }

    /**
     * @param string $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function delete($key): bool
    {
        if (!$key) {
            return false;
        }

        $key = $this->getCacheKey($key);

        return $this->execute('del', $key) > 0;
    }

    /**
     * {@inheritdoc}
     * @throws \Inhere\LiteCache\InvalidArgumentException
     */
    public function clear()
    {
        return $this->execute('flushDB');
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        if (!$key) {
            return false;
        }

        $key = $this->getCacheKey($key);

        return $this->execute('exists', $key);
    }

    /**
     * alias of the `getMultiple`
     * {@inheritdoc}
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMulti($keys, $default = null)
    {
        return $this->getMultiple($keys, $default);
    }

    /**
     * Obtains multiple cache items by their unique keys.
     * @see \Redis::getMultiple()
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        if (!$keys || !$this->isRefresh()) {
            return [];
        }

        $values = [];
        $keyList = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        /** @var array $results */
        $results = $this->execute('getMultiple', $keyList);

        foreach ($results as $idx => $value) {
            $key = $keys[$idx];
            $values[$key] = $value === false ? $default : $this->getParser()->decode($value);
        }

        return $values;
    }

    /**
     * alias of the `setMultiple`
     * {@inheritdoc}
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setMulti($values, $ttl = null): bool
    {
        return $this->setMultiple($values, $ttl);
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int $ttl Optional. The TTL value of this item. If no value is sent and
     *                                      the driver supports TTL then the library may set a default value
     *                                      for it or let the driver take care of that.
     * @return bool True on success and false on failure.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple($values, $ttl = null): bool
    {
        /** @var \Redis $rds */
        $rds = $this->execute('multi');

        foreach ($values as $key => $value) {
            $key = $this->getCacheKey($key);
            $value = $this->getParser()->encode($value);

            $rds->setex($key, $ttl, $value);
        }

        $result = (array)$rds->exec();

        return \count($result) > 0;
    }

    /**
     * Deletes multiple cache items in a single operation.
     * @param iterable $keys A list of string-based keys to be deleted.
     * @return bool True if the items were successfully removed. False if there was an error.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys): bool
    {
        $keyList = \array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        return $this->execute('del', $keyList) > 0;
    }

    /**************************************************************************
     * extends method
     *************************************************************************/

    /**
     * @return bool
     */
    public function isRefresh(): bool
    {
        return $this->refresh;
    }

    /**
     * @param bool $refresh
     */
    public function setRefresh($refresh)
    {
        $this->refresh = (bool)$refresh;
    }

    protected function onConnect(): void
    {
        $this->fire(self::CONNECT, [$this]);
    }

    protected function onDisconnect(): void
    {
        $this->fire(self::DISCONNECT, [$this]);
    }

    protected function onBeforeExecute($method, $args): void
    {
        $this->fire(self::BEFORE_EXECUTE, [$method, $args]);
    }

    protected function onAfterExecute($method, $args, $ret): void
    {
        $this->fire(self::AFTER_EXECUTE, [$method, $args, $ret]);
    }
}
