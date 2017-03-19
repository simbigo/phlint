<?php

namespace Simbigo\Phlint\Core;

/**
 * Class PhlintFunctionArgument
 */
class PhlintFunctionArgument
{
    /**
     * @var bool
     */
    private $default = false;
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $value;

    /**
     * PhlintFunctionArgument constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasDefaultValue()
    {
        return $this->default;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setDefault($value)
    {
        $this->default = true;
        $this->value = $value;
        return $this;
    }
}
