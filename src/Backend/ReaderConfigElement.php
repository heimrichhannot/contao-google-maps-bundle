<?php

namespace HeimrichHannot\GoogleMapsBundle\Backend;

use Contao\Backend;

class ReaderConfigElement extends Backend
{
    const CENTER_MODE_ADDRESS_FIELDS     = 'address_fields';
    const CENTER_MODE_COORDINATE_FIELDS = 'coordinate_fields';

    const CENTER_MODES = [
        self::CENTER_MODE_ADDRESS_FIELDS,
        self::CENTER_MODE_COORDINATE_FIELDS
    ];
}