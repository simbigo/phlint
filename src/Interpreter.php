<?php

namespace Simbigo\Phlint;


class Interpreter
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * Interpreter constructor.
     * @param Lexer $lexer
     */
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function evaluate($text)
    {
        $this->lexer->setText($text);
        return '';
    }
}