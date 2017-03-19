<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class VariableAccessor extends ASTNode
{
    const ACTION_GET = 1;
    const ACTION_SET = 2;

    /**
     * @var int
     */
    private $action;
    /**
     * @var ASTNode
     */
    private $value;
    /**
     * @var Token
     */
    private $variable;

    /**
     * VariableAccessor constructor.
     *
     * @param Token $variable
     * @param ASTNode $value
     * @param int $action
     */
    public function __construct(Token $variable, ASTNode $value, int $action)
    {
        $this->variable = $variable;
        $this->value = $value;
        $this->action = $action;
    }

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    /**
     * @return ASTNode
     */
    public function getValue(): ASTNode
    {
        return $this->value;
    }

    /**
     * @return Token
     */
    public function getVariable(): Token
    {
        return $this->variable;
    }
}