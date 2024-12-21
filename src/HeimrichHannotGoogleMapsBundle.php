<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle;

use HeimrichHannot\GoogleMapsBundle\DependencyInjection\GoogleMapsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotGoogleMapsBundle extends Bundle
{
    public function getContainerExtension(): GoogleMapsExtension
    {
        return new GoogleMapsExtension();
    }
}
