<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\DataContainer;

use Contao\Backend;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Module extends Backend
{
    public const MODULE_GOOGLE_MAP = 'google_map';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }
}
