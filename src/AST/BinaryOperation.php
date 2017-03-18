<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class BinaryOperation extends ASTNode
{
    /**
     * @var ASTNode
     */
    private $leftArg;
    /**
     * @var Token
     */
    private $operation;
    /**
     * @var ASTNode
     */
    private $rightArg;

    /**
     * BinaryOperation constructor.
     *
     * @param Token $operation
     * @param ASTNode $leftArg
     * @param ASTNode $rightArg
     */
    public function __construct(Token $operation, ASTNode $leftArg, ASTNode $rightArg)
    {
        $this->operation = $operation;
        $this->leftArg = $leftArg;
        $this->rightArg = $rightArg;
    }

    /**
     * @return ASTNode
     */
    public function getLeftArg(): ASTNode
    {
        return $this->leftArg;
    }

    /**
     * @return Token
     */
    public function getOperation(): Token
    {
        return $this->operation;
    }

    /**
     * @return ASTNode
     */
    public function getRightArg(): ASTNode
    {
        return $this->rightArg;
    }
}