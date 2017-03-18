<?php

namespace Simbigo\Phlint\Tokens;

class Token
{
    /**
     * @var TokenType
     */
    private $tokenType;
    private $value;

    public function __construct(TokenType $tokenType, $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
    }

}