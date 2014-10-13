<?php
namespace Geocode\Model;


class Coordinates extends Data
{
    /**
     * @var float
     */
    protected $lat = null;

    /**
     * @var float
     */
    protected $lng = null;

    /**
     * @param float $lng
     * @return $this
     */
    public function setLng($lng)
    {
        $this->lng = (float) $lng;
        return $this;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param float $lat
     * @return $this
     */
    public function setLat($lat)
    {
        $this->lat = (float) $lat;
        return $this;
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }
}