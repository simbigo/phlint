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
     * @return mixed|void
     */
    protected function parse($confString)
    {
        $this->ini = parse_ini_string($confString, true, INI_SCANNER_NORMAL);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $parts = explode('.', $key, 1);
        if (count($parts) === 1) {
            return $this->ini[$parts[0]] ?? $default;
        }
        return $this->ini[$parts[0]][$parts[1]] ?? $default;
    }
}
