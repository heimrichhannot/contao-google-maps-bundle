<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Manager;

use Contao\Controller;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\UtilsBundle\File\FileUtil;
use HeimrichHannot\UtilsBundle\Location\LocationUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Base\Point;
use Ivory\GoogleMap\Base\Size;
use Ivory\GoogleMap\Event\Event;
use Ivory\GoogleMap\Event\MouseEvent;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\InfoWindow;
use Ivory\GoogleMap\Overlay\Marker;

class OverlayManager
{
    const CACHE_KEY_PREFIX = 'googleMaps_overlay';
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    /**
     * @var ModelUtil
     */
    protected $modelUtil;

    /**
     * @var LocationUtil
     */
    protected $locationUtil;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected static $markerVariableMapping = [];

    public function __construct(
        ContaoFrameworkInterface $framework,
        ModelUtil $modelUtil,
        LocationUtil $locationUtil,
        FileUtil $fileUtil,
        \Twig_Environment $twig
    ) {
        $this->framework = $framework;
        $this->modelUtil = $modelUtil;
        $this->locationUtil = $locationUtil;
        $this->fileUtil = $fileUtil;
        $this->twig = $twig;
    }

    public function addOverlayToMap(Map $map, OverlayModel $overlayConfig, string $apiKey): void
    {
        $this->apiKey = $apiKey;

        switch ($overlayConfig->type) {
            case Overlay::TYPE_MARKER:
                list($marker, $events) = $this->prepareMarker($overlayConfig, $map);

                $map->getOverlayManager()->addMarker($marker);

                foreach ($events as $event) {
                    $map->getEventManager()->addDomEvent($event);
                }

                break;

            case Overlay::TYPE_INFO_WINDOW:
                $infoWindow = $this->prepareInfoWindow($overlayConfig);
                $infoWindow->setOpen(true);

                $map->getOverlayManager()->addInfoWindow($infoWindow);

                break;

            default:
                // TODO allow event subscribers
                break;
        }
    }

    public function addRoutingToInfoWindow(InfoWindow $infoWindow, OverlayModel $overlayConfig)
    {
        $position = $infoWindow->getPosition();

        if ($overlayConfig->addRouting && $position) {
            $template = $overlayConfig->routingTemplate ?: 'gmap_routing_default';
            $template = System::getContainer()->get('huh.utils.template')->getTemplate($template);

            $routing = $this->twig->render($template, [
                'lat' => $position->getLatitude(),
                'lng' => $position->getLongitude(),
            ]);

            $infoWindow->setContent($infoWindow->getContent().$routing);
        }
    }

    /**
     * @param Marker|InfoWindow $overlay
     *
     * @throws \Exception
     */
    public function setPositioning($overlay, OverlayModel $overlayConfig)
    {
        switch ($overlayConfig->positioningMode) {
            case Overlay::POSITIONING_MODE_COORDINATE:
                $overlay->setPosition(new Coordinate($overlayConfig->positioningLat, $overlayConfig->positioningLng));

                break;

            case Overlay::POSITIONING_MODE_STATIC_ADDRESS:
                if (!($coordinates = System::getContainer()->get('huh.utils.cache.database')->getValue(static::CACHE_KEY_PREFIX.$overlayConfig->positioningAddress))) {
                    $coordinates = $this->locationUtil->computeCoordinatesByString($overlayConfig->positioningAddress, $this->apiKey);

                    if (\is_array($coordinates)) {
                        $coordinates = serialize($coordinates);
                        System::getContainer()->get('huh.utils.cache.database')->cacheValue(static::CACHE_KEY_PREFIX.$overlayConfig->positioningAddress, $coordinates);
                    }
                }

                if (\is_string($coordinates)) {
                    $coordinates = StringUtil::deserialize($coordinates, true);

                    if (isset($coordinates['lat']) && isset($coordinates['lng'])) {
                        $overlay->setPosition(new Coordinate($coordinates['lat'], $coordinates['lng']));
                    }
                }

                break;
        }
    }

    public static function getMarkerVariableMapping(): array
    {
        return static::$markerVariableMapping;
    }

    public static function setMarkerVariableMapping(array $markerVariableMapping): void
    {
        static::$markerVariableMapping = $markerVariableMapping;
    }

