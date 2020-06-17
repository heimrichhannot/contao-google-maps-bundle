<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle;

use HeimrichHannot\GoogleMapsBundle\DependencyInjection\GoogleMapsExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class HeimrichHannotContaoGoogleMapsBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        return new GoogleMapsExtension();
    }
}
