<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Service;

use Contao\Config;
use Http\Client\HttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Service\Base\Location\CoordinateLocation;
use Ivory\GoogleMap\Service\Elevation\Request\PathElevationRequest;
use Ivory\GoogleMap\Service\Elevation\Request\PositionalElevationRequest;
use Ivory\GoogleMap\Service\Elevation\Response\ElevationResult;

class ElevationService
{
    const REQUEST_TYPE_PATH = 'path';

    const REQUEST_TYPE_POSTITIONAL = 'positional';

    const MAX_SAMPLES = 300;

    /**
     * @var \Ivory\GoogleMap\Service\Elevation\ElevationService
     */
    protected $service;

    /**
     * ElevationService constructor.
     */
    public function __construct(HttpClient $httpClient)
    {
        $this->service = new \Ivory\GoogleMap\Service\Elevation\ElevationService($httpClient,
            new GuzzleMessageFactory());
    }

    /**
     * @return array<ElevationResult>
     */
    public function getElevation(array $data = [])
    {
        $locations = $this->prepareCoordinates($data);
        $service = $this->getService();
        $request = $this->getRequest($locations);

        $service->setKey(Config::get('googlemaps_apiKey'));

        $response = $service->process($request);

        return $response->getResults();
    }

    public function prepareCoordinates(array $coordinates): array
    {
        $locations = [];
        $step = (int) ceil(\count($coordinates) / self::MAX_SAMPLES);

        foreach ($coordinates as $key => $coordinate) {
            if (0 !== $key % $step) {
                continue;
            }

            if (!\is_array($coordinate) && !\is_array($coordinate = explode(',', $coordinate))) {
                continue;
            }

            $locations[] = new CoordinateLocation(new Coordinate((float) $coordinate[0], (float) $coordinate[1]));
        }

        return $locations;
    }

    /**
     * @return PathElevationRequest|PositionalElevationRequest
     */
    public function getRequest(array $locations, $type = self::REQUEST_TYPE_POSTITIONAL)
    {
        switch ($type) {
            case self::REQUEST_TYPE_POSTITIONAL:
                return new PositionalElevationRequest($locations);

                break;

            case self::REQUEST_TYPE_PATH:
                return new PathElevationRequest([$locations[0], end($locations)]);

                break;
        }
    }

    public function setService(\Ivory\GoogleMap\Service\Elevation\ElevationService $service): void
    {
        $service->setKey(Config::get('googlemaps_apiKey'));
        $this->service = $service;
    }

    public function getService(): \Ivory\GoogleMap\Service\Elevation\ElevationService
    {
        return $this->service;
    }
}
