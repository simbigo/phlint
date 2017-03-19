<?php

namespace Simbigo\Phlint\Configuration;

/**
 * Class IniConfig
 */
class IniConfiguration extends BaseConfiguration
{
    /**
     * @var
     */
    private $ini;

    /**
     * @param $confString
     */
    protected function parse($confString)
    {
        $this->ini = parse_ini_string($confString, true, INI_SCANNER_NORMAL);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        $parts = explode('.', $key);
        if (count($parts) === 1) {
            return $this->ini[$parts[0]];
        }
        return $this->ini[$parts[0]][$parts[1]];
    }
}
