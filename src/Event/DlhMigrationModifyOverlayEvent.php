<?php

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Contao\Model;
use Symfony\Component\EventDispatcher\Event;

class DlhMigrationModifyOverlayEvent extends Event
{
    const NAME = 'huh.google_maps.event.dlh_migration_modify_overlay';

    /**
     * @var Model
     */
    protected $legacyOverlay;

    /**
     * @var Model
     */
    protected $overlay;

    /**
     * @var Model
     */
    protected $legacyMap;

    /**
     * @var Model
     */
    protected $map;

    public function __construct(Model $legacyOverlay, Model $overlay, Model $legacyMap, Model $map)
    {
        $this->legacyOverlay = $legacyOverlay;
        $this->overlay       = $overlay;
        $this->legacyMap     = $legacyMap;
        $this->map           = $map;
    }

    /**
     * @return Model
     */
    public function getLegacyOverlay(): Model
    {
        return $this->legacyOverlay;
    }

    /**
     * @param Model $legacyOverlay
     */
    public function setLegacyOverlay(Model $legacyOverlay): void
    {
        $this->legacyOverlay = $legacyOverlay;
    }

    /**
     * @return Model
     */
    public function getOverlay(): Model
    {
        return $this->overlay;
    }

    /**
     * @param Model $overlay
     */
    public function setOverlay(Model $overlay): void
    {
        $this->overlay = $overlay;
    }

    /**
     * @return Model
     */
    public function getLegacyMap(): Model
    {
        return $this->legacyMap;
    }

    /**
     * @param Model $legacyMap
     */
    public function setLegacyMap(Model $legacyMap): void
    {
        $this->legacyMap = $legacyMap;
    }

    /**
     * @return Model
     */
    public function getMap(): Model
    {
        return $this->map;
    }

    /**
     * @param Model $map
     */
    public function setMap(Model $map): void
    {
        $this->map = $map;
    }
}