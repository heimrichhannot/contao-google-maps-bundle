<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\ConfigElementType;

use Contao\Model;
use Contao\Model\Collection;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementData;
use HeimrichHannot\ConfigElementTypeBundle\ConfigElementType\ConfigElementResult;
use HeimrichHannot\GoogleMapsBundle\Event\GoogleMapsPrepareExternalItemEvent;
use HeimrichHannot\GoogleMapsBundle\Event\ListGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\Event\ReaderGoogleMapBeforeRenderEvent;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\GoogleMapListener;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\OverlayListener;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\GoogleMapsBundle\Util\ArrayUtil;
use HeimrichHannot\ListBundle\ConfigElementType\ListConfigElementData;
use HeimrichHannot\ListBundle\ConfigElementType\ListConfigElementTypeInterface;
use HeimrichHannot\ListBundle\Item\ItemInterface as ListItemInterface;
use HeimrichHannot\ListBundle\Model\ListConfigElementModel;
use HeimrichHannot\ReaderBundle\ConfigElementType\ReaderConfigElementData;
use HeimrichHannot\ReaderBundle\ConfigElementType\ReaderConfigElementTypeInterface;
use HeimrichHannot\ReaderBundle\Item\ItemInterface as ReaderItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

if (class_exists(ReaderConfigElementTypeInterface::class) && class_exists(ListConfigElementTypeInterface::class)) {
    abstract class AbstractGoogleMapConfigElementType implements ListConfigElementTypeInterface, ReaderConfigElementTypeInterface
    {
    }
} else {
    abstract class AbstractGoogleMapConfigElementType implements ListConfigElementTypeInterface
    {
    }
}

class GoogleMapConfigElementType extends AbstractGoogleMapConfigElementType
{
    const TYPE = 'google_map';

    /**
     * @var MapManager
     */
    private $mapManager;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(MapManager $mapManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->mapManager = $mapManager;
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
     * @return ConfigElementResult
     *
     * @throws \Exception
     */
    public function applyConfiguration(ConfigElementData $configElementData)
    {
        $overlay = new OverlayModel();
        $overlay->setRow($configElementData->getItemData());

        /** @var GoogleMapsPrepareExternalItemEvent $event */
        $event = $this->eventDispatcher->dispatch(new GoogleMapsPrepareExternalItemEvent(
            $configElementData->getItemData(), $overlay, $configElementData->getConfiguration(),
        ));

        $collection = null;
        $overlayModel = $event->getOverlayModel();

        if ($overlayModel) {
            $collection = new Collection([$overlayModel], OverlayModel::getTable());
        }

        $config = ArrayUtil::removePrefix('googlemaps_', $configElementData->getConfiguration()->row());

        $templateData = $this->mapManager->prepareMap($config['map'], $config, $collection);

        if (null === $templateData || !($templateData['mapModel'] instanceof Map)) {
            return new ConfigElementResult(ConfigElementResult::TYPE_NONE, null);
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];
        $mapConfig = $templateData['mapConfigModel'];

        if (
            GoogleMapListener::POSITIONING_MODE_STANDARD === $mapConfig->positioningMode
            && GoogleMapListener::CENTER_MODE_EXTERNAL && $mapConfig->centerMode
            && $overlayModel
        ) {
            switch ($overlayModel->type) {
                case OverlayListener::TYPE_MARKER:
                    if ($map->getOverlayManager()->hasMarkers()) {
                        $marker = $map->getOverlayManager()->getMarkers()[0];
                        $map->setCenter($marker->getPosition());
                    }

                    break;

                case OverlayListener::TYPE_INFO_WINDOW:
                    if ($map->getOverlayManager()->hasInfoWindows()) {
                        $marker = $map->getOverlayManager()->getInfoWindows()[0];
                        $map->setCenter($marker->getPosition());
                    }

                    break;

                case OverlayListener::TYPE_CIRCLE:
                    if ($map->getOverlayManager()->hasCircles()) {
                        $marker = $map->getOverlayManager()->getCircles()[0];
                        $map->setCenter($marker->getCenter());
                    }

                    break;
            }
        }

        return [$map, $mapConfig, $templateData];

        // Preperation for config element type:        return new ConfigElementResult(
        // ConfigElementResult::TYPE_FORMATTED_VALUE,
        // $this->mapManager->renderMapObject($map, null, $templateData)        );
    }

    /**
     * @param ReaderConfigElementModel|ListConfigElementModel $configElement
     * @param ListItemInterface|ReaderItemInterface           $item
     */
    protected function addToItemData(Model $configElement, $item): void
    {
        [$map, $mapConfig, $templateData] = $this->applyConfiguration(new ConfigElementData($item->getRaw(), $configElement));

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
            $this->mapManager->renderMapObject($map, null, $templateData),
        );
    }
}
