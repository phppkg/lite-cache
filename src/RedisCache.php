<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 12:59
 */

namespace Inhere\LiteCache;

use Psr\SimpleCache\CacheInterface;

/**
 * Class RedisCache
 * @package Inhere\LiteCache
 */
class RedisCache implements CacheInterface
{
    /**
     * @var \Redis
     */
    private $redis;

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {

    }

    /**
     * Obtains multiple cache items by their unique keys.
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple($keys, $default = null)
    {
        $list = [];

        foreach ($keys as $key) {

        }

        return $list;
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
    public function setMultiple($values, $ttl = null)
    {
        // TODO: Implement setMultiple() method.
    }

    /**
     * Deletes multiple cache items in a single operation.
     * @param iterable $keys A list of string-based keys to be deleted.
     * @return bool True if the items were successfully removed. False if there was an error.
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple($keys)
    {
        // TODO: Implement deleteMultiple() method.
    }
}