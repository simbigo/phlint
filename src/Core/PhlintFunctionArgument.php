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
     * @var bool
     */
    private $required;
    /**
     * @var
     */
    private $value;

    /**
     * PhlintFunctionArgument constructor.
     *
     * @param $name
     * @param bool $required
     */
    public function __construct($name, $required = true)
    {
        $this->name = $name;
        $this->required = $required;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->default;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->required;
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

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}
