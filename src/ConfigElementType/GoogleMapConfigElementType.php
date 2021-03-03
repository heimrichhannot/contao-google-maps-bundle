<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapBundle\ConfigElementType;

use Contao\Model;
use HeimrichHannot\GoogleMapsBundle\Event\ListGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Event\ReaderGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\ListBundle\ConfigElementType\ListConfigElementData;
use HeimrichHannot\ListBundle\ConfigElementType\ListConfigElementTypeInterface;
use HeimrichHannot\ListBundle\Item\ItemInterface as ListItemInterface;
use HeimrichHannot\ListBundle\Model\ListConfigElementModel;
use HeimrichHannot\ReaderBundle\ConfigElementType\ReaderConfigElementData;
use HeimrichHannot\ReaderBundle\ConfigElementType\ReaderConfigElementTypeInterface;
use HeimrichHannot\ReaderBundle\Item\ItemInterface as ReaderItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class GoogleMapConfigElementType implements ListConfigElementTypeInterface, ReaderConfigElementTypeInterface
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
        $this->addToItemData($configElementData->getListConfigElement(), $configElementData->getItem());
    }

    public function addToReaderItemData(ReaderConfigElementData $configElementData): void
    {
        $this->addToItemData($configElementData->getReaderConfigElement(), $configElementData->getItem());
    }

    /**
     * @param ReaderConfigElementModel|ListConfigElementModel $configElement
     * @param ListItemInterface|ReaderItemInterface           $item
     */
    protected function addToItemData(Model $configElement, $item): void
    {
        $config = $this->arrayUtil->removePrefix('googlemaps_', $configElement->row());

        $templateData = $this->mapManager->prepareMap($config['map'], $config);

        if (null === $templateData || !($templateData['mapModel'] instanceof Map)) {
            return;
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];
        $mapConfig = $templateData['mapConfigModel'];

        if ($item instanceof ListItemInterface) {
            $event = new ListGoogleMapBeforeRenderEvent($item, $map, $mapConfig, $configElement);
            // Fallback for version 1 usages
            $templateVariable = $configElement->templateVariable ?: 'map';
        } else {
            $event = new ReaderGoogleMapBeforeRenderEvent($item, $map, $mapConfig, $configElement);
            // Fallback for version 1 usages
            $templateVariable = $configElement->templateVariable ?: $configElement->name;
        }

        /* @noinspection PhpMethodParametersCountMismatchInspection */
        $this->eventDispatcher->dispatch($event::NAME, $event);

        $item->setFormattedValue(
            $templateVariable,
            $this->mapManager->renderMapObject($map)
        );
    }
}
