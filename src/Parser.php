<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\ASTFunction;
use Simbigo\Phlint\AST\ASTNull;
use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\CallFunctionArg;
use Simbigo\Phlint\AST\ClassDefinition;
use Simbigo\Phlint\AST\DeclareFunctionArg;
use Simbigo\Phlint\AST\FunctionCall;
use Simbigo\Phlint\AST\IfCondition;
use Simbigo\Phlint\AST\Number;
use Simbigo\Phlint\AST\VariableAccessor;
use Simbigo\Phlint\Exceptions\SyntaxError;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

/**
 * Class Parser
 */
class Parser
{
    /**
     * @var Token
     */
    private $token;
    /**
     * @var int
     */
    private $tokenIndex = -1;
    /**
     * @var Token[]
     */
    private $tokens;

    /**
     * @return ClassDefinition
     */
    private function classDeclaration()
    {
        $this->pickup(TokenType::T_KEYWORD_CLASS);
        $token = $this->token;
        $this->pickup(TokenType::T_IDENTIFIER);
        $this->pickup(TokenType::T_SET_EQUALS);
        $this->pickup(TokenType::T_LEFT_BRACE);
        $this->pickup(TokenType::T_RIGHT_BRACE);
        return new ClassDefinition($token);
    }

    private function declaration()
    {
        if ($this->token->is(TokenType::T_KEYWORD_FUNC)) {
            return $this->functionDeclaration();
        } elseif ($this->token->is(TokenType::T_IDENTIFIER) && $this->seeNext()->is(TokenType::T_SET_EQUALS)) {
            return $this->varDeclaration();
        }

        return $this->statementExpression();
    }

    /**
     * @return Number|BinaryOperation
     */
    private function expression()
    {
        if ($this->token->is(TokenType::T_IDENTIFIER)) {
            $token = $this->token;
            if ($this->seeNext()->is(TokenType::T_LEFT_PARENTHESIS)) {
                $node = $this->functionCall();
            } else {
                $this->pickup(TokenType::T_IDENTIFIER);
                $node = new VariableAccessor($token, new ASTNull(), VariableAccessor::ACTION_GET);
            }
        } else {
            $node = $this->term();
        }

        while ($this->token->isIn([TokenType::T_PLUS, TokenType::T_MINUS])) {
            $token = $this->token;
            if ($token->is(TokenType::T_PLUS)) {
                $this->pickup(TokenType::T_PLUS);
            } elseif ($token->is(TokenType::T_MINUS)) {
                $this->pickup(TokenType::T_MINUS);
            }

            $node = new BinaryOperation($token, $node, $this->term());
        }

        return $node;
    }

    /**
     * @return BinaryOperation|\Simbigo\Phlint\AST\Number|VariableAccessor|ASTFunction
     */
    private function factor()
    {
        $token = $this->token;
        if ($token->is(TokenType::T_NUMBER)) {
            $this->pickup(TokenType::T_NUMBER);
            return new Number($token);
        } elseif ($token->is(TokenType::T_LEFT_PARENTHESIS)) {
            $this->pickup(TokenType::T_LEFT_PARENTHESIS);
            $node = $this->expression();
            $this->pickup(TokenType::T_RIGHT_PARENTHESIS);
            return $node;
        }

        return null;
    }

    /**
     * @return ASTFunction
     */
    private function functionCall()
    {
        $token = $this->token;
        $this->pickup(TokenType::T_IDENTIFIER);
        $this->pickup(TokenType::T_LEFT_PARENTHESIS);
        $arguments = [];
        while ($this->token->getType() !== TokenType::T_RIGHT_PARENTHESIS) {
            if ($this->token->is(TokenType::T_IDENTIFIER) && $this->seeNext()->is(TokenType::T_SET_EQUALS)) {
                $argumentName = $this->token;
                $this->pickup(TokenType::T_IDENTIFIER);
                $this->pickup(TokenType::T_SET_EQUALS);
            } else {
                $argumentName = null;
            }

            $argumentValue = $this->expression();
            $arguments[] = new CallFunctionArg($argumentName, $argumentValue);

            if ($this->token->getType() !== TokenType::T_RIGHT_PARENTHESIS) {
                $this->pickup(TokenType::T_COMA);
            }
        }
        $this->pickup(TokenType::T_RIGHT_PARENTHESIS);
        return new ASTFunction($token, ASTFunction::ACTION_CALL, $arguments);
    }

    private function functionDeclaration()
    {
        $this->pickup(TokenType::T_KEYWORD_FUNC);
        $nameToken = $this->token;
        $this->pickup(TokenType::T_IDENTIFIER);
        $this->pickup(TokenType::T_LEFT_PARENTHESIS);
        $arguments = [];
        while ($this->token->getType() !== TokenType::T_RIGHT_PARENTHESIS) {
            $argumentName = $this->token;
            $this->pickup(TokenType::T_IDENTIFIER);
            $defaultValue = null;
            if ($this->token->is(TokenType::T_SET_EQUALS)) {
                $this->pickup(TokenType::T_SET_EQUALS);
                $defaultValue = $this->token;
                $this->pickup(TokenType::T_NUMBER); // todo allow other types
            }
            $arguments[] = new DeclareFunctionArg($argumentName, $defaultValue);
            if ($this->token->getType() !== TokenType::T_RIGHT_PARENTHESIS) {
                $this->pickup(TokenType::T_COMA);
            }
        }
        $this->pickup(TokenType::T_RIGHT_PARENTHESIS);
        $this->pickup(TokenType::T_SET_EQUALS);
        $this->pickup(TokenType::T_LEFT_BRACE);
        $this->pickup(TokenType::T_RIGHT_BRACE);
        return new ASTFunction($nameToken, ASTFunction::ACTION_DECLARE, $arguments);
    }

