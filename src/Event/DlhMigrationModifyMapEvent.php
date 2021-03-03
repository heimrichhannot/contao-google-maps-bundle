<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
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
     * @var object
     */
    protected $legacyMap;

    /**
     * @var Model
     */
    protected $map;

    public function __construct(object $legacyMap, Model $map)
    {
        $this->legacyMap = $legacyMap;
        $this->map = $map;
    }

    public function getLegacyMap(): object
    {
        return $this->legacyMap;
    }

    public function setLegacyMap(object $legacyMap): void
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
