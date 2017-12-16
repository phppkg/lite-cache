<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-12-14
 * Time: 19:37
 */

namespace Inhere\LiteCache\Traits;

use Inhere\LiteCache\Parsers\ParserInterface;
use Inhere\LiteCache\Parsers\PhpParser;

/**
 * Class DataParserAwareTrait
 * @package Inhere\LiteCache\Traits
 */
trait DataParserAwareTrait
{
    /**
     * @var ParserInterface
     */
    private $parser;

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        if (!$this->parser) {
            $this->parser = new PhpParser();
        }

        return $this->parser;
    }

    /**
     * @param ParserInterface $parser
     */
    public function setParser(ParserInterface $parser)
    {
        $this->parser = $parser;
    }
}
