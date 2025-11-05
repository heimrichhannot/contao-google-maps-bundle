<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\ModuleListener;

/*
 * Backend modules
 */
$GLOBALS['TL_LANG']['MOD']['google_maps'] = ['Google Maps', ''];

/*
 * Frontend modules
 */
$GLOBALS['TL_LANG']['FMD']['maps'] = ['Karten', ''];
$GLOBALS['TL_LANG']['FMD'][ModuleListener::MODULE_GOOGLE_MAP] = ['Google Map', ''];
