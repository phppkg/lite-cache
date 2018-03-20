<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Inhere\LiteCache\Parser;

/**
 * Class PhpParser
 * @package Inhere\LiteCache\Parser
 */
class PhpParser implements ParserInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data): string
    {
        return \serialize($data);
    }

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data)
    {
        return \unserialize($data, []);
    }
}
