<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Tokens\TokenType;

class Interpreter
{
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * Interpreter constructor.
     *
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

        $digitLeft = $this->lexer->getNextToken();
        $operator = $this->lexer->getNextToken();
        $digitRight = $this->lexer->getNextToken();
        $result = null;
        if ($operator->is(TokenType::T_PLUS)) {
            $result = $digitLeft->getValue() + $digitRight->getValue();
        } elseif ($operator->is(TokenType::T_MINUS)) {
            $result = $digitLeft->getValue() - $digitRight->getValue();
        }
        return $result;
    }
}