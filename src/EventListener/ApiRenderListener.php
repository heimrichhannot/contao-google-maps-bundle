<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderApiEvent;
use Ivory\GoogleMap\Helper\ApiHelper;
use Ivory\GoogleMap\Helper\Event\ApiEvent;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ApiRenderListener implements EventSubscriberInterface
{
    private ApiHelper $apiHelper;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApiHelper $apiHelper, EventDispatcherInterface $eventDispatcher)
    {
        $this->apiHelper = $apiHelper;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::JAVASCRIPT => [
                ['onApiRender', -10],
            ],
        ];
    }

    public function onApiRender(ApiEvent $event)
    {
        $event = $this->eventDispatcher->dispatch(new BeforeRenderApiEvent($this->apiHelper, $event));

        if ($event->getCode()) {
            $event->setCode($event->getCode());
        }
    }
}
