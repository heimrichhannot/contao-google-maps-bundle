<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
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
    public function __construct(
        private readonly ApiHelper $apiHelper,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ApiEvents::JAVASCRIPT => [
                ['onApiRender', -10],
            ],
        ];
    }

    public function onApiRender(ApiEvent $event): void
    {
        $event = $this->eventDispatcher->dispatch(new BeforeRenderApiEvent($this->apiHelper, $event));

        if ($event->getCode()) {
            $event->setCode($event->getCode());
        }
    }
}
