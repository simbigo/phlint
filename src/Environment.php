<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\Core\IFunction;
use Simbigo\Phlint\Exceptions\UndefinedFunction;
use Simbigo\Phlint\Exceptions\UndefinedVariable;

/**
 * Class Environment
 */
class Environment
{
    /**
     * @var IFunction[]
     */
    private $functionsMap = [];
    /**
     * @var Environment
     */
    private $parentEnvironment;
    /**
     * @var array
     */
    private $variablesMap = [];

    /**
     * Environment constructor.
     *
     * @param Environment|null $parent
     */
    public function __construct(Environment $parent = null)
    {
        $this->parentEnvironment = $parent;
    }

    /**
     * @param string $name
     * @param IFunction $function
     */
    public function assignFunction(string $name, IFunction $function)
    {
        $this->functionsMap[$name] = $function;
    }

    /**
     * @param $name
     * @param $value
     */
    public function assignVariable(string $name, $value)
    {
        $this->variablesMap[$name] = $value;
    }

    /**
     * @param string $function
     * @return IFunction
     * @throws UndefinedFunction
     */
    public function getFunction($function)
    {
        if (array_key_exists($function, $this->functionsMap)) {
            return $this->functionsMap[$function];
        }

        if ($this->parentEnvironment !== null) {
            return $this->parentEnvironment->getFunction($function);
        }

        throw new UndefinedFunction($function);
    }

    /**
     * @param string $variable
     * @return mixed
     * @throws UndefinedVariable
     */
    public function getVariable(string $variable)
    {
        if (array_key_exists($variable, $this->variablesMap)) {
            return $this->variablesMap[$variable];
        }

        /*if ($this->parentEnvironment !== null) {
            return $this->parentEnvironment->getVariable($variable);
        }*/

        throw new UndefinedVariable($variable);
    }

    public function createLocalEnvironment()
    {
        return new static($this);
    }
}
