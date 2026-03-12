<?php

namespace HeimrichHannot\GoogleMapsBundle\MapBuilder;

use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Manager\OverlayManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MapBuilderFactory
{
    public function __construct(
        private readonly MapManager $mapManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OverlayManager $overlayManager,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function build(): MapBuilder
    {
        return new MapBuilder(
            $this->mapManager,
            $this->eventDispatcher,
            $this->overlayManager,
            $this->requestStack,
        );
    }
}
