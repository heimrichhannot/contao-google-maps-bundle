<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GoogleMapBeforeRenderEvent.
 */
class GoogleMapBeforeRenderEvent extends Event
{
    protected $item;

    /**
     * @var Map
     */
    protected $map;

    /**
     * @var GoogleMapModel
     */
    protected $mapConfig;

    public function getItem()
    {
        return $this->item;
    }

    public function setItem($item): void
    {
        $this->item = $item;
    }

    public function getMap(): Map
    {
        return $this->map;
    }

    public function setMap(Map $map): void
    {
        $this->map = $map;
    }

    public function getMapConfig(): GoogleMapModel
    {
        return $this->mapConfig;
    }

    public function setMapConfig(GoogleMapModel $mapConfig): void
    {
        $this->mapConfig = $mapConfig;
    }
}
