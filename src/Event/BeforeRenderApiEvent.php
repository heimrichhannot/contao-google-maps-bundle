<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use HeimrichHannot\GoogleMapsBundle\Collection\MapCollection;
use Ivory\GoogleMap\Helper\ApiHelper;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeRenderApiEvent extends Event
{
    private ?string $code = null;
    private ApiHelper $apiHelper;
    private MapCollection $mapCollection;

    public function __construct(ApiHelper $apiHelper, MapCollection $mapCollection)
    {
        $this->apiHelper = $apiHelper;
        $this->mapCollection = $mapCollection;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * If provided, it will be used over the default rendered code.
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getApiHelper(): ApiHelper
    {
        return $this->apiHelper;
    }

    public function getMapCollection(): MapCollection
    {
        return $this->mapCollection;
    }
}
