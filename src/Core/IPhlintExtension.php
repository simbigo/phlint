<?php

namespace Simbigo\Phlint\Core;

use Simbigo\Phlint\Interpreter;

/**
 * Interface IPhlintExtension
 */
interface IPhlintExtension
{
    /**
     * @param Interpreter $interpreter
     * @return mixed
     */
    public function load(Interpreter $interpreter);
}
