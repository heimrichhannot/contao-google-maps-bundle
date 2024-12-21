<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$dca = &$GLOBALS['TL_DCA']['tl_user_group'];

/*
 * Palettes
 */
PaletteManipulator::create()
    ->addLegend('contao-google-maps-bundle_legend', 'forms_legend')
    ->addField('contao_google_maps_bundles', 'contao-google-maps-bundle_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('contao_google_maps_bundlep', 'contao-google-maps-bundle_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group')
;

/*
 * Fields
 */
$dca['fields']['contao_google_maps_bundles'] = [
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_google_map.title',
    'eval' => ['multiple' => true],
    'sql' => 'blob NULL',
];

$dca['fields']['contao_google_maps_bundlep'] = [
    'inputType' => 'checkbox',
    'options' => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => ['multiple' => true],
    'sql' => 'blob NULL',
];
