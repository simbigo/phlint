<?php

namespace Simbigo\Phlint\Exceptions;

use Exception;

/**
 * Class UndefinedFunction
 */
class UndefinedFunction extends PhlintException
{
    /**
     * UndefinedFunction constructor.
     *
     * @param string $function
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct($function, $message = "", $code = 0, Exception $previous = null)
    {
        $message = 'Undefined function ' . $function . '()' . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }
}