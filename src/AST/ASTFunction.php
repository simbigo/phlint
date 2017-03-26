<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class ASTFunction extends ASTNode
{
    const ACTION_DECLARE = 'declare';
    const ACTION_CALL = 'call';
    /**
     * @var string
     */
    private $action;

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
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
     * @param Token $functionName
     * @param ASTNode $argument
     * @param string $action
     */
    public function __construct(Token $functionName, $action = self::ACTION_CALL, ASTNode $argument = null)
    {
        $this->function = $functionName;
        $this->argument = $argument;
        $this->action = $action;
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
