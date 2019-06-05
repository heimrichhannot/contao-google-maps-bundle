<?php

namespace HeimrichHannot\GoogleMapsBundle\DataContainer;

use Contao\Backend;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Module extends Backend
{
    const MODULE_GOOGLE_MAP = 'google_map';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }
}