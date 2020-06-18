<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapBundle\ConfigElementType;

use HeimrichHannot\GoogleMapsBundle\Event\ListGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\ListBundle\ConfigElementType\ListConfigElementData;
use HeimrichHannot\ListBundle\ConfigElementType\ListConfigElementTypeInterface;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GoogleMapConfigElementType implements ListConfigElementTypeInterface
{
    const TYPE = 'google_map';
    /**
     * @var MapManager
     */
    private $mapManager;
    /**
     * @var ArrayUtil
     */
    private $arrayUtil;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(MapManager $mapManager, EventDispatcherInterface $eventDispatcher, ArrayUtil $arrayUtil)
    {
        $this->mapManager = $mapManager;
        $this->arrayUtil = $arrayUtil;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getType(): string
    {
        return static::TYPE;
    }

    public function getPalette(): string
    {
        return '{config_legend},googlemaps_map,googlemaps_centerMode,googlemaps_skipHtml,googlemaps_skipCss,googlemaps_skipJs;';
    }

    public function addToListItemData(ListConfigElementData $configElementData): void
    {
        $listConfigElement = $configElementData->getListConfigElement();
        $item = $configElementData->getItem();

        $config = $this->arrayUtil->removePrefix('googlemaps_', $listConfigElement->row());

        $templateData = $this->mapManager->prepareMap($config['map'], $config);

        if (null === $templateData || !($templateData['mapModel'] instanceof Map)) {
            return;
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];
        $mapConfig = $templateData['mapConfigModel'];

        /* @noinspection PhpParamsInspection */
        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $this->eventDispatcher->dispatch(
            ListGoogleMapBeforeRenderEvent::NAME,
            new ListGoogleMapBeforeRenderEvent($item, $map, $mapConfig, $listConfigElement));

        $item->setFormattedValue(
            $listConfigElement->templateVariable ?: 'map',
            $this->mapManager->renderMapObject($map)
        );
    }
}
