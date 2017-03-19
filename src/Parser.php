<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\ASTNull;
use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\FunctionCall;
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
     * @return Number|BinaryOperation
     */
    private function expression()
    {
        $node = $this->term();

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
     * @return BinaryOperation|\Simbigo\Phlint\AST\Number|VariableAccessor|FunctionCall
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
        } elseif ($token->is(TokenType::T_IDENTIFIER)) {
            $this->pickup(TokenType::T_IDENTIFIER);
            return new VariableAccessor($token, new ASTNull(), VariableAccessor::ACTION_GET);
        }  elseif ($token->is(TokenType::T_IDENTIFIER)) {
            $this->pickup(TokenType::T_IDENTIFIER);
            return new VariableAccessor($token, new ASTNull(), VariableAccessor::ACTION_GET);
        } elseif ($token->is(TokenType::T_FUNCTION)) {
            return $this->functionCall();
        }

        return null;
    }

    /**
     * @return FunctionCall
     */
    private function functionCall()
    {
        $token = $this->token;
        $this->pickup(TokenType::T_IDENTIFIER);
        $this->pickup(TokenType::T_LEFT_PARENTHESIS);
        $node = $this->expression();
        $this->pickup(TokenType::T_RIGHT_PARENTHESIS);
        return new FunctionCall($token, $node);
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
            $message = 'Invalid token: ' . $this->token->getValue() . PHP_EOL;
            $message .= 'Wait: ' . $tokenType . PHP_EOL;
            $message .= 'Line: ' . $this->token->getLine() . PHP_EOL;
            $message .= 'Position: ' . $this->token->getPos();
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
            $statements[] = $this->statement();
            $this->pickup(TokenType::T_SEMICOLON);
        }
        return $statements;
    }

    /**
     * @return VariableAccessor
     */
    private function setter()
    {
        $token = $this->token;
        $this->pickup(TokenType::T_IDENTIFIER);
        $this->pickup(TokenType::T_SET_EQUALS);
        return new VariableAccessor($token, $this->expression(), VariableAccessor::ACTION_SET);
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

    private function seeNext($offset = 1)
    {
        $offset = $this->tokenIndex + $offset;
        return $this->tokens[$offset] ?? null;
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