<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Contao\Model;
use Symfony\Component\EventDispatcher\Event;

class DlhMigrationModifyOverlayEvent extends Event
{
    const NAME = 'huh.google_maps.event.dlh_migration_modify_overlay';

    /**
     * @var object
     */
    protected $legacyOverlay;

    /**
     * @var Model
     */
    protected $overlay;

    /**
     * @var object
     */
    protected $legacyMap;

    /**
     * @var Model
     */
    protected $map;

    public function __construct(object $legacyOverlay, Model $overlay, object $legacyMap, Model $map)
    {
        $this->legacyOverlay = $legacyOverlay;
        $this->overlay = $overlay;
        $this->legacyMap = $legacyMap;
        $this->map = $map;
    }

    public function getLegacyOverlay(): object
    {
        return $this->legacyOverlay;
    }

    public function setLegacyOverlay(object $legacyOverlay): void
    {
        $this->legacyOverlay = $legacyOverlay;
    }

    public function getOverlay(): Model
    {
        return $this->overlay;
    }

    public function setOverlay(Model $overlay): void
    {
        $this->overlay = $overlay;
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
