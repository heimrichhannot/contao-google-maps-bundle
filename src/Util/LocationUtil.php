<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Util;

use Contao\Config;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Monolog\ContaoContext;
use Contao\System;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;

class LocationUtil
{
    const GOOGLE_MAPS_GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';

    protected ContaoFramework $framework;

    protected Utils $utils;

    protected LoggerInterface $logger;

    public function __construct(ContaoFramework $framework, Utils $utils, LoggerInterface $logger)
    {
        $this->framework = $framework;
        $this->utils = $utils;
        $this->logger = $logger;
    }

    /**
     * Computes the coordinates from a given address. Supported array keys are:.
     *
     * - street
     * - postal
     * - city
     * - country
     */
    public function computeCoordinatesByString(string $address, string $apiKey = ''): array|bool
    {
        $httpClient = HttpClient::create();
        $url = \sprintf(static::GOOGLE_MAPS_GEOCODE_URL, urlencode($address));

        $urlUtils = System::getContainer()->get(Utils::class)->url();
        if ($apiKey) {
            $url = $urlUtils->addQueryStringParameterToUrl('key='.$apiKey, $url);
        } elseif (Config::get('utilsGoogleApiKey')) {
            $url = $urlUtils->addQueryStringParameterToUrl('key='.Config::get('utilsGoogleApiKey'), $url);
        }

        try {
            $response = $httpClient->request('GET', $url);
            $result = $response->getContent();
            $data = json_decode($result, true);

            if (null === $data) {
                return false;
            }

            if (isset($data['error_message'])) {
                throw new \RuntimeException($data['error_message']);
            }

            return [
                'lat' => $response['results'][0]['geometry']['location']['lat'],
                'lng' => $response['results'][0]['geometry']['location']['lng'],
            ];
        } catch (\Exception $e) {
            $this->logger->error(
                $e->getMessage(),
                ['contao' => new ContaoContext(__METHOD__, ContaoContext::ERROR)],
            );

            return false;
        }
    }
}
