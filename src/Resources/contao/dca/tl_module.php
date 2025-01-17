<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\ModuleListener;

$dca = &$GLOBALS['TL_DCA']['tl_module'];

/*
 * Palettes
 */
$dca['palettes'][ModuleListener::MODULE_GOOGLE_MAP] =
    '{type_legend},name,headline,type;{config_legend},googlemaps_map,googlemaps_skipHtml,googlemaps_skipCss,googlemaps_skipJs;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;';

/**
 * Fields.
 */
$fields = [
    'googlemaps_map' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
        'filter' => true,
        'inputType' => 'select',
        'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
        'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
        'sql' => "int(10) unsigned NOT NULL default '0'",
        'wizard' => [['huh.google_maps.data_container.map_wizard', 'addWizard']],
    ],
    'googlemaps_skipHtml' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipHtml'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50 clr'],
        'sql' => "char(1) NOT NULL default ''",
    ],
    'googlemaps_skipCss' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipCss'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50'],
        'sql' => "char(1) NOT NULL default ''",
    ],
    'googlemaps_skipJs' => [
        'label' => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipJs'],
        'inputType' => 'checkbox',
        'eval' => ['tl_class' => 'w50'],
        'sql' => "char(1) NOT NULL default ''",
    ],
];

$dca['fields'] = array_merge($dca['fields'], $fields);
