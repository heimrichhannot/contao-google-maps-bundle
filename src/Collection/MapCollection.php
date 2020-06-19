<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\GoogleMapsBundle\Collection;


use Ivory\GoogleMap\Map;

/**
 * Class MapCollection
 * @package HeimrichHannot\GoogleMapsBundle\Collection
 *
 * Collect all active maps on a page.
 */
class MapCollection
{
    /**
     * @var Map[]
     */
    protected $maps = [];

    /**
     * @param Map $map
     */
    public function addMap(Map $map, ?int $mapConfigId) {
        $this->maps[] = ['map' => $map, 'id' => $mapConfigId];
    }

    /**
     * Return only map objects
     *
     * @return Map[]
     */
    public function getMaps()
    {
        return array_column($this->maps, 'map');
    }

    /**
     * Return map collection
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