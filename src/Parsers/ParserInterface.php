<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Inhere\LiteCache;

/**
 * Interface ParserInterface
 * @package Inhere\LiteCache
 */
interface ParserInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data);

    /**
     * @param string $data
     * @return mixed
     */
    public function decode($data);
}