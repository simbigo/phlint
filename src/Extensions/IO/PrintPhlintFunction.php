<?php

namespace Simbigo\Phlint\Extensions\IO;

use Simbigo\Phlint\Core\PhlintFunction;

class PrintPhlintFunction extends PhlintFunction
{
    public function getName()
    {
        return 'print';
    }

    public function call($arguments)
    {
        call_user_func_array('printf', [$arguments]);
        echo "\n";
    }
}
