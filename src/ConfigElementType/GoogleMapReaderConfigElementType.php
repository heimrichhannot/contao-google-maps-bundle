<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapBundle\ConfigElementType;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\Event\ReaderGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\ReaderBundle\ConfigElementType\ConfigElementType;
use HeimrichHannot\ReaderBundle\Item\ItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\EventDispatcher;

class GoogleMapReaderConfigElementType implements ConfigElementType
{
    const TYPE = 'google_map';

    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var MapManager
     */
    protected $mapManager;

    /**
     * @var ArrayUtil
     */
    protected $arrayUtil;

    /**
     * @var ModelUtil
     */
    protected $modelUtil;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    public function __construct(ContaoFrameworkInterface $framework)
    {
        $this->framework      = $framework;
        $this->mapManager     = System::getContainer()->get('huh.google_maps.map_manager');
        $this->overlayManager = System::getContainer()->get('huh.google_maps.overlay_manager');
        $this->arrayUtil      = System::getContainer()->get('huh.utils.array');
        $this->modelUtil      = System::getContainer()->get('huh.utils.model');
        $this->twig           = System::getContainer()->get('twig');
        $this->dispatcher     = System::getContainer()->get('event_dispatcher');
    }

    public function addToItemData(ItemInterface $item, ReaderConfigElementModel $readerConfigElement)
    {
        $config = $this->arrayUtil->removePrefix('googlemaps_', $readerConfigElement->row());

        $templateData = $this->mapManager->prepareMap($config['map'], $config);

        if (null === $templateData || !($templateData['mapModel'] instanceof Map)) {
            return null;
        }

        /** @var Map $map */
        $map       = $templateData['mapModel'];
        $mapConfig = $templateData['mapConfigModel'];

        $this->dispatcher->dispatch(ReaderGoogleMapBeforeRenderEvent::NAME, new ReaderGoogleMapBeforeRenderEvent($item, $map, $mapConfig, $readerConfigElement));

        $item->setFormattedValue($readerConfigElement->name, $this->mapManager->renderMapObject($map));
    }

    public function addOverlaysToMap(Map $map, $overlays)
    {

    }
}
