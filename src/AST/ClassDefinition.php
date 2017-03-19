<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

/**
 * Class ClassDefinition
 */
class ClassDefinition extends ASTNode
{
    /**
     * @var Token
     */
    private $className;

    /**
     * Number constructor.
     *
     * @param $className
     */
    public function __construct(Token $className)
    {
        $this->className = $className;
    }

    /**
     * @return Token
     */
    public function getClassName()
    {
        return $this->className;
    }
}