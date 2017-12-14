<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Inhere\LiteCache;

/**
 * Class PhpParser
 * @package Inhere\LiteCache
 */
class PhpParser implements ParserInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data)
    {
        return serialize($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode($data)
    {
        return unserialize($data, []);
    }
}