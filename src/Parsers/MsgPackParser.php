<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/12/16 0016
 * Time: 21:23
 */

namespace Inhere\LiteCache\Parsers;

/**
 * Class MsgPackParser
 * @package Inhere\LiteCache\Parsers
 */
class MsgPackParser implements ParserInterface
{
    /**
     * class constructor.
     */
    public function __construct()
    {
        if (!\function_exists('msgpack_pack')) {
            throw new \RuntimeException("The php extension 'msgpack' is required!");
        }
    }

    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data)
    {
        return \msgpack_pack($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode($data)
    {
        return \msgpack_unpack($data);
    }
}
