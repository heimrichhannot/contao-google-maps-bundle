<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['google_maps'] = [
    'tables' => ['tl_google_map', 'tl_google_map_overlay']
];

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['maps'] = [
    'google_map' => 'HeimrichHannot\GoogleMapsBundle\Element\ContentGoogleMap',
];

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundles';
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundlep';
