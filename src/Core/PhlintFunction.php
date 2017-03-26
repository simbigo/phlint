<?php

namespace Simbigo\Phlint\Core;

use Simbigo\Phlint\Configuration\BaseConfiguration;

/**
 * Class PhlintFunction
 */
abstract class PhlintFunction implements IFunction
{
    /**
     * @var array
     */
    private $arguments = [];

    /**
     * PhlintFunction constructor.
     *
     * @param BaseConfiguration $configuration
     */
    public function __construct(BaseConfiguration $configuration)
    {
        $this->init($configuration);
    }

    /**
     * @param BaseConfiguration $configuration
     */
    protected function init(BaseConfiguration $configuration)
    {

    }

    /**
     * @param $name
     * @param bool $required
     * @param null $default
     */
    public function defineArgument($name, $required = true, $default = null)
    {
        $this->arguments[] = $required ? [$name] : [$name, $default];
    }

    /**
     * @return array
     */
    public function getDefinedArguments()
    {
        return $this->arguments;
    }


}
