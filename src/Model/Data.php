<?php

namespace Geocode\Model;


abstract class Data
{
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