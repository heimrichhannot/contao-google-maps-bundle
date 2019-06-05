<?php

namespace HeimrichHannot\GoogleMapsBundle\DataContainer;

use Contao\Backend;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Content extends Backend
{
    const ELEMENT_GOOGLE_MAP = 'google_map';

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }
}