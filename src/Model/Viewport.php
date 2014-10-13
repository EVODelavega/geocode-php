<?php
namespace Geocode\Model;


class Viewport extends Data
{
    /**
     * @var Coordinates
     */
    protected $northeast = null;

    /**
     * @var Coordinates
     */
    protected $southwest = null;

    /**
     * @param Coordinates $ne
     * @return $this
     */
    public function setNortheast(Coordinates $ne)
    {
        $this->northeast = $ne;
        return $this;
    }

    /**
     * @return Coordinates
     */
    public function getNortheast()
    {
        return $this->northeast;
    }

    /**
     * @param Coordinates $sw
     * @return $this
     */
    public function setSouthwest(Coordinates $sw)
    {
        $this->southwest = $sw;
        return $this;
    }

    /**
     * @return Coordinates
     */
    public function getSouthwest()
    {
        return $this->southwest;
    }
}