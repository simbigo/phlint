<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

class Lexer
{
    /**
     * @var string
     */
    private $text;

    /**
     * @return Token
     */
    public function getNextToken() : Token
    {
        return new Token(new TokenType(TokenType::T_EOF), null);
    }

    /**
     * @param $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
    }
}