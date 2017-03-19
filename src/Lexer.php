<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Exceptions\ParseError;
use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

/**
 * Class Lexer
 */
class Lexer
{
    /**
     * Float pointer of numbers
     */
    const FLOAT_POINTER = '.';

    /**
     * @var string
     */
    private $currentChar;
    /**
     * @var int
     */
    private $line;
    /**
     * @var int
     */
    private $linePos;
    /**
     * @var int
     */
    private $pos;
    /**
     * @var string
     */
    private $source;

    /**
     * @return bool
     */

    private function endOfSource(): bool
    {
        return $this->currentChar === null;
    }

    /**
     * @param string $message
     * @throws ParseError
     */
    private function error(string $message = '')
    {
        $lines = explode("\n", $this->source);
        $line = $lines[$this->line];

        $message .= 'Invalid character "' . $this->currentChar . '"' . PHP_EOL;
        $message .= 'Line: ' . ($this->line + 1) . PHP_EOL;
        $message .= 'Position: ' . $this->linePos . PHP_EOL . PHP_EOL;
        $message .= $line . PHP_EOL;
        $message .= str_repeat(' ', $this->linePos) . '^';

        throw new ParseError($message);
    }

    /**
     * @param $char
     * @return bool
     */
    private function isAlpha($char)
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
        return new Token($tokenType, $this->line, $this->linePos, $value);
    }

    /**
     *
     */
    private function readChar()
    {
        $this->pos++;
        $this->linePos++;
        if ($this->pos > mb_strlen($this->source) - 1) {
            $this->currentChar = null;
        } else {
            $this->currentChar = mb_substr($this->source, $this->pos, 1);
        }

        if ($this->currentChar === "\n") {
            $this->line++;
            $this->linePos = 0;
        }
    }

    /**
     * @return float
     */

    private function readNumber()
    {
        $hasPointer = false;
        $result = '';
        while (!$this->endOfSource() && ($this->isDigit($this->currentChar) || $this->currentChar === self::FLOAT_POINTER)) {
            if ($this->currentChar === self::FLOAT_POINTER) {
                if ($hasPointer) {
                    $this->error('Invalid character "' . self::FLOAT_POINTER . '"');
                } else {
                    $hasPointer = true;
                }
            }

            $result .= $this->currentChar;
            $this->readChar();
        }

        return strpos($result, self::FLOAT_POINTER) === false ? (int)$result : (float)$result;
    }

    /**
     * @return string
     */
    private function readWord()
    {
        $result = '';
        while (!$this->endOfSource() && ($this->isAlpha($this->currentChar) || $this->currentChar === '_')) {
            $result .= $this->currentChar;
            $this->readChar();
        }
        return $result;
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
                return $this->makeToken(TokenType::T_NUMBER, $this->readNumber());
            }
            if ($this->isAlpha($this->currentChar)) {
                $word = $this->readWord();
                if ($this->currentChar === '(') {
                    return $this->makeToken(TokenType::T_FUNCTION, $word);
                } else {
                    return $this->makeToken(TokenType::T_VARIABLE, $word);
                }
            }
            if ($this->currentChar === '=') {
                $this->readChar();
                return $this->makeToken(TokenType::T_SET_EQUALS, '=');
            }
            if ($this->currentChar === ';') {
                $this->readChar();
                return $this->makeToken(TokenType::T_SEMICOLON, ';');
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
            if ($this->currentChar === '(') {
                $this->readChar();
                return $this->makeToken(TokenType::T_LEFT_PARENTHESIS, '(');
            }
            if ($this->currentChar === ')') {
                $this->readChar();
                return $this->makeToken(TokenType::T_RIGHT_PARENTHESIS, ')');
            }

            $this->error('Unknown character "' . $this->currentChar . '"');
        }

        return $this->makeToken(TokenType::T_EOF, null);
    }

    /**
     * @param string $source
     * @return Token[]
     */
    public function tokenize(string $source)
    {
        $this->source = $source;
        $this->pos = -1;
        $this->line = 0;
        $this->linePos = -1;
        $this->readChar();


        $tokens = [];
        $token = $this->getNextToken();
        while (!$token->is(TokenType::T_EOF)) {
            $tokens[] = $token;
            $token = $this->getNextToken();
        }
        $tokens[] = $token;

        return $tokens;
    }
}