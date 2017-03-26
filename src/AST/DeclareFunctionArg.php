<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

/**
 * Class FunctionArgument
 */
class DeclareFunctionArg extends ASTNode
{

    /**
     * @var Token
     */
    private $name;
    /**
     * @var Token
     */
    private $value;

    /**
     * FunctionArgument constructor.
     *
     * @param Token $name
     * @param Token|null $value
     */
    public function __construct(Token $name, Token $value = null)
    {

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return Token
     */
    public function getName(): Token
    {
        return $this->name;
    }

    /**
     * @return Token|null
     */
    public function getValue()
    {
        return $this->value;
    }

}