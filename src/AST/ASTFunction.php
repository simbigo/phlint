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
     * @var DeclareFunctionArg[]|CallFunctionArg[]
     */
    private $arguments;
    /**
     * @var Token
     */
    private $function;

    /**
     * VariableAccessor constructor.
     *
     * @param Token $functionName
     * @param ASTNode[] $arguments
     * @param string $action
     */
    public function __construct(Token $functionName, $action = self::ACTION_CALL, array $arguments = null)
    {
        $this->function = $functionName;
        $this->arguments = $arguments;
        $this->action = $action;
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
