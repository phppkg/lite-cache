<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/6 0006
 * Time: 16:56
 */

namespace PhpComp\LiteCache;

/**
 * Class ArrayCache
 * @package PhpComp\LiteCache
 */
class ArrayCache extends AbstractCache
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
     * @return array
     */
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

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return true;
    }
}

