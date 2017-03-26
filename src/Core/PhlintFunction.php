<?php

namespace Simbigo\Phlint\Core;

abstract class PhlintFunction
{
    private $arguments = [];

    public function __construct()
    {
        $this->init();
    }

    protected function defineArgument($name, $required = true, $default = null)
    {
        $argument = new PhlintFunctionArgument($name, $required);
        $argument->setDefault($default);
        $this->arguments[$name] = $argument;
        return $argument;
    }

    protected function getArgument($name)
    {
        return $this->arguments[$name];
    }

    public function getDefinedArguments()
    {
        return $this->arguments;
    }

    protected function init()
    {

    }

    abstract public function call($arguments, $namedArguments);

    abstract public function getName();
}
