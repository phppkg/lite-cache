<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:07
 */

namespace Inhere\LiteCache\Parser;

/**
 * Interface ParserInterface
 * @package Inhere\LiteCache\Parser
 */
interface ParserInterface
{
    /**
     * @param mixed $data
     * @return string
     */
    public function encode($data): string;

    /**
     * @param string $data
     * @return mixed
     */
    public function decode(string $data);
}
