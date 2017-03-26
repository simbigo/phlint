<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Tokens\Token;
use Simbigo\Phlint\Tokens\TokenType;

class TokenPrinter
{
    const FORMAT_FULL_LIST = 'fullList';
    const FORMAT_SHORT_LIST = 'shortList';
    const FORMAT_SOURCE_NODE = 'sourceNode';
    const FORMAT_SYNTAX_HIGHLIGHT = 'shortList';

    protected $currentLine;
    protected $prevTokenLine;

    protected function printFullList(Token $token)
    {
        echo 'Token: {' . PHP_EOL;
        echo '    type: ' . $token->getType() . PHP_EOL;
        echo '    name: ' . TokenType::getName($token->getType()) . PHP_EOL;
        echo '    value: ' . $token->getValue() . PHP_EOL;
        echo '    line: ' . $token->getLine() . PHP_EOL;
        echo '    position: ' . $token->getPos() . PHP_EOL;
        echo '}' . PHP_EOL;
    }

    protected function printShortList(Token $token)
    {
        echo 'Token: { name: ' . TokenType::getName($token->getType()) . ' }' . PHP_EOL;
    }

    protected function printSourceNode(Token $token)
    {
        if ($token->getLine() !== $this->prevTokenLine) {
            echo PHP_EOL;
        }
        echo "\033[1;30;47m " . $token->getValue() . " \033[0m ";
    }

    /**
     * @param Token[] $tokens
     * @param string $format
     */
    public function printTokens(array $tokens, $format = self::FORMAT_FULL_LIST)
    {
        $method = 'print' . $format;
        foreach ($tokens as $token) {
            if ($this->currentLine === null) {
                $this->currentLine = $token->getLine();
                $this->prevTokenLine = $this->currentLine;
            }
            $this->$method($token);
            $this->prevTokenLine = $this->currentLine;
            $this->currentLine = $token->getLine();
        }
        echo PHP_EOL;
    }
}