<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\ASTNode;
use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\Number;
use Simbigo\Phlint\Exceptions\InternalError;
use Simbigo\Phlint\Tokens\TokenType;

/**
 * Class Interpreter
 */
class Interpreter
{
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
        }

        throw new InternalError('Unknown node type: ' . get_class($node));
    }

    /**
     * @param Number $node
     * @return int|float
     */
    private function visitNumberNode(Number $node)
    {
        return $node->getValue()->getValue();
    }

    /**
     * @param ASTNode[] $statements
     */
    public function evaluate(array $statements)
    {
        foreach ($statements as $statement) {
            echo $this->visitNode($statement) . PHP_EOL;
        }
    }
}
