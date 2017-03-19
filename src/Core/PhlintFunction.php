<?php

namespace Simbigo\Phlint\Core;

abstract class PhlintFunction
{
    private $arguments = [];

    protected function createArgument($name)
    {
        $argument = new PhlintFunctionArgument($name);
        $this->arguments[$name] = $argument;
        return $argument;
    }

    protected function getArgument($name)
    {
        return $this->arguments[$name];
    }

    protected function getDefinedArguments()
    {
        return $this->arguments;
    }

    abstract public function call($arguments);

    abstract public function getName();
}
