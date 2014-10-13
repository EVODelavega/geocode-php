<?php
namespace Geocode\Model;


class Geometry extends Data
{
    const LOCATION_ROOFTOP = 'ROOFTOP';
    const LOCATION_INTERPOLATED = 'RANGE_INTERPOLATED';
    const LOCATION_GEOCENTER = 'GEOMETRIC_CENTER';
    const LOCATION_APPROX = 'APPROXIMATE';

    /**
     * @var Coordinates
     */
    protected $location = null;

    /**
     * @var string
     */
    protected $locationType = null;

    /**
     * @var Viewport
     */
    protected $viewport = null;

    /**
     * @var array
     */
    private static $ValidLocations = null;

    /**
     * @param Viewport $vp
     * @return $this
     */
    public function setViewport(Viewport $vp)
    {
        $this->viewport = $vp;
        return $this;
    }

    /**
     * @return Viewport
     */
    public function getViewport()
    {
        return $this->viewport;
    }

    /**
     * @param Coordinates $coords
     * @return $this
     */
    public function setLocation(Coordinates $coords)
    {
        $this->location = $coords;
        return $this;
    }

    /**
     * @return Coordinates
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $type
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setLocationType($type)
    {
        if (static::$ValidLocations === null) {
            $this->setValidLocations();
        }
        if (!isset(static::$ValidLocations[$type])) {
            throw new \InvalidArgumentException(
                sprintf(
                    '%s is not a valid location type, use %s::LOCATION_* constants',
                    $type,
                    __CLASS__
                )
            );
        }
        $this->locationType = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocationType()
    {
        return $this->locationType;
    }

    /**
     * @return $this
     */
    private function setValidLocations()
    {
        static::$ValidLocations = array(
            self::LOCATION_APPROX       => true,
            self::LOCATION_GEOCENTER    => true,
            self::LOCATION_INTERPOLATED => true,
            self::LOCATION_ROOFTOP      => true
        );
        return $this;
    }
}