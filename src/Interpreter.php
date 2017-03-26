<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\ASTFunction;
use Simbigo\Phlint\AST\ASTNode;
use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\CallFunctionArg;
use Simbigo\Phlint\AST\ClassDefinition;
use Simbigo\Phlint\AST\Number;
use Simbigo\Phlint\AST\VariableAccessor;
use Simbigo\Phlint\Core\UserFunction;
use Simbigo\Phlint\Exceptions\InternalError;
use Simbigo\Phlint\Exceptions\PhlintException;
use Simbigo\Phlint\Exceptions\SyntaxError;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

/**
 * Class Interpreter
 */
class Interpreter
{
    /**
     * @var Environment
     */
    private $environment;

    /**
     * @param BinaryOperation $node
     * @return float|int|mixed
     * @throws InternalError
     */
    private function visitBinaryOperationNode(BinaryOperation $node)
    {
        $operation = $node->getOperation();
        if ($operation->is(TokenType::T_PLUS)) {
            return $this->visitNode($node->getLeftArg()) + $this->visitNode($node->getRightArg());
        } elseif ($operation->is(TokenType::T_MINUS)) {
            return $this->visitNode($node->getLeftArg()) - $this->visitNode($node->getRightArg());
        } elseif ($operation->is(TokenType::T_MUL)) {
            return $this->visitNode($node->getLeftArg()) * $this->visitNode($node->getRightArg());
        } elseif ($operation->is(TokenType::T_DIV)) {
            return $this->visitNode($node->getLeftArg()) / $this->visitNode($node->getRightArg());
        }

        $message = 'Unknown token type: ' . $operation->getType() . PHP_EOL;
        $message .= 'Line: ' . $operation->getLine() . PHP_EOL;
        $message .= 'Position: ' . $operation->getPos() . PHP_EOL;
        $message .= 'Value: ' . $operation->getValue();
        throw new InternalError($message);
    }

    /**
     * @param CallFunctionArg $argument
     * @return float|int|mixed
     */
    private function visitCallFunctionArgNode(CallFunctionArg $argument)
    {
        return $this->visitNode($argument->getValue());
    }

    /**
     * @param ClassDefinition $node
     * @return null
     */
    private function visitClassDefinitionNode(ClassDefinition $node)
    {
        return null;
    }

    /**
     * @param ASTFunction $node
     * @return mixed
     * @throws PhlintException
     */
    private function visitFunctionNode(ASTFunction $node)
    {
        $functionName = $node->getFunction()->getValue();
        if ($node->getAction() === ASTFunction::ACTION_DECLARE) {
            $function = new UserFunction($functionName, $node->getStatements());
            foreach ($node->getArguments() as $argument) {
                $defaultValue = $argument->getValue();
                $function->defineArgument($argument->getName(), $defaultValue === null, $defaultValue);
            }
            $this->environment->assignFunction($functionName, $function);
            return null;
        }

        $arguments = $node->getArguments();
        $functionArguments = $this->environment->getFunction($functionName)->getDefinedArguments();
        $functionEnvironment = $this->environment->createLocalEnvironment();
        foreach ($functionArguments as $index => $functionArgument) {
            if (!isset($arguments[$index])) {
                if (!isset($functionArgument[1])) {
                    throw new PhlintException('Invalid arguments number.');
                }
                $argumentValue = $functionArgument[1];
            } else {
                $argumentValue = $this->visitNode($arguments[$index]);
            }
            $argumentName = $functionArgument[0];
            if ($argumentName instanceof Token) {
                $argumentName = $argumentName->getValue();
            }
            $functionEnvironment->assignVariable($argumentName, $argumentValue);
        }
        return $this->environment->getFunction($functionName)->call($this, $functionEnvironment);
    }

    /**
     * @param ASTNode|BinaryOperation|Number $node
     * @return float|int|mixed
     * @throws InternalError
     */
    private function visitNode(ASTNode $node)
    {
        if ($node instanceof BinaryOperation) {
            return $this->visitBinaryOperationNode($node);
        } elseif ($node instanceof Number) {
            return $this->visitNumberNode($node);
        } elseif ($node instanceof VariableAccessor) {
            return $this->visitVariableAccessorNode($node);
        } elseif ($node instanceof ASTFunction) {
            return $this->visitFunctionNode($node);
        } elseif ($node instanceof ClassDefinition) {
            return $this->visitClassDefinitionNode($node);
        } elseif ($node instanceof CallFunctionArg) {
            return $this->visitCallFunctionArgNode($node);
        }

        throw new InternalError('Unknown node type: ' . get_class($node));
    }

    /**
     * @param \Simbigo\Phlint\AST\Number|Number $node
     * @return float|int
     */
    private function visitNumberNode(Number $node)
    {
        return $node->getValue()->getValue();
    }

    /**
     * @param VariableAccessor $node
     * @return mixed
     * @throws SyntaxError
     */
    private function visitVariableAccessorNode(VariableAccessor $node)
    {
        $variableName = $node->getVariable()->getValue();
        if ($node->getAction() === VariableAccessor::ACTION_SET) {
            $this->environment->assignVariable($variableName, $this->visitNode($node->getValue()));
            return null;
        }

        return $this->environment->getVariable($variableName);
    }

    /**
     * @param Environment $environment
     * @param ASTNode[] $statements
     */
    public function evaluate(Environment $environment, array $statements)
    {
        #$global = $this->environment;
        $this->environment = $environment;
        #var_dump($environment);
        foreach ($statements as $statement) {
            $this->visitNode($statement);
        }
        #$this->environment = $global;
    }

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }
}
