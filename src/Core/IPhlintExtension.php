<?php

namespace Simbigo\Phlint\Core;

use Simbigo\Phlint\Configuration\BaseConfiguration;
use Simbigo\Phlint\Environment;
use Simbigo\Phlint\Interpreter;

/**
 * Interface IPhlintExtension
 */
interface IPhlintExtension
{
    /**
     * @param Interpreter $interpreter
     * @param Environment $environment
     * @param BaseConfiguration $configuration
     * @return mixed
     */
    public function load(Interpreter $interpreter, Environment $environment, BaseConfiguration $configuration);
}
