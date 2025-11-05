<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\System;

$dca = &$GLOBALS['TL_DCA']['tl_page'];

/*
 * Palettes
 */
PaletteManipulator::create()
    ->addLegend('huh_google_maps_legend', 'global_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('overrideGooglemaps_apiKey', 'huh_google_maps_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page')
;

/*
 * Fields
 */
Controller::loadDataContainer('tl_settings');
System::getContainer()->get('huh.google_maps.utils.dca')->addOverridableFields(['googlemaps_apiKey'], 'tl_settings', 'tl_page');
$dca['fields']['googlemaps_apiKey']['sql'] = "varchar(255) NOT NULL default ''";
