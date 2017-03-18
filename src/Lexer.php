<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Exceptions\ParseError;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

class Lexer
{
    /**
     * @var string
     */
    private $currentChar;
    /**
     * @var int
     */
    private $pos;
    /**
     * @var string
     */
    private $text;

    /**
     * @return bool
     */

    private function endOfSource(): bool
    {
        return $this->currentChar === null;
    }

    /**
     * @param string $message
     * @param bool|true $appendInfo
     * @throws ParseError
     */
    private function error(string $message = '', bool $appendInfo = true)
    {
        if (empty($message)) {
            $message = 'Parse error.';
        }

        if ($appendInfo) {
            $message .= ' in position ' . $this->pos . '.';
        }

        throw new ParseError($message);
    }

    /**
     * @param string $char
     * @return bool
     */
    private function isDigit(string $char)
    {
        return ctype_digit($char);
    }

    /**
     * @param string $char
     * @return bool
     */
    private function isWhitespace(string $char): bool
    {
        return in_array($char, [' ', "\r", "\n", "\t"], true);
    }

    /**
     * @param int $tokenType
     * @param $value
     * @return Token
     */
    private function makeToken(int $tokenType, $value): Token
    {
        return new Token($tokenType, $value);
    }

    /**
     *
     */
    private function readChar()
    {
        $this->pos++;
        if ($this->pos > mb_strlen($this->text) - 1) {
            $this->currentChar = null;
        } else {
            $this->currentChar = mb_substr($this->text, $this->pos, 1);
        }
    }

    /**
     * @return int
     */
    private function readInteger()
    {
        $result = '';
        while (!$this->endOfSource() && $this->isDigit($this->currentChar)) {
            $result .= $this->currentChar;
            $this->readChar();
        }
        return (int)$result;
    }

    /**
     *
     */
    private function skipWhitespace()
    {
        while (!$this->endOfSource() && $this->isWhitespace($this->currentChar)) {
            $this->readChar();
        }
    }

    /**
     * @return Token
     */
    public function getNextToken(): Token
    {
        while (!$this->endOfSource()) {

            if ($this->isWhitespace($this->currentChar)) {
                $this->skipWhitespace();
            }

            if ($this->isDigit($this->currentChar)) {
                return $this->makeToken(TokenType::T_INTEGER, $this->readInteger());
            }
            if ($this->currentChar === '+') {
                $this->readChar();
                return $this->makeToken(TokenType::T_PLUS, '+');
            }
            if ($this->currentChar === '-') {
                $this->readChar();
                return $this->makeToken(TokenType::T_MINUS, '-');
            }
            if ($this->currentChar === '*') {
                $this->readChar();
                return $this->makeToken(TokenType::T_MUL, '*');
            }
            if ($this->currentChar === '/') {
                $this->readChar();
                return $this->makeToken(TokenType::T_DIV, '/');
            }

            $this->error('Unknown character "' . $this->currentChar . '"');
        }

        return $this->makeToken(TokenType::T_EOF, null);
    }

    /**
     * @param $text
     */
    public function setText(string $text)
    {
        $this->text = $text;
        $this->pos = -1;
        $this->readChar();
    }

}