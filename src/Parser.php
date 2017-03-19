<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\Number;
use Simbigo\Phlint\AST\VariableAccessor;
use Simbigo\Phlint\Exceptions\SyntaxError;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

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
     * @return BinaryOperation|\Simbigo\Phlint\AST\Number
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

    private function program()
    {
        $statements = [];
        while (!$this->token->is(TokenType::T_EOF)) {
            $statements[] = $this->statement();
            $this->pickup(TokenType::T_SEMICOLON);
        }
        return $statements;
    }

    private function setter()
    {
        $token = $this->token;
        $this->pickup(TokenType::T_VARIABLE);
        $this->pickup(TokenType::T_SET_EQUALS);
        return new VariableAccessor($token, $this->expression(), VariableAccessor::ACTION_SET);
    }

    private function statement()
    {
        $node = $this->setter();
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