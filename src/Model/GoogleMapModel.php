<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Model;

use Contao\Model;

/**
 * @property string $positioningMode
 * @property string $boundMode
 * @property string $centerMode
 *
 * @method static GoogleMapModel|null findById($id, array $opt=array())
 * @method static GoogleMapModel|null findByPk($id, array $opt=array())
 */
class GoogleMapModel extends Model
{
    protected static $strTable = 'tl_google_map';
}
