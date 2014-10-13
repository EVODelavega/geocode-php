<?php
namespace Geocode\Model;


class Address extends Data
{
    /**
     * @var int|string
     */
    protected $postalCode = null;

    /**
     * @var int|string
     */
    protected $streetNumber = null;

    /**
     * @var string
     */
    protected $streetName = null;

    /**
     * @var string
     */
    protected $countyProvice = null;

    /**
     * @var string
     */
    protected $stateDistrict = null;

    /**
     * @var string
     */
    protected $country = null;

    /**
     * @var string
     */
    protected $locality = null;

    /**
     * @var array
     */
    protected $shortAliases = array();

    /**
     * Override parent, to add set-by-string wizardry
     * If a string is passed, a correctly formatted address is expected
     *
     * @param null|array|\stdClass|\Traversable|string $mixed
     */
    public function __construct($mixed = null)
    {
        if (!is_string($mixed)) {
            return parent::__construct($mixed);//return to omit else
        }
        $this->setByAddressString($mixed);
        return $this;//return to silence IDE
    }

    public function setComponentObj(\stdClass $component)
    {
        $types = $component->types;
        $property = implode(
            '',
            array_map(
                'ucfirst',
                explode(
                    '_',
                    $types[0]
                )
            )
        );
        $this->{'set'.$property}($component->long_name);
        $this->shortAliases[$property] = $component->short_name;
        return $this;
    }

    /**
     * @param string $addr
     * @param array $order = null
     * @return $this
     * @throws \InvalidArgumentException
     */
    private function setByAddressString($addr, array $order = null)
    {
        if ($order === null) {
            //default format
            $order = array(
                'street',
                'locality',
                'state',
                'country'
            );
        }
        $parts = array_map(
            'trim',
            explode(',', $addr)
        );
        $rest = array();
        if (count($parts) !== count($order)) {//require correct lengths
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument length mismatch (%d address parts and %d order params)',
                    count($parts),
                    count($order)
                )
            );
        }
        foreach ($order as $k => $part) {
            $value = $parts[$k];
            if ($part === 'street') {
                if (ctype_digit($value{0})) {
                    preg_match('/^[^\s]+/', $value, $m);
                    $this->setStreetNumber($m[0])
                        ->setStreetName(
                            substr(
                                $value,
                                strlen($m[0])+1//length of number + space...
                            )
                        );
                } else {
                    $this->setStreetName($value);
                }
            } elseif ($part === 'state') {
                if (ctype_digit(substr($value, -1))) {
                    preg_match('/[^\s]+$/', $value, $m);//get zip
                    $this->setPostalCode($m[0])
                        ->setStateDistrict(
                            substr(
                                $value,
                                0,
                                strlen($value) - (strlen($m[0])+1)//substring + space
                            )
                        );
                } else {
                    $this->setStateDistrict($value);
                }
            } else {
                $rest[$part] = $value;
            }
        }
        if ($rest) {
            $this->setBulk($rest);
        }
        return $this;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $countyProvice
     * @return $this
     */
    public function setCountyProvice($countyProvice)
    {
        $this->countyProvice = $countyProvice;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountyProvice()
    {
        return $this->countyProvice;
    }

    /**
     * @param string $locality
     * @return $this
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param int|string $postalCode
     * @return $this
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $stateDistrict
     * @return $this
     */
    public function setStateDistrict($stateDistrict)
    {
        $this->stateDistrict = $stateDistrict;

        return $this;
    }

    /**
     * @return string
     */
    public function getStateDistrict()
    {
        return $this->stateDistrict;
    }

    /**
     * @param string $streetName
     * @return $this
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param int|string $streetNumber
     * @return $this
     */
    public function setStreetNumber($streetNumber)
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getStreetNumber()
    {
        return $this->streetNumber;
    }
}
