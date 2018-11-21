<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2018/5/29 0029
 * Time: 23:15
 */

namespace PhpComp\LiteCache;

use PhpComp\LiteCache\Traits\ConfigAndEventAwareTrait;
use Psr\SimpleCache\CacheInterface;
use Toolkit\DataParser\DataParserAwareTrait;

/**
 * Class AbstractCache
 * @package PhpComp\LiteCache
 */
abstract class AbstractCache implements CacheInterface
{
    use DataParserAwareTrait, ConfigAndEventAwareTrait;

    /**
     * @var string
     */
    protected static $driverName = '';

    /** @var bool Refresh current request cache. */
    private $refresh = false;

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
        if (!static::isSupported()) {
            throw new \RuntimeException(
                \sprintf("The driver '%s'is not support for current env.", self::getDriverName())
            );
        }

        $this->setConfig($config);
        $this->init();
    }

    protected function init()
    {
        // do something ...
    }

    /**
     * @return bool
     */
    abstract public static function isSupported(): bool;

    /**
     * @return string
     */
    public static function getDriverName(): string
    {
        return static::$driverName;
    }

    /**
     * @param string $driverName
     */
    protected static function setDriverName(string $driverName)
    {
        self::$driverName = $driverName;
    }

    /**
     * @param iterable $keys
     * @param null $default
     * @return array|iterable
     * @throws \Psr\SimpleCache\InvalidArgumentException
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
     * @param iterable $values
     * @param null $ttl
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }

        return true;
    }

    /**
     * @param iterable $keys
     * @return bool
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

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
}
