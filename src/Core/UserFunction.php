<?php

namespace Simbigo\Phlint\Core;

use Simbigo\Phlint\Environment;
use Simbigo\Phlint\Interpreter;

/**
 * Class UserFunction
 */
class UserFunction implements IFunction
{
    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var array
     */
    private $defaultArguments = [];
    /**
     * @var
     */
    private $name;
    /**
     * @var array
     */
    private $statements;

    /**
     * UserFunction constructor.
     *
     * @param $name
     * @param array $statements
     */
    public function __construct($name, array $statements)
    {
        $this->name = $name;
        $this->statements = $statements;
    }

    /**
     * @param Interpreter $interpreter
     * @param Environment $environment
     * @return mixed|void
     */
    public function call(Interpreter $interpreter, Environment $environment)
    {
        $interpreter->evaluate($environment, $this->statements);
    }

    /**
     * @param $name
     * @param bool $required
     * @param null $default
     */
    public function defineArgument($name, $required = true, $default = null)
    {
        $this->arguments[] = $required ? [$name] : [$name, $default];
    }

    public function getDefinedArguments()
    {
        return $this->arguments;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}