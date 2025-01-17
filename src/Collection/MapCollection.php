<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
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
     * @var array<Map>
     */
    protected $maps = [];

    public function addMap(Map $map, ?int $mapConfigId): void
    {
        $this->maps[] = ['map' => $map, 'id' => $mapConfigId];
    }

    /**
     * Return only map objects.
     *
     * @return array<Map>
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
