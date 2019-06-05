<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Manager;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap;
use HeimrichHannot\GoogleMapsBundle\EventListener\MapRendererListener;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\UtilsBundle\File\FileUtil;
use HeimrichHannot\UtilsBundle\Location\LocationUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Control\FullscreenControl;
use Ivory\GoogleMap\Control\MapTypeControl;
use Ivory\GoogleMap\Control\RotateControl;
use Ivory\GoogleMap\Control\ScaleControl;
use Ivory\GoogleMap\Control\StreetViewControl;
use Ivory\GoogleMap\Control\ZoomControl;
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Overlay\MarkerClusterType;

class MapManager
{
    /**
     * @var ContaoFrameworkInterface
     */
    protected $framework;

    /**
     * @var OverlayManager
     */
    protected $overlayManager;

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
     * @var string
     */
    protected static $apiKey;

    const CACHE_KEY_PREFIX       = 'googleMaps_map';
    const GOOGLE_MAPS_STATIC_URL = 'https://maps.googleapis.com/maps/api/staticmap';

    public function __construct(
        ContaoFrameworkInterface $framework,
        OverlayManager $overlayManager,
        ModelUtil $modelUtil,
        LocationUtil $locationUtil,
        FileUtil $fileUtil,
        \Twig_Environment $twig
    ) {
        $this->framework      = $framework;
        $this->overlayManager = $overlayManager;
        $this->modelUtil      = $modelUtil;
        $this->locationUtil   = $locationUtil;
        $this->fileUtil       = $fileUtil;
        $this->twig           = $twig;
    }

    public function prepareMap(int $mapId, array $config = []): array
    {
        if (!$mapId) {
            return null;
        }

        if (null === ($mapConfig = $this->modelUtil->findModelInstanceByPk('tl_google_map', $mapId))) {
            return null;
        }

        // compute API key
        static::$apiKey = $this->computeApiKey($mapConfig);

        $templateData = $config;
        $map          = new Map();
        $map->setVariable('map_' . $mapId . '_' . substr(md5(time() . $mapId), 0, 8));

        // apply map config
        $htmlId = $mapConfig->htmlId ?: 'map_canvas_' . uniqid();
        $map->setHtmlId($htmlId);

        $this->setVisualization($map, $mapConfig);
        $this->setBehavior($map, $mapConfig);
        $this->setPositioning($map, $mapConfig);
        $this->addControls($map, $mapConfig);
        $this->addStaticMap($map, $mapConfig, $templateData);

        // add overlays
        if (null !== ($overlays = $this->modelUtil->findModelInstancesBy('tl_google_map_overlay', ['tl_google_map_overlay.pid=?', 'tl_google_map_overlay.published=?'], [$mapConfig->id, true]))) {
            foreach ($overlays as $overlay) {
                $this->overlayManager->addOverlayToMap($map, $overlay, static::$apiKey);
            }
        }

        $templateData['mapModel']       = $map;
        $templateData['mapConfig']      = $mapConfig->row();
        $templateData['mapConfigModel'] = $mapConfig;

        return $templateData;
    }

    public function render(int $mapId, array $config = [])
    {
        $templateData = $this->prepareMap($mapId, $config);

        if (null === $templateData) {
            return null;
        }

        /** @var Map $map */
        $map = $templateData['mapModel'];

        $mapHelper = MapHelperBuilder::create()->build();
        $apiHelper = ApiHelperBuilder::create()->setLanguage($this->getLanguage($mapId))->setKey(static::$apiKey)->build();

        $listener = new MapRendererListener($templateData['mapConfigModel'], $this, $mapHelper);

        $mapHelper->getEventDispatcher()->addListener('map.stylesheet', [$listener, 'renderStylesheet']);

        $templateData['mapHtml']     = $mapHelper->renderHtml($map);
        $templateData['mapCss']      = $mapHelper->renderStylesheet($map);
        $templateData['mapJs']       = $mapHelper->renderJavascript($map);
        $templateData['mapGoogleJs'] = $apiHelper->render([$map]);


        $template = $templateData['mapConfig']['template'] ?: 'gmap_map_default';
        $template = System::getContainer()->get('huh.utils.template')->getTemplate($template);

        return $this->twig->render($template, $templateData);
    }

