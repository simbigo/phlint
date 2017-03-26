<?php

namespace Simbigo\Phlint\AST;

use Simbigo\Phlint\Tokens\Token;

class ASTFunction extends ASTNode
{
    const ACTION_CALL = 'call';
    const ACTION_DECLARE = 'declare';
    /**
     * @var string
     */
    private $action;
    /**
     * @var DeclareFunctionArg[]|CallFunctionArg[]
     */
    private $arguments;
    /**
     * @var Token
     */
    private $function;
    /**
     * @var array
     */
    private $statements;

    /**
     * @return array
     */
    public function getStatements(): array
    {
        return $this->statements;
    }

    /**
     * VariableAccessor constructor.
     *
     * @param Token $functionName
     * @param string $action
     * @param ASTNode[] $arguments
     * @param array $statements
     */
    public function __construct(
        Token $functionName,
        $action = self::ACTION_CALL,
        array $arguments = null,
        array $statements = []
    ) {
        $this->function = $functionName;
        $this->arguments = $arguments;
        $this->action = $action;
        $this->statements = $statements;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return DeclareFunctionArg[]|CallFunctionArg[]
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return Token
     */
    public function getFunction(): Token
    {
        return $this->function;
    }
}
