<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Model;

use Contao\Model;

/**
 * @property string $positioningMode
 * @property string $boundMode
 * @property string $centerMode
 */
class GoogleMapModel extends Model
{
    protected static $strTable = 'tl_google_map';
}
