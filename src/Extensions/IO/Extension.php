<?php

namespace Simbigo\Phlint\Extensions\IO;

use Simbigo\Phlint\Configuration\BaseConfiguration;
use Simbigo\Phlint\Core\IPhlintExtension;
use Simbigo\Phlint\Environment;
use Simbigo\Phlint\Interpreter;

class Extension implements IPhlintExtension
{
    /**
     * @param Interpreter $interpreter
     * @param Environment $environment
     * @param BaseConfiguration $configuration
     * @return mixed
     */
    public function load(Interpreter $interpreter, Environment $environment, BaseConfiguration $configuration)
    {
        $environment->assignFunction('print', new PrintPhlintFunction($configuration));
    }
}