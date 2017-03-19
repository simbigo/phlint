<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class FunctionCall extends ASTNode
{
    /**
     * @var ASTNode
     */
    private $argument;
    /**
     * @var Token
     */
    private $function;

    /**
     * VariableAccessor constructor.
     *
     * @param Token $function
     * @param ASTNode $argument
     */
    public function __construct(Token $function, ASTNode $argument)
    {
        $this->function = $function;
        $this->argument = $argument;
    }

    /**
     * @return ASTNode
     */
    public function getArgument(): ASTNode
    {
        return $this->argument;
    }

    /**
     * @return Token
     */
    public function getFunction(): Token
    {
        return $this->function;
    }
}
