<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;

/*
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['google_maps'] = [
    'tables' => ['tl_google_map', 'tl_google_map_overlay'],
    'stylesheet' => 'bundles/heimrichhannotgooglemaps/css/backend.google-maps-bundle.css',
];

/*
 * Models
 */
$GLOBALS['TL_MODELS']['tl_google_map'] = GoogleMapModel::class;
$GLOBALS['TL_MODELS']['tl_google_map_overlay'] = OverlayModel::class;

/*
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundles';
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundlep';
