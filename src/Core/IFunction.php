<?php

namespace Simbigo\Phlint\Core;

use Simbigo\Phlint\Environment;
use Simbigo\Phlint\Interpreter;

/**
 * Interface IFunction
 *
 * @package Simbigo\Phlint\Core
 */
interface IFunction
{
    /**
     * @param Interpreter $interpreter
     * @param Environment $environment
     * @return mixed
     */
    public function call(Interpreter $interpreter, Environment $environment);

    /**
     * @param $name
     * @param bool $required
     * @param null $default
     */
    public function defineArgument($name, $required = true, $default = null);

    /**
     * @return mixed
     */
    public function getDefinedArguments();
}