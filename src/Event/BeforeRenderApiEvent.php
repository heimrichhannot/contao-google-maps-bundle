<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Ivory\GoogleMap\Helper\ApiHelper;
use Ivory\GoogleMap\Helper\Event\ApiEvent;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeRenderApiEvent extends Event
{
    private ?string $code = null;
    private ApiHelper $apiHelper;
    private ApiEvent $event;

    public function __construct(ApiHelper $apiHelper, ApiEvent $event)
    {
        $this->apiHelper = $apiHelper;
        $this->event = $event;
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

    public function getApiEvent(): ApiEvent
    {
        return $this->event;
    }
}
