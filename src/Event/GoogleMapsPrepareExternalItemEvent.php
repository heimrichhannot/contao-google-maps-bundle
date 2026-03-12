<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Contao\Model;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use Symfony\Contracts\EventDispatcher\Event;

class GoogleMapsPrepareExternalItemEvent extends Event
{
    public function __construct(
        public readonly array $itemData,
        /**
         * The overlay model for the current item. Null if no marker should be added.
         */
        public ?OverlayModel $overlayModel,
        public Model|array|null $context = null,
    ) {
    }
}
