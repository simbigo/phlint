<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class IfCondition extends ASTNode
{
    /**
     * @var ASTNode[]
     */
    private $falseBranch;
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
     * @var ASTNode[]
     */
    private $trueBranch;

    /**
     * BinaryOperation constructor.
     *
     * @param Token $operation
     * @param ASTNode $leftArg
     * @param ASTNode $rightArg
     * @param ASTNode[] $trueBranch
     * @param ASTNode[] $falseBranch
     */
    public function __construct(
        Token $operation,
        ASTNode $leftArg,
        ASTNode $rightArg,
        array $trueBranch,
        array $falseBranch
    ) {
        $this->operation = $operation;
        $this->leftArg = $leftArg;
        $this->rightArg = $rightArg;
        $this->trueBranch = $trueBranch;
        $this->falseBranch = $falseBranch;
    }

    /**
     * @return ASTNode[]
     */
    public function getFalseBranch(): array
    {
        return $this->falseBranch;
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

    /**
     * @return ASTNode[]
     */
    public function getTrueBranch(): array
    {
        return $this->trueBranch;
    }
}