<?php

namespace HeimrichHannot\GoogleMapsBundle\DataContainer;

use Contao\Backend;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ReaderConfigElement extends Backend
{
    const CENTER_MODE_ADDRESS_FIELDS    = 'address_fields';
    const CENTER_MODE_COORDINATE_FIELDS = 'coordinate_fields';

    const CENTER_MODES = [
        self::CENTER_MODE_ADDRESS_FIELDS,
        self::CENTER_MODE_COORDINATE_FIELDS
    ];

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