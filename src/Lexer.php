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
     *
     */
    const KEYWORD_CLASS = 'class';
    /**
     *
     */
    const KEYWORD_ELSE = 'else';
    /**
     *
     */
    const KEYWORD_FUNC = 'func';
    /**
     *
     */
    const KEYWORD_IF = 'if';
    /**
     * @var string
     */
    private $currentChar;

    /**
     * @var array
     */
    private $keywords = [
        self::KEYWORD_CLASS => TokenType::T_KEYWORD_CLASS,
        self::KEYWORD_IF => TokenType::T_KEYWORD_IF,
        self::KEYWORD_ELSE => TokenType::T_KEYWORD_ELSE,
        self::KEYWORD_FUNC => TokenType::T_KEYWORD_FUNC,
    ];

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
     * @param $word
     * @return null|Token
     */
    private function makeKeywordToken($word)
    {
        $token = null;
        if (isset($this->keywords[$word])) {
            $token = $this->makeToken($this->keywords[$word], $word);
        }
        return $token;
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
     * @param int $offset
     * @return null|string
     */
    private function seeNext($offset = 1)
    {
        $offset = $this->pos + $offset;
        return mb_strlen($this->source) >= $offset ? mb_substr($this->source, $offset, 1) : null;
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

            switch ($this->currentChar) {
                case '=':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_SET_EQUALS, '=');
                case ';':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_SEMICOLON, ';');
                case ',':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_COMA, ',');
                case '+':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_PLUS, '+');
                case '-':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_MINUS, '-');
                case '*':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_MUL, '*');
                case '/':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_DIV, '/');
                case '(':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_LEFT_PARENTHESIS, '(');
                case ')':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_RIGHT_PARENTHESIS, ')');
                case '{':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_LEFT_BRACE, '{');
                case '}':
                    $this->readChar();
                    return $this->makeToken(TokenType::T_RIGHT_BRACE, '}');
                case '>':
                    $tokenType = TokenType::T_GREATER;
                    $tokenValue = '>';
                    if ($this->seeNext() === '=') {
                        $tokenType = TokenType::T_GREATER_EQUAL;
                        $tokenValue = '>=';
                    }
                    $this->readChar();
                    return $this->makeToken($tokenType, $tokenValue);
                case '<':
                    $tokenType = TokenType::T_LESS;
                    $tokenValue = '<';
                    if ($this->seeNext() === '=') {
                        $tokenType = TokenType::T_LESS_EQUAL;
                        $tokenValue = '<=';
                    }
                    $this->readChar();
                    $this->readChar();
                    return $this->makeToken($tokenType, $tokenValue);
                case '!':
                    $this->readChar();
                    $this->readChar();
                    return $this->makeToken(TokenType::T_BANG_EQUAL, '!=');
            }

            if ($this->isDigit($this->currentChar)) {
                return $this->makeToken(TokenType::T_NUMBER, $this->readNumber());
            }

            if ($this->isAlpha($this->currentChar)) {
                $word = $this->readWord();

                $token = $this->makeKeywordToken($word);
                if ($token === null) {
                    $token = $this->makeToken(TokenType::T_IDENTIFIER, $word);
                }
                return $token;
            }

            $this->error();
        }

        return $this->makeToken(TokenType::T_EOF, null);
    }

    /**
     * @param string $source
     * @return Token[]
     */
    public function tokenize(string $source)
    {
        $this->source = rtrim($source);
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