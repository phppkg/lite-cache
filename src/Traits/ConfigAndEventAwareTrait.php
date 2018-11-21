<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/15 0015
 * Time: 22:00
 */

namespace PhpComp\LiteCache\Traits;

/**
 * Trait ConfigAndEventAwareTrait
 * @package PhpComp\LiteCache\Traits
 */
trait ConfigAndEventAwareTrait
{
    /** @var array  */
    private $_events = [];

    /**
     * register a event callback
     * @param string $name event name
     * @param callable $cb event callback
     * @param bool $replace replace exists's event cb
     */
    public function on(string $name, callable $cb, $replace = false)
    {
        if ($replace || !isset($this->_events[$name])) {
            $this->_events[$name] = $cb;
        }
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
    protected function fire(string $name, array $args = [])
    {
        if (!isset($this->_events[$name]) || !($cb = $this->_events[$name])) {
            return null;
        }

        return self::call($cb, ...$args);
    }

    /**
     * Method to get property Options
     * @param string|null $key
     * @param mixed $default
     * @return array|mixed
     */
    public function getConfig(string $key = null, $default = null)
    {
        if ($key) {
            return $this->config[$key] ?? $default;
        }

        return $this->config;
    }

    /**
     * Method to set property config
     * @param  array $config
     * @param  bool $merge
     * @return static Return self to support chaining.
     */
    public function setConfig(array $config, $merge = true)
    {
        $this->config = $merge ? array_merge($this->config, $config) : $config;

        return $this;
    }

    /**
     * @param callable|mixed $cb
     * @param array ...$args
     * @return mixed
     */
    public static function call(callable $cb, ...$args)
    {
        if (\is_string($cb)) {
            // function
            if (\strpos($cb, '::') === false) {
                return $cb(...$args);
            }

            // ClassName::method
            $cb = \explode('::', $cb, 2);
        } elseif (\is_object($cb) && \method_exists($cb, '__invoke')) {
            return $cb(...$args);
        }

        if (\is_array($cb)) {
            list($obj, $mhd) = $cb;

            return \is_object($obj) ? $obj->$mhd(...$args) : $obj::$mhd(...$args);
        }

        return $cb(...$args);
    }
}
