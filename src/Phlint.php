<?php

namespace Simbigo\Phlint;

use Exception;
use Simbigo\Phlint\Configuration\BaseConfiguration;
use Simbigo\Phlint\Core\IPhlintExtension;
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
     * @var bool
     */
    private $dumpTokens = false;
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var array
     */
    private $exitCommands = ['exit', 'quit'];
    /**
     * @var IPhlintExtension[]
     */
    private $extensions = [];
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
     * @param BaseConfiguration $configuration
     * @param Interpreter $interpreter
     * @param Parser $parser
     * @param Lexer $lexer
     * @param Environment $environment
     */
    public function __construct(
        BaseConfiguration $configuration,
        Interpreter $interpreter,
        Parser $parser,
        Lexer $lexer,
        Environment $environment
    ) {
        $this->interpreter = $interpreter;
        $this->parser = $parser;
        $this->lexer = $lexer;
        $this->configuration = $configuration;
        $this->environment = $environment;
        $this->configure();
    }

    /**
     *
     */
    private function configure()
    {
        mb_internal_encoding('UTF-8');
        $this->loadExtensions();
    }

    /**
     * @param string $name
     * @param string|IPhlintExtension $extension
     */
    private function loadExtension($name, $extension)
    {
        $extensionInstance = is_string($extension) ? new $extension() : $extension;
        $this->extensions[$name] = $extensionInstance;
        $extensionInstance->load($this->interpreter, $this->environment, $this->configuration);
    }

    /**
     *
     */
    private function loadExtensions()
    {
        $extensions = $this->configuration->get('extensions');
        foreach ($extensions as $extensionName => $extensionClass) {
            $this->loadExtension($extensionName, $extensionClass);
        }
    }

    /**
     * @param Token[] $tokens
     */
    public function dumpTokens(array $tokens)
    {
        $printer = new TokenPrinter();
        $printer->printTokens($tokens, $this->dumpTokens);
    }

    /**
     * @param $arguments
     * @return int|string
     *
     * @todo refactoring to use run options
     */
    public function run($arguments)
    {
        if (count($arguments) > 0) {
            if ($arguments[0] === '-c') {
                $code = $arguments[1] ?? '';
                $exitCode = $this->runCode($code);
            } elseif ($arguments[0] === '-t') {
                $this->dumpTokens = $arguments[1];
                $exitCode = $this->runFile($arguments[2]);
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
            if ($this->dumpTokens) {
                $this->dumpTokens($tokens);
                exit;
            }
            $statements = $this->parser->parse($tokens);
            $this->interpreter->evaluate($this->environment, $statements);
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
