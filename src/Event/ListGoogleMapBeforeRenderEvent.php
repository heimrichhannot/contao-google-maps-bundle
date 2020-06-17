<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\ListBundle\Item\ItemInterface;
use HeimrichHannot\ListBundle\Model\ListConfigElementModel;
use Ivory\GoogleMap\Map;

class ListGoogleMapBeforeRenderEvent extends GoogleMapBeforeRenderEvent
{
    const NAME = 'huh.google_maps.event.list_before_render';

    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @var Map
     */
    protected $map;

    /**
     * @var GoogleMapModel
     */
    protected $mapConfig;

    /**
     * @var ListConfigElementModel
     */
    protected $listConfigElement;

    /**
     * ReaderGoogleMapBeforeRenderEvent constructor.
     *
     * @param $templateName
     * @param array $templateData
     */
    public function __construct(ItemInterface $item, Map $map, GoogleMapModel $mapConfig, ListConfigElementModel $listConfigElement)
    {
        $this->item = $item;
        $this->map = $map;
        $this->mapConfig = $mapConfig;
        $this->listConfigElement = $listConfigElement;
    }

    public function getReaderConfigElement(): ListConfigElementModel
    {
        return $this->listConfigElement;
    }

    /**
     * @param ListConfigElementModel $readerConfigElement
     */
    public function setReaderConfigElement(ListConfigElementModel $listConfigElement): void
    {
        $this->listConfigElement = $listConfigElement;
    }
}