    public function renderMapObject(Map $map)
    {
        $mapHelper = MapHelperBuilder::create()->build();
        $apiHelper = ApiHelperBuilder::create()->setLanguage($this->getLanguage())->setKey(static::$apiKey)->build();

        $templateData['mapHtml']     = $mapHelper->renderHtml($map);
        $templateData['mapCss']      = $mapHelper->renderStylesheet($map);
        $templateData['mapJs']       = $mapHelper->renderJavascript($map);
        $templateData['mapGoogleJs'] = $apiHelper->render([$map]);

        $template = $templateData['mapConfig']['template'] ?: 'gmap_map_default';
        $template = System::getContainer()->get('huh.utils.template')->getTemplate($template);

        return $this->twig->render($template, $templateData);
    }

    public function renderHtml(int $mapId, array $config = [])
    {
        $config['skipCss'] = true;
        $config['skipJs']  = true;

        return $this->render($mapId, $config);
    }

    public function renderCss(int $mapId, array $config = [])
    {
        $config['skipHtml'] = true;
        $config['skipJs']   = true;

        return $this->render($mapId, $config);
    }

    public function renderJs(int $mapId, array $config = [])
    {
        $config['skipHtml'] = true;
        $config['skipCss']  = true;

        return $this->render($mapId, $config);
    }

    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function setVisualization(Map $map, GoogleMapModel $mapConfig)
    {
        $map->setMapOption('mapTypeId', $mapConfig->mapType ?: MapTypeId::ROADMAP);

        switch ($mapConfig->sizeMode) {
            case GoogleMap::SIZE_MODE_ASPECT_RATIO:
                $map->setStylesheetOptions(
                    [
                        'width'          => '100%',
                        'height'         => '100%',
                        'padding-bottom' => (100 * (int)$mapConfig->aspectRatioY / (int)$mapConfig->aspectRatioX) . '%'
                    ]
                );

                break;
            case GoogleMap::SIZE_MODE_STATIC:
                $width  = StringUtil::deserialize($mapConfig->width, true);
                $height = StringUtil::deserialize($mapConfig->height, true);

                if (isset($width['value']) && isset($width['unit']) && isset($height['value']) && isset($height['unit'])) {
                    $map->setStylesheetOptions(
                        [
                            'width'  => $width['value'] . $width['unit'],
                            'height' => $height['value'] . $height['unit']
                        ]
                    );
                }

                break;
            case GoogleMap::SIZE_MODE_CSS:
                $map->setStylesheetOptions(
                    [
                        'width'  => 'auto',
                        'height' => 'auto'
                    ]
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
        $map->setMapOption('styles', json_decode($mapConfig->styles, true));
    }

    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function setBehavior(Map $map, GoogleMapModel $mapConfig)
    {
        $map->addMapOptions([
            'disableDoubleClickZoom' => $mapConfig->disableDoubleClickZoom ? true : false,
            'draggable'              => $mapConfig->draggable ? true : false,
            'scrollwheel'            => $mapConfig->scrollwheel ? true : false
        ]);
    }

    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function setPositioning(Map $map, GoogleMapModel $mapConfig)
    {
        switch ($mapConfig->positioningMode) {
            case GoogleMap::POSITIONING_MODE_STANDARD:
                $map->setMapOption('zoom', (int)$mapConfig->zoom ?: 3);
                $this->setCenter($map, $mapConfig);

                break;
            case GoogleMap::POSITIONING_MODE_BOUND:
                $map->setAutoZoom(true);
                $this->setBound($map, $mapConfig);

                break;
        }
    }

    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function setBound(Map $map, GoogleMapModel $mapConfig)
    {
        $southWest = new Coordinate();
        $northEast = new Coordinate();

        switch ($mapConfig->boundMode) {
            case GoogleMap::BOUND_MODE_COORDINATES:
                $southWest = new Coordinate($mapConfig->boundSouthWestLat, $mapConfig->boundSouthWestLng);
                $northEast = new Coordinate($mapConfig->boundNorthEastLat, $mapConfig->boundNorthEastLng);
                break;
            case GoogleMap::BOUND_MODE_AUTOMATIC:
                // TODO compute by pins
                break;
        }

        $map->setBound(new Bound($southWest, $northEast));
    }

    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function setCenter(Map $map, GoogleMapModel $mapConfig)
    {
        switch ($mapConfig->centerMode) {
            case GoogleMap::CENTER_MODE_COORDINATE:
                $map->setCenter(new Coordinate($mapConfig->centerLat, $mapConfig->centerLng));
                break;
            case GoogleMap::CENTER_MODE_STATIC_ADDRESS:
                if (!($coordinates = System::getContainer()->get('huh.utils.cache.database')->getValue(static::CACHE_KEY_PREFIX . $mapConfig->centerAddress))) {
                    $coordinates = $this->locationUtil->computeCoordinatesByString($mapConfig->centerAddress, static::$apiKey);

                    if (is_array($coordinates)) {
                        $coordinates = serialize($coordinates);
                        System::getContainer()->get('huh.utils.cache.database')->cacheValue(static::CACHE_KEY_PREFIX . $mapConfig->centerAddress, $coordinates);
                    }
                }

                if (is_string($coordinates)) {
                    $coordinates = StringUtil::deserialize($coordinates, true);

                    if (isset($coordinates['lat']) && isset($coordinates['lng'])) {
                        $map->setCenter(new Coordinate($coordinates['lat'], $coordinates['lng']));
                    }
                }

                break;
        }
    }

    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function addStaticMap(Map $map, GoogleMapModel $mapConfig, array &$templateData)
    {
        if ($mapConfig->staticMapNoscript) {
            $staticParams = [
                'center'  => $map->getCenter()->getLatitude() . ',' . $map->getCenter()->getLongitude(),
                'zoom'    => $map->getMapOption('zoom'),
                'size'    => $mapConfig->staticMapWidth . 'x' . $mapConfig->staticMapHeight,
                'maptype' => $map->getMapOption('mapTypeId'),
                'key'     => static::$apiKey
            ];

            $templateData['staticMapUrl'] = static::GOOGLE_MAPS_STATIC_URL . '?' . http_build_query($staticParams);
        }
    }


    /**
     * @param Map $map
     * @param GoogleMapModel $mapConfig
     */
    public function addControls(Map $map, GoogleMapModel $mapConfig)
    {
        // map type
        if ($mapConfig->addMapTypeControl) {
            $control = new MapTypeControl(
                StringUtil::deserialize($mapConfig->mapTypesAvailable, true),
                $mapConfig->mapTypeControlPos,
                $mapConfig->mapTypeControlStyle
            );

            $map->getControlManager()->setMapTypeControl($control);
        }

        // zoom
        if ($mapConfig->addZoomControl) {
            $control = new ZoomControl(
                $mapConfig->zoomControlPos
            );

            $map->getControlManager()->setZoomControl($control);
        }

        // rotate
        if ($mapConfig->addRotateControl) {
            $control = new RotateControl(
                $mapConfig->rotateControlPos
            );

            $map->getControlManager()->setRotateControl($control);
        }

        // street view
        if ($mapConfig->addStreetViewControl) {
            $control = new StreetViewControl(
                $mapConfig->streetViewControlPos
            );

            $map->getControlManager()->setStreetViewControl($control);
        }

        // fullscreen
        if ($mapConfig->addFullscreenControl) {
            $control = new FullscreenControl(
                $mapConfig->fullscreenControlPos
            );

            $map->getControlManager()->setFullscreenControl($control);
        }

        // scale
        if ($mapConfig->addScaleControl) {
            $control = new ScaleControl();

            $map->getControlManager()->setScaleControl($control);
        }
    }

    /**
     * @return string
     */
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

        $settings                    = new \stdClass();
        $settings->googlemaps_apiKey = Config::get('googlemaps_apiKey');

        return System::getContainer()->get('huh.utils.dca')->getOverridableProperty('googlemaps_apiKey', [
            $settings,
            ['tl_page', $objPage->rootId ?: $objPage->id],
            $mapConfig
        ]);
    }

    /**
     * * return language that is either configured at map config or set at page config
     *
     * @param int|null $id
     *
     * @return string
     */
    protected function getLanguage(int $id = null): string
    {
        global $objPage;

        if (!$id) {
            return $objPage->language;
        }

        if (null === ($config = $this->modelUtil->findModelInstanceByPk('tl_google_map', $id))) {
            return $objPage->language;
        }

        if (!$config->overrideLanguage) {
            return $objPage->language;
        }

        return $config->language;
    }
}
