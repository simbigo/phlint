<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Exceptions\ParseException;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

class Lexer
{
    /**
     * @var string
     */
    private $currentChar = '';
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

    private function endOfSource() : bool
    {
        return $this->currentChar === null;
    }

    /**
     * @param string $message
     * @param bool|true $appendInfo
     * @throws ParseException
     */
    private function error(string $message = '', bool $appendInfo = true)
    {
        if (empty($message)) {
            $message = 'Parse error.';
        }

        if ($appendInfo) {
            $message .= ' in position ' . $this->pos . '.';
        }

        throw new ParseException($message);
    }

    /**
     * @param string $char
     * @return bool
     */
    private function isAlpha(string $char)
    {
        return ctype_alpha($char);
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
    private function isWhitespace(string $char) : bool
    {
        return in_array($char, [' ', "\r", "\n", "\t"], true);
    }

    /**
     * @param int $tokenType
     * @param $value
     * @return Token
     */
    private function makeToken(int $tokenType, $value) : Token
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
     * @return Token
     */
    public function getNextToken(): Token
    {
        while (!$this->endOfSource()) {
            $this->readChar();

            if ($this->isDigit($this->currentChar)) {
                return $this->makeToken(TokenType::T_INTEGER, (int)$this->currentChar);
            }
            if ($this->currentChar === '+') {
                return $this->makeToken(TokenType::T_PLUS, '+');
            }
            if ($this->currentChar === '-') {
                return $this->makeToken(TokenType::T_MINUS, '-');
            }
            if ($this->currentChar === '*') {
                return $this->makeToken(TokenType::T_MUL, '*');
            }
            if ($this->currentChar === '/') {
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
        $this->currentChar = '';
        $this->pos = -1;
    }
}