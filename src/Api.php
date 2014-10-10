<?php

namespace Geocode;

use Geocode\Model\Config;

class Api
{
    /**
     * @var Config
     */
    protected $config = null;

    public function __construct(Config $conf = null)
    {
        $this->config = $conf;
    }

    /**
     * @param Config $conf
     * @return $this
     */
    public function setConfig(Config $conf)
    {
        $this->config = $conf;
        return $this;
    }

    /**
     * @param bool $loadDefault
     * @return Config|null
     */
    public function getConfig($loadDefault = false)
    {
        if ($this->config === null && $loadDefault === true) {
            //default property values should enable calls
            $this->setConfig(
                new Config()
            );
        }
        return $this->config;
    }
}