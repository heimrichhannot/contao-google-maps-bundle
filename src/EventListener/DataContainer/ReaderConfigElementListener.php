<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer;

class ReaderConfigElementListener
{
    const CENTER_MODE_ADDRESS_FIELDS = 'address_fields';

    const CENTER_MODE_COORDINATE_FIELDS = 'coordinate_fields';

    const CENTER_MODES = [
        self::CENTER_MODE_ADDRESS_FIELDS,
        self::CENTER_MODE_COORDINATE_FIELDS,
    ];
}
