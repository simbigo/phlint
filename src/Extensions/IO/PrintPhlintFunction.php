<?php

namespace Simbigo\Phlint\Extensions\IO;

use Simbigo\Phlint\Core\PhlintFunction;

class PrintPhlintFunction extends PhlintFunction
{
    public function getName()
    {
        return 'print';
    }

    public function call($arguments, $namedArguments)
    {
        foreach ($arguments as $argument) {
            call_user_func('printf', $argument);
            echo "\n";
        }
    }
}
