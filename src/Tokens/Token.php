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
    private $line;
    /**
     * @var int
     */
    private $pos;
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
     * @param int $line
     * @param int $pos
     * @param $value
     */
    public function __construct(int $tokenType, int $line, int $pos, $value)
    {
        $this->tokenType = $tokenType;
        $this->value = $value;
        $this->line = $line;
        $this->pos = $pos;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getPos(): int
    {
        return $this->pos;
    }

    /**
     * @return int
     */
    public function getType(): int
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
    public function is($tokenType): bool
    {
        return $this->tokenType === $tokenType;
    }

    public function isIn(array $tokens): bool
    {
        return in_array($this->tokenType, $tokens, true);
    }
}