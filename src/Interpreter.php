<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Exceptions\SyntaxError;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

class Interpreter
{
    /**
     * @var Token
     */
    private $currentToken;
    /**
     * @var Lexer
     */
    private $lexer;

    /**
     * Interpreter constructor.
     *
     * @param Lexer $lexer
     */
    public function __construct(Lexer $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * @param $message
     * @throws SyntaxError
     */
    private function error($message = '')
    {
        throw new SyntaxError(trim('Invalid syntax. ' . $message));
    }

    /**
     *
     */
    private function factor()
    {
        $token = $this->currentToken;
        $this->pickUp(TokenType::T_INTEGER);
        return $token->getValue();
    }

    private function pickUp($tokenType)
    {
        if ($this->currentToken->is($tokenType)) {
            $this->currentToken = $this->lexer->getNextToken();
        } else {
            $this->error();
        }
    }

    /**
     * @return mixed
     */
    private function term()
    {
        $result = $this->factor();

        $operators = [
            TokenType::T_MUL,
            TokenType::T_DIV
        ];

        while (in_array($this->currentToken->getType(), $operators, true)) {
            $token = $this->currentToken;
            if ($token->is(TokenType::T_MUL)) {
                $this->pickUp(TokenType::T_MUL);
                $result *= $this->factor();
            } elseif ($token->is(TokenType::T_DIV)) {
                $this->pickUp(TokenType::T_DIV);
                $result /= $this->factor();
            }
        }

        return $result;
    }

    /**
     * @param $text
     * @return mixed
     */
    public function evaluate($text)
    {
        $this->lexer->setText($text);

        $this->currentToken = $this->lexer->getNextToken();
        $result = $this->term();

        $operators = [
            TokenType::T_PLUS,
            TokenType::T_MINUS
        ];

        while (in_array($this->currentToken->getType(), $operators, true)) {
            if ($this->currentToken->is(TokenType::T_PLUS)) {
                $this->pickUp(TokenType::T_PLUS);
                $result += $this->term();
            } elseif ($this->currentToken->is(TokenType::T_MINUS)) {
                $this->pickUp(TokenType::T_MINUS);
                $result -= $this->term();
            }
        }
        return $result;
    }
}
