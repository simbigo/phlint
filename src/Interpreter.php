<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\ASTFunction;
use Simbigo\Phlint\AST\ASTNode;
use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\ClassDefinition;
use Simbigo\Phlint\AST\Number;
use Simbigo\Phlint\AST\VariableAccessor;
use Simbigo\Phlint\Core\PhlintFunction;
use Simbigo\Phlint\Exceptions\InternalError;
use Simbigo\Phlint\Exceptions\SyntaxError;
use Simbigo\Phlint\Tokens\TokenType;

/**
 * Class Interpreter
 */
class Interpreter
{
    /**
     * @var PhlintFunction[]
     */
    private $functionMap = [];
    /**
     * @var array
     */
    private $variableMap = [];

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
     * @param ASTFunction $node
     * @return mixed
     * @throws SyntaxError
     */
    private function visitFunctionNode(ASTFunction $node)
    {
        $functionName = $node->getFunction()->getValue();
        if ($node->getAction() === ASTFunction::ACTION_DECLARE) {
            $this->functionMap[$functionName] = $node;
            return null;
        }

        if (!array_key_exists($functionName, $this->functionMap)) {
            throw new SyntaxError('Undefined function "' . $functionName . '"');
        }
        return $this->functionMap[$functionName]->call($this->visitNode($node->getArgument()));
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
        }

        throw new InternalError('Unknown node type: ' . get_class($node));
    }

    private function visitClassDefinitionNode(ClassDefinition $node)
    {
        return null;
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
            $this->variableMap[$variableName] = $this->visitNode($node->getValue());
            return null;
        }

        if (!array_key_exists($variableName, $this->variableMap)) {
            throw new SyntaxError('Undefined variable "' . $variableName . '"');
        }
        return $this->variableMap[$variableName];
    }

    /**
     * @param ASTNode[] $statements
     */
    public function evaluate(array $statements)
    {
        foreach ($statements as $statement) {
            $this->visitNode($statement);
        }
    }

    /**
     * @param PhlintFunction $function
     */
    public function registerFunction(PhlintFunction $function)
    {
        $this->functionMap[$function->getName()] = $function;
    }
}
