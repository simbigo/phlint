<?php

namespace Simbigo\Phlint;

use Simbigo\Phlint\AST\VariableAccessor;
use Simbigo\Phlint\Core\IFunction;
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
     * @param VariableAccessor $variable
     * @return mixed
     * @throws UndefinedVariable
     */
    public function getVariable(VariableAccessor $variable)
    {
        $name = $variable->getVariable()->getValue();
        if (array_key_exists($name, $this->variablesMap)) {
            return $this->variablesMap[$name];
        }

        if ($this->parentEnvironment !== null) {
            return $this->parentEnvironment->getVariable($variable);
        }

        throw new UndefinedVariable($variable->getVariable());
    }
}
