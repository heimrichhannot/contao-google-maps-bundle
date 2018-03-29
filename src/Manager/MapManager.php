<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Manager;

use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\StringUtil;
use HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\UtilsBundle\Cache\DatabaseCacheUtil;
use HeimrichHannot\UtilsBundle\Location\LocationUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Map;
use Ivory\GoogleMap\MapTypeId;

class MapManager
{
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
     * @var DatabaseCacheUtil
     */
    protected $databaseCacheUtil;

    public function __construct(
        ContaoFrameworkInterface $framework,
        ModelUtil $modelUtil,
        LocationUtil $locationUtil,
        DatabaseCacheUtil $databaseCacheUtil
    ) {
        $this->framework         = $framework;
        $this->modelUtil         = $modelUtil;
        $this->locationUtil      = $locationUtil;
        $this->databaseCacheUtil = $databaseCacheUtil;
    }

    public function prepareMap(array $elementData): ?Map
    {
        if (!isset($elementData['googlemaps_map']))
        {
            return null;
        }

        if (null === ($mapConfig = $this->modelUtil->findModelInstanceByPk('tl_google_map', $elementData['googlemaps_map'])))
        {
            return null;
        }

        $map = new Map();

        // apply map config
        $this->setVisualization($map, $mapConfig);
        $this->setPositioning($map, $mapConfig);

        return $map;
    }

    /**
     * @param Map            $map
     * @param GoogleMapModel $mapConfig
     */
    public function setVisualization(Map $map, GoogleMapModel $mapConfig)
    {
        $map->setMapOption('mapTypeId', $mapConfig->mapType ?: MapTypeId::ROADMAP);

        switch ($mapConfig->sizeMode)
        {
            case GoogleMap::SIZE_MODE_ASPECT_RATIO:
                $map->setStylesheetOptions(
                    [
                        'width'  => '100%',
                        'height' => '100%',
                        'padding-bottom' => (100 * (int) $mapConfig->aspectRatioY / (int) $mapConfig->aspectRatioX) . '%'
                    ]
                );

                break;
            case GoogleMap::SIZE_MODE_STATIC:
                $width = StringUtil::deserialize($mapConfig->width, true);
                $height = StringUtil::deserialize($mapConfig->height, true);

                if (isset($width['value']) && isset($width['unit']) && isset($height['value']) && isset($height['unit']))
                {
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

        $map->setMapOption('styles', json_decode($mapConfig->styles, true));
    }

    /**
     * @param Map            $map
     * @param GoogleMapModel $mapConfig
     */
    public function setPositioning(Map $map, GoogleMapModel $mapConfig)
    {
        switch ($mapConfig->positioningMode)
        {
            case GoogleMap::POSITIONING_MODE_STANDARD:
                $map->setMapOption('zoom', (int) $mapConfig->zoom ?: 3);
                $this->setCenter($map, $mapConfig);

                break;
            case GoogleMap::POSITIONING_MODE_BOUND:
                $map->setAutoZoom(true);
                $this->setBound($map, $mapConfig);

                break;
        }
    }

    /**
     * @param Map            $map
     * @param GoogleMapModel $mapConfig
     */
    public function setBound(Map $map, GoogleMapModel $mapConfig)
    {
        $southWest = new Coordinate();
        $northEast = new Coordinate();

        switch ($mapConfig->boundMode)
        {
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
     * @param Map            $map
     * @param GoogleMapModel $mapConfig
     */
    public function setCenter(Map $map, GoogleMapModel $mapConfig)
    {
        switch ($mapConfig->centerMode)
        {
            case GoogleMap::CENTER_MODE_COORDINATE:
                $map->setCenter(new Coordinate($mapConfig->centerLat, $mapConfig->centerLng));
                break;
            case GoogleMap::CENTER_MODE_STATIC_ADDRESS:
                if (!($coordinates = $this->databaseCacheUtil->getValue($mapConfig->centerAddress)))
                {
                    $coordinates = $this->locationUtil->computeCoordinatesByString($mapConfig->centerAddress);

                    if (is_array($coordinates))
                    {
                        $coordinates = serialize($coordinates);
                        $this->databaseCacheUtil->cacheValue($mapConfig->centerAddress, $coordinates);
                    }
                }

                if (is_string($coordinates))
                {
                    $coordinates = StringUtil::deserialize($coordinates, true);

                    if (isset($coordinates['lat']) && isset($coordinates['lng']))
                    {
                        $map->setCenter(new Coordinate($coordinates['lat'], $coordinates['lng']));
                    }
                }

                break;
        }
    }
}