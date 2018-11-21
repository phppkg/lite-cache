<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/29 0029
 * Time: 23:08
 */

namespace PhpComp\LiteCache;

/**
 * Class FileCache
 * @package PhpComp\LiteCache
 */
class FileCache extends AbstractCache
{
    /**
     * @var array
     */
    protected $config = [
        'path' => '',
        'prefix' => 'file_',
        'securityKey' => '',
    ];

    /**
     * @var array Temp caches
     */
    private $cache = [];

    /**
     * @throws \InvalidArgumentException
     */
    protected function init()
    {
        $this->config['path'] = \rtrim($this->config['path'], '/\\ ');

        if (!$this->config['path']) {
            throw new \InvalidArgumentException("Must setting the file cache dir by 'path'");
        }

        // create cache dir.
        $this->createDir($this->config['path']);
    }

    /**
     * @return bool
     */
    public static function isSupported(): bool
    {
        return \function_exists('file_get_contents');
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function get($key, $default = null)
    {
        if (!$key || $this->isRefresh()) {
            return $default;
        }

        $file = null;

        // in memory
        if (\array_key_exists($key, $this->cache)) {
            $data = $this->cache[$key];

            // in cache file
        } elseif (\file_exists($file = $this->getCacheFile($key))) {
            $str = \file_get_contents($file);
            $data = $this->getParser()->decode($str);

            // not exist
        } else {
            return $default;
        }

        $expire = $data['exp'];

        if (!$expire || $expire >= \time()) {
            return $data['val'];
        }

        // if expired
        if ($file) {
            \unlink($file);
        } else {
            unset($this->cache[$key]);
        }

        return $default;
    }

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store, must be serializable.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                     the driver supports TTL then the library may set a default value
     *                                     for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function set($key, $value, $ttl = null): bool
    {
        $data = $this->cache[$key] = [
            'exp' => $ttl > 0 ? (\time() + $ttl) : 0,
            'val' => $value,
        ];

        // encode data
        $string = $this->getParser()->encode($data);
        $cacheFile = $this->getCacheFile($key);

        // create dir.
        $this->createDir(\dirname($cacheFile));

        return false !== \file_put_contents($cacheFile, $string);
    }

    /**
     * Delete an item from the cache by its unique key.
     * @param string $key The unique cache key of the item to delete.
     * @return bool True if the item was successfully removed. False if there was an error.
     * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function delete($key): bool
    {
        // in memory
        if (\array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);
        }

        $file = $this->getCacheFile($key);

        if (\file_exists($file)) {
            return \unlink($file);
        }

        return true;
    }

    /**
     * Wipes clean the entire cache's keys.
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        foreach ($this->cache as $key => $data) {
            $file = $this->getCacheFile($key);

            if (\file_exists($file)) {
                \unlink($file);
            }
        }

        $this->cache = [];

        return true;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException MUST be thrown if the $key string is not a legal value.
     */
    public function has($key): bool
    {
        if (\array_key_exists($key, $this->cache)) {
            return true;
        }

        return \file_exists($this->getCacheFile($key));
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getCacheFile(string $key): string
    {
        $name = \md5($key . $this->config['securityKey']);

        return \sprintf('%s/%s/%s.data', $this->config['path'], \substr($name, 0, 7), $name);
    }

    /**
     * 支持层级目录的创建
     * @param $path
     * @param int|string $mode
     * @param bool $recursive
     * @return bool
     */
    private function createDir($path, $mode = 0775, $recursive = true): bool
    {
        return (is_dir($path) || !(!@mkdir($path, $mode, $recursive) && !is_dir($path))) && is_writable($path);
    }
}
