<?php

namespace Simbigo\Phlint\Extensions\IO;

use Simbigo\Phlint\Core\IPhlintExtension;
use Simbigo\Phlint\Interpreter;

class Extension implements IPhlintExtension
{
    /**
     * @param Interpreter $interpreter
     * @return mixed
     */
    public function load(Interpreter $interpreter)
    {
        $interpreter->registerFunction(new PrintPhlintFunction());
    }
}