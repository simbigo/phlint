<?php

namespace Simbigo\Phlint;

use Exception;
use Simbigo\Phlint\AST\BinaryOperation;
use Simbigo\Phlint\AST\Number;
use Simbigo\Phlint\Exceptions\ParseError;
use Simbigo\Phlint\Exceptions\SyntaxError;

/**
 * Class Phlint
 */
class Phlint
{
    /**
     * @var array
     */
    private $exitCommands = ['exit', 'quit'];
    /**
     * @var Lexer
     */
    private $lexer;
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var string
     */
    private $prompt = '[phlint]: ';

    /**
     * Phlint constructor.
     *
     * @param Interpreter $interpreter
     * @param Parser $parser
     * @param Lexer $lexer
     */
    public function __construct(Interpreter $interpreter, Parser $parser, Lexer $lexer)
    {
        mb_internal_encoding('UTF-8');

        $this->interpreter = $interpreter;
        $this->parser = $parser;
        $this->lexer = $lexer;
    }

    /**
     * @param $arguments
     * @return int|string
     */
    public function run($arguments)
    {
        if (count($arguments) > 0) {
            if ($arguments[0] === '-c') {
                $code = $arguments[1] ?? '';
                $exitCode = $this->runCode($code);
            } else {
                $exitCode = $this->runFile($arguments[0]);
            }
        } else {
            $exitCode = $this->runInteractive();
        }
        return $exitCode;
    }

    /**
     * @param $source
     * @return int
     * @throws Exception
     */
    public function runCode($source)
    {
        try {
            $tokens = $this->lexer->tokenize($source);
            $statements = $this->parser->parse($tokens);
            $this->interpreter->evaluate($statements);
        } catch (Exception $e) {
            if ($e instanceof ParseError || $e instanceof SyntaxError) {
                echo $e->getMessage() . PHP_EOL;
                return $e->getCode();
            }
            throw $e;
        }

        return 0;
    }

    /**
     * @param $file
     * @return int
     */
    public function runFile($file)
    {
        $source = file_get_contents($file);
        return $this->runCode($source);
    }

    /**
     * @return int|string
     */
    public function runInteractive()
    {
        $exitCode = 0;
        $exit = false;
        while (!$exit) {
            echo $this->prompt;

            $code = trim(fgets(STDIN));
            if (in_array($code, $this->exitCommands)) {
                $exit = true;
            } else {
                $exitCode = $this->runCode($code) . PHP_EOL;
            }
        }
        return $exitCode;
    }
}