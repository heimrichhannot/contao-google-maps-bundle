<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapBundle\ConfigElementType;

use Contao\Model;
use HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap;
use HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay;
use HeimrichHannot\GoogleMapsBundle\Event\GoogleMapsPrepareExternalItemEvent;
use HeimrichHannot\GoogleMapsBundle\Event\ListGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Event\ReaderGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
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
        $overlay = new OverlayModel();
        $overlay->setRow($item->getRaw());

        /** @var GoogleMapsPrepareExternalItemEvent $event */
        $event = $this->eventDispatcher->dispatch(new GoogleMapsPrepareExternalItemEvent(
                $item->getRaw(), $overlay, $configElement
        ));

        $collection = null;
        $overlayModel = $event->getOverlayModel();

        if ($overlayModel) {
            $collection = new Model\Collection([$overlayModel], OverlayModel::getTable());
        }

        $config = $this->arrayUtil->removePrefix('googlemaps_', $configElement->row());

        $templateData = $this->mapManager->prepareMap($config['map'], $config, $collection);

        if (null === $templateData || !($templateData['mapModel'] instanceof Map)) {
            return;
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];
        $mapConfig = $templateData['mapConfigModel'];

        if (GoogleMap::POSITIONING_MODE_STANDARD === $mapConfig->positioningMode
            && GoogleMap::CENTER_MODE_EXTERNAL && $mapConfig->centerMode
            && $overlayModel) {
            switch ($overlayModel->type) {
                case Overlay::TYPE_MARKER:
                    if ($map->getOverlayManager()->hasMarkers()) {
                        $marker = $map->getOverlayManager()->getMarkers()[0];
                        $map->setCenter($marker->getPosition());
                    }
                    break;
                case Overlay::TYPE_INFO_WINDOW:
                    if ($map->getOverlayManager()->hasInfoWindows()) {
                        $marker = $map->getOverlayManager()->getInfoWindows()[0];
                        $map->setCenter($marker->getPosition());
                    }
                    break;
                case Overlay::TYPE_CIRCLE:
                    if ($map->getOverlayManager()->hasCircles()) {
                        $marker = $map->getOverlayManager()->getCircles()[0];
                        $map->setCenter($marker->getCenter());
                    }
                    break;
            }
        }

        if ($item instanceof ListItemInterface) {
            $event = new ListGoogleMapBeforeRenderEvent($item, $map, $mapConfig, $configElement);
            // Fallback for version 1 usages
            $templateVariable = $configElement->templateVariable ?: 'map';
        } else {
            $event = new ReaderGoogleMapBeforeRenderEvent($item, $map, $mapConfig, $configElement);
            // Fallback for version 1 usages
            $templateVariable = $configElement->templateVariable ?: $configElement->name;
        }

        $this->eventDispatcher->dispatch($event, $event::NAME);

        $item->setFormattedValue(
            $templateVariable,
            $this->mapManager->renderMapObject($map, null, $templateData)
        );
    }
}
