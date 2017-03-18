<?php

namespace Simbigo\Phlint\Tokens;

/**
 * Class Token
 */
class Token
{
    /**
     * @var int
     */
    private $tokenType;
    /**
     * @var
     */
    private $value;

    /**
     * Token constructor.
     *
     * @param int $tokenType
     * @param $value
     */
    public function __construct(int $tokenType, $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getType() : int
    {
        return $this->tokenType;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param $tokenType
     * @return bool
     */
    public function is($tokenType) : bool
    {
        return $this->tokenType === $tokenType;
    }
}