    protected function prepareMarker(OverlayModel $overlayConfig, Map $map)
    {
        $events = [];
        $marker = new Marker(new Coordinate());
        $this->setPositioning($marker, $overlayConfig);

        static::$markerVariableMapping[$overlayConfig->id] = $marker->getVariable();

        switch ($overlayConfig->markerType) {
            case Overlay::MARKER_TYPE_SIMPLE:
                break;

            case Overlay::MARKER_TYPE_ICON:
                $icon = new Icon();

                // image file
                $filePath = $this->fileUtil->getPathFromUuid($overlayConfig->iconSrc);

                if ($filePath) {
                    $icon->setUrl($filePath);
                }

                // anchor
                $icon->setAnchor(new Point($overlayConfig->iconAnchorX, $overlayConfig->iconAnchorY));

                // size
                $width = StringUtil::deserialize($overlayConfig->iconWidth, true);
                $height = StringUtil::deserialize($overlayConfig->iconHeight, true);

                if ($width['value'] && $height['value']) {
                    $icon->setScaledSize(new Size($width['value'], $height['value'], $width['unit'], $height['unit']));
                } else {
                    throw new \Exception('The overlay ID '.$overlayConfig->id.' doesn\'t have a icon width and height set.');
                }

                $marker->setIcon($icon);

                break;
        }

        if ($overlayConfig->animation) {
            $marker->setAnimation($overlayConfig->animation);
        }

        if ($overlayConfig->zIndex) {
            $marker->setOption('zIndex', (int) $overlayConfig->zIndex);
        }

        // title
        switch ($overlayConfig->titleMode) {
            case Overlay::TITLE_MODE_TITLE_FIELD:
                $marker->setOption('title', $overlayConfig->title);

                break;

            case Overlay::TITLE_MODE_CUSTOM_TEXT:
                $marker->setOption('title', $overlayConfig->titleText);

                break;
        }

        // events
        if ($overlayConfig->clickEvent) {
            $marker->addOptions(['clickable' => true]);

            switch ($overlayConfig->clickEvent) {
                case Overlay::CLICK_EVENT_LINK:
                    /** @var Controller $controller */
                    $controller = $this->framework->getAdapter(Controller::class);
                    $url = $controller->replaceInsertTags($overlayConfig->url);

                    $event = new Event(
                        $marker->getVariable(),
                        'click',
                        "function() {
                            var win = window.open('".$url."', '".($overlayConfig->target ? '_blank' : '_self')."');
                        }"
                    );

                    $events[] = $event;

                    break;

                case Overlay::CLICK_EVENT_INFO_WINDOW:
                    $infoWindow = $this->prepareInfoWindow($overlayConfig);
                    $infoWindow->setPixelOffset(new Size($overlayConfig->infoWindowAnchorX, $overlayConfig->infoWindowAnchorY));
                    $infoWindow->setOpenEvent(MouseEvent::CLICK);
                    // caution: this autoOpen is different from the one in dlh google maps
                    $infoWindow->setAutoOpen(true);

                    $marker->setInfoWindow($infoWindow);

                    break;
            }
        }

        return [$marker, $events];
    }

    protected function prepareInfoWindow(OverlayModel $overlayConfig)
    {
        $infoWindow = new InfoWindow($overlayConfig->infoWindowText);
        $this->setPositioning($infoWindow, $overlayConfig);
        $this->addRoutingToInfoWindow($infoWindow, $overlayConfig);

        // size
        $width = StringUtil::deserialize($overlayConfig->infoWindowWidth, true);
        $height = StringUtil::deserialize($overlayConfig->infoWindowHeight, true);
        $sizing = [];

        if (isset($width['value']) && $width['value']) {
            $sizing[] = 'width: '.$width['value'].$width['unit'].';';
        }

        if (isset($height['value']) && $height['value']) {
            $sizing[] = 'height: '.$height['value'].$height['unit'].';';
        }

        if (!empty($sizing)) {
            $infoWindow->setContent(
                '<div class="wrapper" style="'.implode(' ', $sizing).'">'.$infoWindow->getContent().'</div>'
            );
        }

        if ($overlayConfig->zIndex) {
            $infoWindow->setOption('zIndex', (int) $overlayConfig->zIndex);
        }

        return $infoWindow;
    }
}
