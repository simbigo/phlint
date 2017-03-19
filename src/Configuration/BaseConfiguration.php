<?php

namespace Simbigo\Phlint\Configuration;

/**
 * Class BaseConfiguration
 */
abstract class BaseConfiguration
{
    /**
     * @param $confString
     * @return mixed
     */
    abstract protected function parse($confString);

    /**
     * @param $key
     * @return mixed
     */
    abstract public function get($key);

    /**
     * @param $filePath
     */
    public function read($filePath)
    {
        // @todo: if exists
        $config = file_get_contents($filePath);
        $this->parse($config);
    }
}
