<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Collection;

use Ivory\GoogleMap\Map;

/**
 * Class MapCollection.
 */
class MapCollection
{
    /**
     * @var Map[]
     */
    protected $maps = [];

    public function addMap(Map $map, ?int $mapConfigId)
    {
        $this->maps[] = ['map' => $map, 'id' => $mapConfigId];
    }

    /**
     * Return only map objects.
     *
     * @return Map[]
     */
    public function getMaps()
    {
        return array_column($this->maps, 'map');
    }

    /**
     * Return map collection.
     *
     * @return array
     */
    public function getCollection()
    {
        return $this->maps;
    }

    public function isEmpty(): bool
    {
        return empty($this->maps);
    }
}
