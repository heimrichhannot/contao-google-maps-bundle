<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['google_maps'] = [
    'tables' => ['tl_google_map', 'tl_google_map_overlay'],
    'stylesheet' => 'bundles/heimrichhannotgooglemaps/css/backend.google-maps-bundle.css',
];

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['maps'] = [
    'google_map' => 'HeimrichHannot\GoogleMapsBundle\Element\ContentGoogleMap',
];

/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['maps'] = [
    'google_map' => 'HeimrichHannot\GoogleMapsBundle\Module\ModuleGoogleMap',
];

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['replaceInsertTags']['googleMapsBundle'] = ['huh.google_maps.listener.hooks', 'addInsertTags'];
$GLOBALS['TL_HOOKS']['generatePage']['huh_googlemaps'] = [\HeimrichHannot\GoogleMapsBundle\EventListener\GeneratePageListener::class, '__invoke'];

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_google_map']         = 'HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel';
$GLOBALS['TL_MODELS']['tl_google_map_overlay'] = 'HeimrichHannot\GoogleMapsBundle\Model\OverlayModel';

/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundles';
$GLOBALS['TL_PERMISSIONS'][] = 'contao_google_maps_bundlep';