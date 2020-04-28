<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Contao\Model;
use Symfony\Component\EventDispatcher\Event;

class DlhMigrationModifyMapEvent extends Event
{
    const NAME = 'huh.google_maps.event.dlh_migration_modify_map';

    /**
     * @var Model
     */
    protected $legacyMap;

    /**
     * @var Model
     */
    protected $map;

    public function __construct(Model $legacyMap, Model $map)
    {
        $this->legacyMap = $legacyMap;
        $this->map = $map;
    }

    public function getLegacyMap(): Model
    {
        return $this->legacyMap;
    }

    public function setLegacyMap(Model $legacyMap): void
    {
        $this->legacyMap = $legacyMap;
    }

    public function getMap(): Model
    {
        return $this->map;
    }

    public function setMap(Model $map): void
    {
        $this->map = $map;
    }
}
