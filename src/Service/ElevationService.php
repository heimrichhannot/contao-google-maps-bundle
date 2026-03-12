<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Service;

use Contao\Config;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Ivory\GoogleMap\Base\Coordinate;
use Ivory\GoogleMap\Service\Base\Location\CoordinateLocation;
use Ivory\GoogleMap\Service\Elevation\Request\ElevationRequestInterface;
use Ivory\GoogleMap\Service\Elevation\Request\PathElevationRequest;
use Ivory\GoogleMap\Service\Elevation\Request\PositionalElevationRequest;
use Ivory\GoogleMap\Service\Elevation\Response\ElevationResult;
use Psr\Http\Client\ClientInterface;

class ElevationService
{
    public const REQUEST_TYPE_PATH = 'path';

    public const REQUEST_TYPE_POSTITIONAL = 'positional';

    public const MAX_SAMPLES = 300;

    /**
     * @var \Ivory\GoogleMap\Service\Elevation\ElevationService
     */
    protected $service;

    public function __construct(ClientInterface $httpClient)
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

            if (!\is_array($coordinate) && !\is_array($coordinate = explode(',', (string) $coordinate))) {
                continue;
            }

            $locations[] = new CoordinateLocation(new Coordinate((float) $coordinate[0], (float) $coordinate[1]));
        }

        return $locations;
    }

    /**
     * @return PathElevationRequest|PositionalElevationRequest|ElevationRequestInterface
     */
    public function getRequest(array $locations, $type = self::REQUEST_TYPE_POSTITIONAL): ElevationRequestInterface
    {
        return match ($type) {
            self::REQUEST_TYPE_POSTITIONAL => new PositionalElevationRequest($locations),
            self::REQUEST_TYPE_PATH => new PathElevationRequest([$locations[0], end($locations)]),
            default => throw new \RuntimeException('Unsupported request type'),
        };
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
