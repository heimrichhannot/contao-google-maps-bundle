<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Manager;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\InsertTag\InsertTagParser;
use Contao\FragmentTemplate;
use Contao\StringUtil;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\OverlayListener;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\GoogleMapsBundle\Util\LocationUtil;
use HeimrichHannot\UtilsBundle\Util\FileUtil;
use HeimrichHannot\UtilsBundle\Util\ModelUtil;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Base\Point;
use Ivory\GoogleMap\Base\Size;
use Ivory\GoogleMap\Event\Event;
use Ivory\GoogleMap\Event\MouseEvent;
use Ivory\GoogleMap\Layer\KmlLayer;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\Overlay\Icon;
use Ivory\GoogleMap\Overlay\InfoWindow;
use Ivory\GoogleMap\Overlay\Marker;
use Ivory\GoogleMap\Overlay\Polygon;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class OverlayManager
{
    const CACHE_KEY_PREFIX = 'googleMaps_overlay';

    const CACHE_TIME = 86400;

    protected ContaoFramework $framework;

    protected ModelUtil $modelUtil;

    protected LocationUtil $locationUtil;

    /**
     * @var string
     */
    protected static $apiKey;

    /**
     * @var array
     */
    protected static $markerVariableMapping = [];

    private FileUtil $fileUtil;

    private InsertTagParser $insertTagParser;

    private CacheInterface $cache;

    public function __construct(ContaoFramework $framework, ModelUtil $modelUtil, LocationUtil $locationUtil, FileUtil $fileUtil, InsertTagParser $insertTagParser, CacheInterface $cache)
    {
        $this->framework = $framework;
        $this->modelUtil = $modelUtil;
        $this->locationUtil = $locationUtil;
        $this->fileUtil = $fileUtil;
        $this->insertTagParser = $insertTagParser;
        $this->cache = $cache;
    }

    public function addOverlayToMap(Map $map, OverlayModel $overlayConfig, string $apiKey): void
    {
        $this->apiKey = $apiKey;

        switch ($overlayConfig->type) {
            case OverlayListener::TYPE_MARKER:
                [$marker, $events] = $this->prepareMarker($overlayConfig, $map);

                $map->getOverlayManager()->addMarker($marker);

                foreach ($events as $event) {
                    $map->getEventManager()->addDomEvent($event);
                }

                break;

            case OverlayListener::TYPE_INFO_WINDOW:
                $infoWindow = $this->prepareInfoWindow($overlayConfig);
                $infoWindow->setOpen(true);

                $map->getOverlayManager()->addInfoWindow($infoWindow);

                break;

            case OverlayListener::TYPE_KML_LAYER:
                $kmlLayer = $this->prepareKmlLayer($overlayConfig);

                $map->getLayerManager()->addKmlLayer($kmlLayer);

                break;

            case OverlayListener::TYPE_POLYGON:
                $polygon = $this->preparePolygon($overlayConfig);

                $map->getOverlayManager()->addPolygon($polygon);

                break;

            default:
                // TODO allow event subscribers
                break;
        }
    }

    public function addRoutingToInfoWindow(InfoWindow $infoWindow, OverlayModel $overlayConfig): void
    {
        $position = $infoWindow->getPosition();

        if ($overlayConfig->addRouting && $position) {
            $templateName = $overlayConfig->routingTemplate ?: 'gmap_routing_default';

            $template = new FragmentTemplate($templateName);
            $template->setData([
                'lat' => $position->getLatitude(),
                'lng' => $position->getLongitude(),
            ]);

            $routing = $template->parse();
            $infoWindow->setContent($infoWindow->getContent().$routing);
        }
    }

    /**
     * @param Marker|InfoWindow $overlay
     *
     * @throws \Exception
     */
    public function setPositioning($overlay, OverlayModel $overlayConfig): void
    {
        switch ($overlayConfig->positioningMode) {
            case OverlayListener::POSITIONING_MODE_COORDINATE:
                $overlay->setPosition(new Coordinate((float) $overlayConfig->positioningLat, (float) $overlayConfig->positioningLng));

                break;

            case OverlayListener::POSITIONING_MODE_STATIC_ADDRESS:
                $coordinates = $this->cache->get(
                    static::CACHE_KEY_PREFIX.$overlayConfig->positioningAddress,
                    function (ItemInterface $item) use ($overlayConfig) {
                        $item->expiresAfter(static::CACHE_TIME);

                        $coordinates = $this->locationUtil->computeCoordinatesByString($overlayConfig->positioningAddress, $this->apiKey);

                        if (false === $coordinates) {
                            trigger_error('Could not compute coordinates from address. Maybe your Google API key is invalid or geocoding API is not enabled.', E_USER_WARNING);

                            return null;
                        }

                        return \is_array($coordinates) ? serialize($coordinates) : null;
                    },
                );

                if (\is_string($coordinates)) {
                    $coordinates = StringUtil::deserialize($coordinates, true);

                    if (isset($coordinates['lat'], $coordinates['lng'])) {
                        $overlay->setPosition(new Coordinate((float) $coordinates['lat'], (float) $coordinates['lng']));
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

    public static function checkHex(string $hex): string
    {
        if ('' === trim($hex, '0..9A..Fa..f')) {
            return '#'.$hex;
        }

        return '#000000';
    }

    protected function prepareMarker(OverlayModel $overlayConfig, Map $map)
    {
        $events = [];
        $marker = new Marker(new Coordinate());
        $this->setPositioning($marker, $overlayConfig);

        static::$markerVariableMapping[$overlayConfig->id] = $marker->getVariable();

        switch ($overlayConfig->markerType) {
            case OverlayListener::MARKER_TYPE_SIMPLE:
                break;

            case OverlayListener::MARKER_TYPE_ICON:
                $icon = new Icon();

                // image file
                $filePath = $this->fileUtil->getPathFromUuid($overlayConfig->iconSrc);

                if ($filePath) {
                    $icon->setUrl($filePath);
                }

                // anchor
                $icon->setAnchor(new Point($overlayConfig->iconAnchorX ?? 0, $overlayConfig->iconAnchorY ?? 0));

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
            case OverlayListener::TITLE_MODE_TITLE_FIELD:
                $marker->setOption('title', $overlayConfig->title);

                break;

            case OverlayListener::TITLE_MODE_CUSTOM_TEXT:
                $marker->setOption('title', $overlayConfig->titleText);

                break;
        }

        // events
        if ($overlayConfig->clickEvent) {
            $marker->addOptions(['clickable' => true]);

            switch ($overlayConfig->clickEvent) {
                case OverlayListener::CLICK_EVENT_LINK:
                    $url = $this->insertTagParser->replace($overlayConfig->url);

                    $event = new Event(
                        $marker->getVariable(),
                        'click',
                        "function() {
                            var win = window.open('".$url."', '".($overlayConfig->target ? '_blank' : '_self')."');
                        }",
                    );

                    $events[] = $event;

                    break;

                case OverlayListener::CLICK_EVENT_INFO_WINDOW:
                    $infoWindow = $this->prepareInfoWindow($overlayConfig);
                    $infoWindow->setPixelOffset(new Size($overlayConfig->infoWindowAnchorX ?? 0, $overlayConfig->infoWindowAnchorY ?? 0));
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
                '<div class="wrapper" style="'.implode(' ', $sizing).'">'.$infoWindow->getContent().'</div>',
            );
        }

        if ($overlayConfig->zIndex) {
            $infoWindow->setOption('zIndex', (int) $overlayConfig->zIndex);
        }

        return $infoWindow;
    }

    protected function prepareKmlLayer(OverlayModel $overlayConfig)
    {
        $kmlLayer = new KmlLayer($overlayConfig->kmlUrl);

        if ($overlayConfig->kmlClickable) {
            $kmlLayer->setOption('clickable', (bool) $overlayConfig->kmlClickable);
        }

        if ($overlayConfig->kmlPreserveViewport) {
            $kmlLayer->setOption('preserveViewport', (bool) $overlayConfig->kmlPreserveViewport);
        }

        if ($overlayConfig->kmlScreenOverlays) {
            $kmlLayer->setOption('screenOverlays', (bool) $overlayConfig->kmlScreenOverlays);
        }

        if ($overlayConfig->kmlSuppressInfowindows) {
            $kmlLayer->setOption('suppressInfoWindows', (bool) $overlayConfig->kmlSuppressInfowindows);
        }

        if ($overlayConfig->zIndex) {
            $kmlLayer->setOption('zIndex', (int) $overlayConfig->zIndex);
        }

        return $kmlLayer;
    }

    protected function preparePolygon(OverlayModel $overlayConfig)
    {
        $polygon = new Polygon();

        // position settings
        $vertices = StringUtil::deserialize($overlayConfig->pathCoordinates, true);
        $verticesArray = [];

        foreach ($vertices as $vertex) {
            $verticesArray[] = new Coordinate((float) ($vertex['positioningLat'] ?? 0), (float) ($vertex['positioningLng'] ?? 0));
        }

        $polygon->setCoordinates($verticesArray);

        // stroke settings
        if ($overlayConfig->strokeWeight) {
            $polygon->setOption('strokeWeight', (int) $overlayConfig->strokeWeight);
        }

        if ($overlayConfig->strokeColor) {
            $polygon->setOption('strokeColor', self::checkHex($overlayConfig->strokeColor));
        }

        if ($overlayConfig->strokeOpacity) {
            $polygon->setOption('strokeOpacity', (float) $overlayConfig->strokeOpacity);
        }

        // fill settings
        if ($overlayConfig->fillColor) {
            $polygon->setOption('fillColor', self::checkHex($overlayConfig->fillColor));
        }

        if ($overlayConfig->fillOpacity) {
            $polygon->setOption('fillOpacity', (float) $overlayConfig->fillOpacity);
        }

        // other settings
        if ($overlayConfig->zIndex) {
            $polygon->setOption('zIndex', (int) $overlayConfig->zIndex);
        }

        return $polygon;
    }
}
