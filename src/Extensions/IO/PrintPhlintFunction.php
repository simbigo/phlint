<?php

namespace Simbigo\Phlint\Extensions\IO;

use Simbigo\Phlint\Configuration\BaseConfiguration;
use Simbigo\Phlint\Core\PhlintFunction;
use Simbigo\Phlint\Environment;
use Simbigo\Phlint\Interpreter;

/**
 * Class PrintPhlintFunction
 */
class PrintPhlintFunction extends PhlintFunction
{
    /**
     * @var
     */
    private $separator;

    protected function init(BaseConfiguration $configuration)
    {
        parent::init($configuration);

        $this->separator = $configuration->get('io.print.sep', PHP_EOL);
        $this->defineArgument('arg');
    }

    /**
     * @param Interpreter $interpreter
     * @param Environment $environment
     * @return mixed|void
     */
    public function call(Interpreter $interpreter, Environment $environment)
    {
        call_user_func('printf', $environment->getVariable('arg'));
        echo PHP_EOL;
    }
}
