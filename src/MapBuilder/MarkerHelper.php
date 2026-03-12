<?php

namespace HeimrichHannot\GoogleMapsBundle\MapBuilder;

use Symfony\Component\HttpFoundation\Request;

class MarkerHelper
{
    public function __construct(
        public readonly string $variable,
        public readonly Request $request
    ) {}

    public function trigger(): string
    {
        return sprintf(
            "if (typeof google !== 'undefined') {google.maps.event.trigger(%s, 'click');} return false;",
            $this->variable
        );
    }
}