<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Controller;
use Contao\System;
$dca = &$GLOBALS['TL_DCA']['tl_page'];

/**
 * Palettes
 */
$dca['palettes']['root'] = str_replace('{global_legend', '{huh_google_maps_legend},overrideGooglemaps_apiKey;{global_legend', $dca['palettes']['root']);

/**
 * Fields
 */
Controller::loadDataContainer('tl_settings');
System::getContainer()->get('huh.utils.dca')->addOverridableFields(['googlemaps_apiKey'], 'tl_settings', 'tl_page');
$GLOBALS['TL_DCA']['tl_page']['fields']['googlemaps_apiKey']['sql'] = "varchar(255) NOT NULL default ''";
