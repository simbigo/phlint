<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class CallFunctionArg extends ASTNode
{
    /**
     * @var Token
     */
    private $name;
    /**
     * @var ASTNode
     */
    private $value;

    public function __construct(Token $name = null, ASTNode $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return Token
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return ASTNode
     */
    public function getValue(): ASTNode
    {
        return $this->value;
    }
}