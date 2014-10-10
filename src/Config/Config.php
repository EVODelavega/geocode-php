<?php
namespace Geocode\Config;


class Config
{
    const OUTPUT_JSON = 'json';
    const OUTPUT_XML = 'xml';

    const PROTOCOL_HTTP = 'http';
    const PROTOCOL_HTTPS = 'https';

    /**
     * @var string
     */
    protected $output = self::OUTPUT_JSON;

    /**
     * @var string
     */
    protected $protocol = self::PROTOCOL_HTTPS;

    /**
     * @var string
     */
    protected $url = '%s://maps.googleapis.com/maps/api/geocode/%s?';

    /**
     * @var null|string
     */
    protected $key = null;

    /**
     * @param array|\stdClass|\Traversable|null $mixed
     */
    public function __construct($mixed = null)
    {
        if ($mixed instanceof \stdClass || $mixed instanceof \Traversable || is_array($mixed)) {
            $this->setBulk($mixed);
        }
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasKey()
    {
        return ($this->key !== null);
    }

    /**
     * @return null|string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $protocol
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setProtocol($protocol)
    {
        if ($protocol !== self::PROTOCOL_HTTPS && $protocol !== self::PROTOCOL_HTTP) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a valid protocol, use %s::PROTOCOL_* constants'.
                    $protocol,
                    __CLASS__
                )
            );
        }
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @return bool
     */
    public function isSecureProtocol()
    {
        return ($this->protocol === self::PROTOCOL_HTTPS);
    }

    /**
     * @param string $url
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setUrl($url)
    {
        if (!strstr($url, 'api/')) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a valid url, expected a url to contain "api/"',
                    $url
                )
            );
        }
        if ($url{0} !== '%') {
            $url = str_replace(
                array(
                    self::PROTOCOL_HTTP,
                    self::PROTOCOL_HTTPS
                ),
                '%s',
                $url
            );
        }
        if (substr($url, -3) !== '%s?') {
            if (strstr($url, 'geocode/')) {
                $url = substr($url, 0, strpos($url, 'geocode/') + 8).'%s?';//geocode/ is 8 long
            } else {
                $url = preg_replace(
                    '/(api\/[^\/]+\/).+$/',
                    '$1%s?',
                    $url
                );
            }
        }
        $this->url = $url;
        return $this;
    }

    /**
     * @param bool $formatted
     * @return string
     */
    public function getUrl($formatted = true)
    {
        if ($formatted === true) {
            return sprintf(
                $this->url,
                $this->protocol,
                $this->output
            );
        }
        return $this->url;
    }

    /**
     * @param string $out
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setOutput($out)
    {
        if ($out !== self::OUTPUT_JSON && $out !== self::OUTPUT_XML) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a valid output format, use %s::OUTPUT_* constants',
                    $out,
                    __CLASS__
                )
            );
        }
        $this->output = $out;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param array|\stdClass|\Traversable $mixed
     * @return $this
     */
    public function setBulk($mixed)
    {
        foreach ($mixed as $k => $v) {
            $setter = 'set'.implode(
                    '',
                    array_map(
                        'ucfirst',
                        explode(
                            '_',
                            $k
                        )
                    )
                );
            if (method_exists($this, $setter)) {
                $this->{$setter}($v);
            }
        }
        return $this;
    }
}