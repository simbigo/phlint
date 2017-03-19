<?php

namespace Simbigo\Phlint;

use Exception;
use Simbigo\Phlint\Configuration\BaseConfiguration;
use Simbigo\Phlint\Core\IPhlintExtension;
use Simbigo\Phlint\Core\PrintPhlintFunction;
use Simbigo\Phlint\Exceptions\ParseError;
use Simbigo\Phlint\Exceptions\SyntaxError;
use Simbigo\Phlint\Tokens\Token;

/**
 * Class Phlint
 */
class Phlint
{
    /**
     * @var BaseConfiguration
     */
    private $configuration;
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
     * @var IPhlintExtension[]
     */
    private $extensions = [];


    /**
     * Phlint constructor.
     *
     * @param BaseConfiguration $configuration
     * @param Interpreter $interpreter
     * @param Parser $parser
     * @param Lexer $lexer
     */
    public function __construct(
        BaseConfiguration $configuration,
        Interpreter $interpreter,
        Parser $parser,
        Lexer $lexer
    ) {
        mb_internal_encoding('UTF-8');

        $this->interpreter = $interpreter;
        $this->parser = $parser;
        $this->lexer = $lexer;
        $this->configuration = $configuration;
        $this->configure();
    }

    /**
     *
     */
    private function configure()
    {
        $this->loadExtensions();
    }

    private function loadExtensions()
    {
        $extensions = $this->configuration->get('extensions');
        foreach ($extensions as $extensionName => $extensionClass) {
            $this->loadExtension($extensionName, $extensionClass);
        }
    }

    /**
     * @param string $name
     * @param string|IPhlintExtension $extension
     */
    public function loadExtension($name, $extension)
    {
        $extensionInstance = is_string($extension) ? new $extension() : $extension;
        $this->extensions[$name] = $extensionInstance;
        $extensionInstance->load($this->interpreter);
    }

    /**
     * @param Token $token
     */
    public function dumpToken(Token $token)
    {
        echo 'Token: {' . PHP_EOL;
        echo '    type: ' . $token->getType() . PHP_EOL;
        echo '    value: ' . $token->getValue() . PHP_EOL;
        echo '    line: ' . $token->getLine() . PHP_EOL;
        echo '    position: ' . $token->getPos() . PHP_EOL;
        echo '}' . PHP_EOL;
    }

    /**
     * @param Token[] $tokens
     */
    public function dumpTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            $this->dumpToken($token);
        }
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
            //$this->printTokens($tokens); exit;
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