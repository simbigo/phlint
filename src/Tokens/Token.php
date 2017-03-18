<?php

namespace Simbigo\Phlint\Tokens;

class Token
{
    /**
     * @var int
     */
    private $tokenType;
    private $value;

    public function __construct(int $tokenType, $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
    }

    public function is($tokenType)
    {
        return $this->tokenType === $tokenType;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}