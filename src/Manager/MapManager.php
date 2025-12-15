<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Manager;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\FragmentTemplate;
use Contao\Model\Collection;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\Collection\MapCollection;
use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderMapEvent;
use HeimrichHannot\GoogleMapsBundle\EventListener\ApiRenderListener;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\GoogleMapListener;
use HeimrichHannot\GoogleMapsBundle\EventListener\MapRendererListener;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\GoogleMapsBundle\Util\LocationUtil;
use HeimrichHannot\UtilsBundle\Util\FileUtil;
use HeimrichHannot\UtilsBundle\Util\ModelUtil;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Control\FullscreenControl;
use Ivory\GoogleMap\Control\MapTypeControl;
use Ivory\GoogleMap\Control\RotateControl;
use Ivory\GoogleMap\Control\ScaleControl;
use Ivory\GoogleMap\Control\StreetViewControl;
use Ivory\GoogleMap\Control\ZoomControl;
use Ivory\GoogleMap\Helper\ApiHelper;
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Overlay\MarkerClusterType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MapManager
{
    const CACHE_KEY_PREFIX = 'googleMaps_map';

    const CACHE_TIME = 86400;

    const GOOGLE_MAPS_STATIC_URL = 'https://maps.googleapis.com/maps/api/staticmap';

    protected ContaoFramework $framework;

    protected OverlayManager $overlayManager;

    protected ModelUtil $modelUtil;

    protected LocationUtil $locationUtil;

    /**
     * @var string
     */
    protected static $apiKey;

    /**
     * Collections of all maps on a page.
     *
     * @var array<Map>
     */
    protected $maps = [];

    private FileUtil $fileUtil;

    private MapCollection $mapCollection;

    private EventDispatcherInterface $eventDispatcher;

    private CacheInterface $cache;

    public function __construct(ContaoFramework $framework, OverlayManager $overlayManager, ModelUtil $modelUtil, LocationUtil $locationUtil, FileUtil $fileUtil, MapCollection $mapCollection, EventDispatcherInterface $eventDispatcher, CacheInterface $cache)
    {
        $this->framework = $framework;
        $this->overlayManager = $overlayManager;
        $this->modelUtil = $modelUtil;
        $this->locationUtil = $locationUtil;
        $this->fileUtil = $fileUtil;
        $this->mapCollection = $mapCollection;
        $this->eventDispatcher = $eventDispatcher;
        $this->cache = $cache;
    }

    public function prepareMap(int $mapId, array $config = [], ?Collection $overlays = null): ?array
    {
        if (!$mapId) {
            return null;
        }

        if (null === ($mapConfig = $this->modelUtil->findModelInstanceByPk('tl_google_map', $mapId))) {
            return null;
        }

        // compute API key
        static::$apiKey = $this->computeApiKey($mapConfig);

        if (!static::$apiKey) {
            throw new \Exception('No api key has been defined for the google map with config ID '.$mapConfig->id.'.');
        }

        $templateData = $config;
        $map = new Map();
        $map->setVariable('map_'.$mapId.'_'.substr(md5(time().$mapId), 0, 8));

        // apply map config
        $htmlId = $mapConfig->htmlId ?: 'map_canvas_'.uniqid();
        $map->setHtmlId($htmlId);

        $this->setVisualization($map, $mapConfig);
        $this->setBehavior($map, $mapConfig);
        $this->setPositioning($map, $mapConfig);
        $this->addControls($map, $mapConfig);
        $this->addStaticMap($map, $mapConfig, $templateData);

        // add overlays
        if (null === $overlays) {
            $overlays = $this->modelUtil->findModelInstancesBy('tl_google_map_overlay', ['tl_google_map_overlay.pid=?', 'tl_google_map_overlay.published=?'], [$mapConfig->id, true]);
        }

        if (null !== $overlays) {
            foreach ($overlays as $overlay) {
                $this->overlayManager->addOverlayToMap($map, $overlay, static::$apiKey);
            }
        }

        $templateData['mapModel'] = $map;
        $templateData['mapConfig'] = $mapConfig->row();
        $templateData['mapConfigModel'] = $mapConfig;

        return $templateData;
    }

    public function render(int $mapId, array $config = [], ?Collection $overlays = null)
    {
        $templateData = $this->prepareMap($mapId, $config, $overlays);

        if (null === $templateData) {
            return null;
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];

        return $this->renderMapObject($map, $mapId, $templateData['mapConfigModel'], $templateData);
    }

    /**
     * @param int|null $mapId The map id (the database id)
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function renderMapObject(Map $map, ?int $mapId = null, $mapConfigModel = null, array $templateData = []): string
    {
        if (\is_array($mapConfigModel) && empty($templateData)) {
            $templateData = $mapConfigModel;
            trigger_deprecation('heimrichhannot/contao-google-maps-bundle', '2.10.0', 'Passing templateData as third element to renderMapObject is deprecated since version 2.10.0. Please update your code accordingly.');
        }

        if (!($mapConfigModel instanceof GoogleMapModel)) {
            $mapConfigModel = $templateData['mapConfigModel'] ?? null;
        }

        $mapHelper = MapHelperBuilder::create()->build();

        if ($mapConfigModel) {
            $listener = new MapRendererListener($templateData['mapConfigModel'], $this, $mapHelper, $this->framework);
            $mapHelper->getEventDispatcher()->addListener('map.stylesheet', [$listener, 'renderStylesheet']);
        }

        $templateData['mapHtml'] = $mapHelper->renderHtml($map);
        $templateData['mapCss'] = $mapHelper->renderStylesheet($map);
        $templateData['mapJs'] = $mapHelper->renderJavascript($map);
        $this->mapCollection->addMap($map, $mapId);

        $templateName = $templateData['mapConfig']['template'] ?: 'gmap_map_default';

        $event = new BeforeRenderMapEvent($templateName, $templateData, $map);
        $this->eventDispatcher->dispatch($event, BeforeRenderMapEvent::NAME);

        $template = new FragmentTemplate($event->getTemplate());
        $template->setData($event->getTemplateData());

        return $template->parse();
    }

    public function renderHtml(int $mapId, array $config = [])
    {
        $config['skipCss'] = true;
        $config['skipJs'] = true;

        return $this->render($mapId, $config);
    }

    public function renderCss(int $mapId, array $config = [])
    {
        $config['skipHtml'] = true;
        $config['skipJs'] = true;

        return $this->render($mapId, $config);
    }

    public function renderJs(int $mapId, array $config = [])
    {
        $config['skipHtml'] = true;
        $config['skipCss'] = true;

        return $this->render($mapId, $config);
    }

    /**
     * Render the google map api.
     */
    public function renderApi(): string
    {
        if ($this->mapCollection->isEmpty()) {
            return '';
        }
        $collection = $this->mapCollection->getCollection();

        if (1 === \count($collection) && isset($collection[0]['id'])) {
            $language = $this->getLanguage($collection[0]['id']);
        } else {
            $language = $this->getLanguage();
        }

        /** @var ApiHelper $apiHelper */
        $apiHelper = ApiHelperBuilder::create()
            ->setLanguage($language)
            ->setKey(static::$apiKey)
            ->build()
        ;

        $listener = new ApiRenderListener($apiHelper, $this->eventDispatcher);
        $apiHelper->getEventDispatcher()->addListener(ApiEvents::JAVASCRIPT, [$listener, 'onApiRender']);

        $output = $apiHelper->render($this->mapCollection->getMaps());
        
        // Add loading=async parameter to Google Maps API URL for better performance
        $output = preg_replace(
            '/(https:\/\/maps\.googleapis\.com\/maps\/api\/js\?[^"\']*)/i',
            '$1&loading=async',
            $output
        );

        return $output;
    }

    public function setVisualization(Map $map, GoogleMapModel $mapConfig): void
    {
        $map->setMapOption('mapTypeId', $mapConfig->mapType ?: MapTypeId::ROADMAP);

        switch ($mapConfig->sizeMode) {
            case GoogleMapListener::SIZE_MODE_ASPECT_RATIO:
                $map->setStylesheetOptions(
                    [
                        'width' => '100%',
                        'height' => '100%',
                        'padding-bottom' => (100 * (int) $mapConfig->aspectRatioY / (int) $mapConfig->aspectRatioX).'%',
                    ],
                );

                break;

            case GoogleMapListener::SIZE_MODE_STATIC:
                $width = StringUtil::deserialize($mapConfig->width, true);
                $height = StringUtil::deserialize($mapConfig->height, true);

                if (isset($width['value'], $width['unit'], $height['value'], $height['unit'])) {
                    $map->setStylesheetOptions(
                        [
                            'width' => $width['value'].$width['unit'],
                            'height' => $height['value'].$height['unit'],
                        ],
                    );
                }

                break;

            case GoogleMapListener::SIZE_MODE_CSS:
                $map->setStylesheetOptions(
                    [
                        'width' => '100%',
                        'height' => '100%',
                    ],
                );

                break;
        }

        // clustering
        if ($mapConfig->addClusterer) {
            $clusterer = $map->getOverlayManager()->getMarkerCluster();
            $clusterer->setType(MarkerClusterType::MARKER_CLUSTERER);

            if ($mapConfig->clustererImg) {
                $imagePath = $this->fileUtil->getPathFromUuid($mapConfig->clustererImg);
                $clusterer->setOption('imagePath', $imagePath);
            }
        }

        // styles
        $map->setMapOption('styles', json_decode(StringUtil::decodeEntities($mapConfig->styles), true));
    }

    public function setBehavior(Map $map, GoogleMapModel $mapConfig): void
    {
        $map->addMapOptions([
            'disableDoubleClickZoom' => (bool) $mapConfig->disableDoubleClickZoom,
            'draggable' => (bool) $mapConfig->draggable,
            'scrollwheel' => (bool) $mapConfig->scrollwheel,
        ]);
    }

    public function setPositioning(Map $map, GoogleMapModel $mapConfig): void
    {
        switch ($mapConfig->positioningMode) {
            case GoogleMapListener::POSITIONING_MODE_STANDARD:
                $map->setMapOption('zoom', (int) $mapConfig->zoom ?: 3);
                $this->setCenter($map, $mapConfig);

                break;

            case GoogleMapListener::POSITIONING_MODE_BOUND:
                $map->setAutoZoom(true);
                $this->setBound($map, $mapConfig);

                break;
        }
    }

    public function setBound(Map $map, GoogleMapModel $mapConfig): void
    {
        $southWest = new Coordinate();
        $northEast = new Coordinate();

        switch ($mapConfig->boundMode) {
            case GoogleMapListener::BOUND_MODE_COORDINATES:
                $southWest = new Coordinate((float) $mapConfig->boundSouthWestLat, (float) $mapConfig->boundSouthWestLng);
                $northEast = new Coordinate((float) $mapConfig->boundNorthEastLat, (float) $mapConfig->boundNorthEastLng);

                break;

            case GoogleMapListener::BOUND_MODE_AUTOMATIC:
                // TODO compute by pins
                break;
        }

        $map->setBound(new Bound($southWest, $northEast));
    }

    public function setCenter(Map $map, GoogleMapModel $mapConfig): void
    {
        switch ($mapConfig->centerMode) {
            case GoogleMapListener::CENTER_MODE_COORDINATE:
                $map->setCenter(new Coordinate((float) $mapConfig->centerLat, (float) $mapConfig->centerLng));

                break;

            case GoogleMapListener::CENTER_MODE_STATIC_ADDRESS:
                $coordinates = $this->cache->get(
                    static::CACHE_KEY_PREFIX.$mapConfig->centerAddress,
                    function (ItemInterface $item) use ($mapConfig) {
                        $item->expiresAfter(static::CACHE_TIME);

                        $coordinates = $this->locationUtil->computeCoordinatesByString($mapConfig->centerAddress, static::$apiKey);

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
                        $map->setCenter(new Coordinate((float) $coordinates['lat'], (float) $coordinates['lng']));
                    }
                }

                break;
        }
    }

    public function addStaticMap(Map $map, GoogleMapModel $mapConfig, array &$templateData): void
    {
        if ($mapConfig->staticMapNoscript) {
            $staticParams = [
                'center' => $map->getCenter()->getLatitude().','.$map->getCenter()->getLongitude(),
                'zoom' => $map->getMapOption('zoom'),
                'size' => $mapConfig->staticMapWidth.'x'.$mapConfig->staticMapHeight,
                'maptype' => $map->getMapOption('mapTypeId'),
                'key' => static::$apiKey,
            ];

            $templateData['staticMapUrl'] = static::GOOGLE_MAPS_STATIC_URL.'?'.http_build_query($staticParams);
        }
    }

    public function addControls(Map $map, GoogleMapModel $mapConfig): void
    {
        // map type
        if ($mapConfig->addMapTypeControl) {
            $control = new MapTypeControl(
                StringUtil::deserialize($mapConfig->mapTypesAvailable, true),
                $mapConfig->mapTypeControlPos,
                $mapConfig->mapTypeControlStyle,
            );

            $map->getControlManager()->setMapTypeControl($control);
        } else {
            // Explicitly disable map type control when not enabled
            $map->setMapOption('mapTypeControl', false);
        }

        // zoom
        if ($mapConfig->addZoomControl) {
            $control = new ZoomControl(
                $mapConfig->zoomControlPos,
            );

            $map->getControlManager()->setZoomControl($control);
        }

        // rotate
        if ($mapConfig->addRotateControl) {
            $control = new RotateControl(
                $mapConfig->rotateControlPos,
            );

            $map->getControlManager()->setRotateControl($control);
        } else {
            // Explicitly disable rotate control when not enabled
            $map->setMapOption('rotateControl', false);
        }

        // street view
        if ($mapConfig->addStreetViewControl) {
            $control = new StreetViewControl(
                $mapConfig->streetViewControlPos,
            );

            $map->getControlManager()->setStreetViewControl($control);
        } else {
            // Explicitly disable street view control when not enabled
            $map->setMapOption('streetViewControl', false);
        }

        // fullscreen
        if ($mapConfig->addFullscreenControl) {
            $control = new FullscreenControl(
                $mapConfig->fullscreenControlPos,
            );

            $map->getControlManager()->setFullscreenControl($control);
        } else {
            // Explicitly disable fullscreen control when not enabled
            $map->setMapOption('fullscreenControl', false);
        }

        // scale
        if ($mapConfig->addScaleControl) {
            $control = new ScaleControl();

            $map->getControlManager()->setScaleControl($control);
        } else {
            // Explicitly disable scale control when not enabled
            $map->setMapOption('scaleControl', false);
        }
    }

    public function getApiKey(): string
    {
        return static::$apiKey;
    }

    public function computeApiKey(GoogleMapModel $mapConfig)
    {
        if (static::$apiKey) {
            return static::$apiKey;
        }

        global $objPage;

        $settings = new \stdClass();
        $settings->googlemaps_apiKey = Config::get('utilsGoogleApiKey');

        if (!$settings->googlemaps_apiKey) {
            $settings->googlemaps_apiKey = Config::get('googlemaps_apiKey');
        }

        return System::getContainer()->get('huh.google_maps.utils.dca')->getOverridableProperty('googlemaps_apiKey', [
            $settings,
            ['tl_page', $objPage->rootId ?: $objPage->id],
            $mapConfig,
        ]);
    }

    /**
     * return language that is either configured at map config or set at page config.
     */
    public function getLanguage(?int $mapId = null): string
    {
        global $objPage;

        if (!$mapId) {
            return $objPage->language;
        }

        if (null === ($config = $this->modelUtil->findModelInstanceByPk('tl_google_map', $mapId))) {
            return $objPage->language;
        }

        if (!$config->overrideLanguage) {
            return $objPage->language;
        }

        return $config->language;
    }
}