    /**
     * @return IfCondition
     */
    private function ifCondition()
    {
        $this->pickup(TokenType::T_KEYWORD_IF);
        $validCompareTokens = [
            TokenType::T_LESS,
            TokenType::T_GREATER,
            TokenType::T_LESS_EQUAL,
            TokenType::T_GREATER_EQUAL,
            TokenType::T_BANG_EQUAL,
            TokenType::T_EQUAL
        ];

        $leftArgument = $this->expression();
        $compareOperator = $this->token;
        if ($this->token->isIn($validCompareTokens)) {
            $this->pickup($this->token->getType());
        }
        $rightArgument = $this->expression();

        $this->pickup(TokenType::T_LEFT_BRACE);
        $trueBranch = $this->program();
        $this->pickup(TokenType::T_RIGHT_BRACE);

        $falseBranch = [new ASTNull()];
        if ($this->token->is(TokenType::T_KEYWORD_ELSE)) {
            $this->pickup(TokenType::T_KEYWORD_ELSE);
            $this->pickup(TokenType::T_LEFT_BRACE);
            $falseBranch = $this->program();
            $this->pickup(TokenType::T_RIGHT_BRACE);
        }

        return new IfCondition($compareOperator, $leftArgument, $rightArgument, $trueBranch, $falseBranch);
    }

    /**
     *
     */
    private function nextToken()
    {
        $this->tokenIndex++;
        $this->token = $this->tokens[$this->tokenIndex];
    }

    /**
     * @param $tokenType
     * @throws SyntaxError
     */
    private function pickup($tokenType)
    {
        if ($this->token->is($tokenType)) {
            $this->nextToken();
        } else {
            $message = 'Invalid token: ' . $this->token->getType() . ' [ ' . TokenType::getName($this->token->getType()) . ' ]' . PHP_EOL;
            $message .= 'Wait: ' . $tokenType . ' [ ' . TokenType::getName($tokenType) . ' ]' . PHP_EOL;
            $message .= 'Value: ' . $this->token->getValue() . PHP_EOL;
            $message .= 'Line: ' . ($this->token->getLine() + 1) . PHP_EOL;
            $message .= 'Position: ' . $this->token->getPos() . PHP_EOL . PHP_EOL;

            $tokenPos = $this->tokenIndex;
            while (isset($this->tokens[$tokenPos - 1]) && $this->tokens[$tokenPos - 1]->getLine() === $this->token->getLine()) {
                $tokenPos--;
            }
            $tokenPos++;

            $tokensForMessage = [];
            while (isset($this->tokens[$tokenPos]) && $this->tokens[$tokenPos]->getLine() === $this->token->getLine()) {
                $tokensForMessage[] = $this->tokens[$tokenPos];
                $tokenPos++;
            }

            $cursorPos = 0;
            $source = '';
            /** @var Token $item */
            foreach ($tokensForMessage as $item) {
                if ($item === $this->token) {
                    $cursorPos = mb_strlen($source);
                }
                $source .= $item->getValue() . ' ';
            }

            $message .= $source . PHP_EOL;
            $message .= str_repeat(' ', $cursorPos) . '^';

            throw new SyntaxError($message);
        }
    }

    /**
     * @return array
     */
    private function program()
    {
        $statements = [];
        while (!$this->token->is(TokenType::T_EOF)) {
            if ($this->token->is(TokenType::T_KEYWORD_CLASS)) {
                $statements[] = $this->classDeclaration();
            } else {
                $statements[] = $this->declaration();
            }
        }
        return $statements;
    }

    /**
     * @param int $offset
     * @return null|Token
     */
    private function seeNext($offset = 1)
    {
        $offset = $this->tokenIndex + $offset;
        return $this->tokens[$offset] ?? null;
    }

    /**
     * @return VariableAccessor
     */
    private function statement()
    {
        if ($this->seeNext()->is(TokenType::T_LEFT_PARENTHESIS)) {
            $node = $this->functionCall();
        } else {
            $node = $this->setter();
        }
        return $node;
    }

    /**
     * @return Number|BinaryOperation
     */
    private function term()
    {
        $node = $this->factor();
        while ($this->token->isIn([TokenType::T_MUL, TokenType::T_DIV])) {
            $token = $this->token;
            if ($token->is(TokenType::T_MUL)) {
                $this->pickup(TokenType::T_MUL);
            } elseif ($token->is(TokenType::T_DIV)) {
                $this->pickup(TokenType::T_DIV);
            }
            $node = new BinaryOperation($token, $node, $this->factor());
        }

        return $node;
    }

    private function varDeclaration()
    {
        $variable = $this->token;
        $this->pickup(TokenType::T_IDENTIFIER);
        $this->pickup(TokenType::T_SET_EQUALS);
        return new VariableAccessor($variable, $this->statementExpression(), VariableAccessor::ACTION_SET);
    }

    private function statementExpression()
    {
        $expression = $this->expression();
        $this->pickup(TokenType::T_SEMICOLON);
        return $expression;
    }

    /**
     * @param array $tokens
     * @return \Simbigo\Phlint\AST\Number[]|BinaryOperation[]
     */
    public function parse(array $tokens)
    {
        $this->tokens = $tokens;
        $this->nextToken();
        return $this->program();
    }
}