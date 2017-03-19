<?php

namespace Simbigo\Phlint\Tokens;

use ReflectionClass;

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
    const T_LEFT_BRACE = 8;
    const T_RIGHT_BRACE = 9;
    const T_COMMENT = 10;
    const T_IDENTIFIER = 11;
    const T_SET_EQUALS = 12;
    const T_SEMICOLON = 13;
    const T_UNDERSCORE = 14;
    const T_KEYWORD_CLASS = 15;


    private static $constants;

    public static function getName($tokenType)
    {
        if (self::$constants === null) {
            $reflector = new ReflectionClass(self::class);
            $constants = $reflector->getConstants();
            foreach ($constants as $name => $value) {
                self::$constants[$value] = $name;
            }
        }
        return self::$constants[$tokenType] ?? null;
    }
}