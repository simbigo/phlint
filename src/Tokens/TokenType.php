<?php

namespace Simbigo\Phlint\Tokens;

/**
 * Class TokenType
 */
class TokenType
{
    const T_EOF = 0;
    const T_NUMBER = 1;
    const T_PLUS = 2;
    const T_MINUS = 3;
    const T_MUL = 4;
    const T_DIV = 5;
    const T_LEFT_PARENTHESIS = 6;
    const T_RIGHT_PARENTHESIS = 7;
    const T_COMMENT = 8;
    const T_IDENTIFIER = 9;
    const T_SET_EQUALS = 10;
    const T_SEMICOLON = 11;
    const T_UNDERSCORE = 12;
    const T_KEYWORD_CLASS = 13;
}