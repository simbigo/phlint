<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

/**
 * Class Number
 */
class Number extends ASTNode
{
    /**
     * @var int|float
     */
    private $value;

    /**
     * Number constructor.
     *
     * @param $value
     */
    public function __construct(Token $value)
    {
        $this->value = $value;
    }

    /**
     * @return float|int
     */
    public function getValue()
    {
        return $this->value;
    }
}