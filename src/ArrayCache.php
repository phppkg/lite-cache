<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/6 0006
 * Time: 16:56
 */

namespace Inhere\LiteCache;

use Psr\SimpleCache\CacheInterface;

/**
 * Class ArrayCache
 * @package Inhere\LiteCache
 */
class ArrayCache implements CacheInterface
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->cache[$key];
        }

        return $default;
    }

    /**
     * @param iterable $keys
     * @param null $default
     * @return array|iterable
     */
    public function getMultiple($keys, $default = null)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key): bool
    {
        return \array_key_exists($key, $this->cache);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        $this->cache[$key] = $value;

        return true;
    }

    /**
     * @param iterable $values
     * @param null $ttl
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }


    /**
     * @param string $key
     * @return bool
     */
    public function delete($key): bool
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);

            return true;
        }

        return false;
    }

    /**
     * @param iterable $keys
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    public function all(): array
    {
        return $this->cache;
    }

    /**
     * @return bool|void
     */
    public function clear()
    {
        $this->cache = [];
    }

}

