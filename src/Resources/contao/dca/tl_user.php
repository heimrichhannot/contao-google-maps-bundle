<?php

$dca = &$GLOBALS['TL_DCA']['tl_user'];

/**
 * Palettes
 */
$dca['palettes']['extend'] = str_replace('fop;', 'fop;{contao-google-maps-bundle_legend},contao_google_maps_bundles,contao_google_maps_bundlep;', $dca['palettes']['extend']);
$dca['palettes']['custom'] = str_replace('fop;', 'fop;{contao-google-maps-bundle_legend},contao_google_maps_bundles,contao_google_maps_bundlep;', $dca['palettes']['custom']);

/**
 * Fields
 */
$dca['fields']['contao_google_maps_bundles'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_user']['contao_google_maps_bundles'],
    'exclude'    => true,
    'inputType'  => 'checkbox',
    'foreignKey' => 'tl_google_map.title',
    'eval'       => ['multiple' => true],
    'sql'        => "blob NULL"
];

$dca['fields']['contao_google_maps_bundlep'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['contao_google_maps_bundlep'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'options'   => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval'      => ['multiple' => true],
    'sql'       => "blob NULL"
];
