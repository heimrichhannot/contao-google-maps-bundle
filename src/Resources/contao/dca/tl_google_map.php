<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Controller;
use Contao\DC_Table;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\GoogleMapListener;
use Ivory\GoogleMap\MapTypeId;

$GLOBALS['TL_DCA']['tl_google_map'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_google_map_overlay'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'onload_callback' => [
            // ['huh.google_maps.data_container.google_map', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['huh.google_maps.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback' => [
            ['huh.google_maps.utils.dca', 'setDateAddedOnCopy'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list' => [
        'label' => [
            'fields' => ['title', 'type'],
            'format' => '%s',
        ],
        'sorting' => [
            'mode' => 2,
            'fields' => ['title', 'type'],
            'headerFields' => ['title'],
            'panelLayout' => 'filter;sort,search,limit',
        ],
    ],
    'palettes' => [
        '__selector__' => [
            'type',
            // visualization
            'sizeMode',
            'addClusterer',
            // behavior
            'staticMapNoscript',
            // positioning
            'positioningMode',
            'boundMode',
            'centerMode',
            // controls
            'addMapTypeControl',
            'addZoomControl',
            'addRotateControl',
            'addFullscreenControl',
            'addStreetViewControl',
            // language
            'overrideLanguage',
        ],
        'default' => '{general_legend},type,title,htmlId,overrideGooglemaps_apiKey;'
            .'{visualization_legend},mapType,sizeMode,addClusterer,styles;'
            .'{behavior_legend},disableDoubleClickZoom,draggable,scrollwheel,staticMapNoscript;'
            .'{positioning_legend},positioningMode;'
            .'{control_legend},mapTypesAvailable,addMapTypeControl,addZoomControl,addRotateControl,addFullscreenControl,addStreetViewControl,addScaleControl;'
            .'{language_legend},overrideLanguage;'
            .'{responsive_legend},responsive;'
            .'{template_legend},template;',
        'responsive' => '{general_legend},type,title;'
            .'{visualization_legend},sizeMode;',
    ],
    'subpalettes' => [
        // visualization
        'sizeMode_'.GoogleMapListener::SIZE_MODE_ASPECT_RATIO => 'aspectRatioX,aspectRatioY',
        'sizeMode_'.GoogleMapListener::SIZE_MODE_STATIC => 'width,height',
        'addClusterer' => 'clustererImg',
        // behavior
        'staticMapNoscript' => 'staticMapWidth,staticMapHeight',
        // positioning
        'positioningMode_'.GoogleMapListener::POSITIONING_MODE_STANDARD => 'centerMode,zoom',
        'positioningMode_'.GoogleMapListener::POSITIONING_MODE_BOUND => 'boundMode',
        'boundMode_'
        .GoogleMapListener::BOUND_MODE_COORDINATES => 'boundNorthEastLat,boundNorthEastLng,boundSouthWestLat,boundSouthWestLng',
        'boundMode_'.GoogleMapListener::BOUND_MODE_AUTOMATIC => '',
        'centerMode_'.GoogleMapListener::CENTER_MODE_COORDINATE => 'centerLat,centerLng',
        'centerMode_'.GoogleMapListener::CENTER_MODE_STATIC_ADDRESS => 'centerAddress',
        // controls
        'addMapTypeControl' => 'mapTypeControlPos,mapTypeControlStyle',
        'addZoomControl' => 'zoomControlPos',
        'addRotateControl' => 'rotateControlPos',
        'addFullscreenControl' => 'fullscreenControlPos',
        'addStreetViewControl' => 'streetViewControlPos',
        // language
        'overrideLanguage' => 'language',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'tstamp' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['tstamp'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag' => 6,
            'eval' => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'type' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['type'],
            'search' => true,
            'flag' => 12,
            'sorting' => true,
            'default' => 'base',
            'inputType' => 'select',
            'options' => [
                GoogleMapListener::MAP_TYPE_BASE,
                GoogleMapListener::MAP_TYPE_RESPONSIVE,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['mandatory' => true, 'submitOnChange' => true],
            'sql' => "varchar(12) NOT NULL default ''",
        ],
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['title'],
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'htmlId' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['htmlId'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        // visualization
        'mapType' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['mapType'],
            'filter' => true,
            'inputType' => 'select',
            'options' => GoogleMapListener::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true],
            'sql' => "varchar(64) NOT NULL default '".MapTypeId::ROADMAP."'",
        ],
        'sizeMode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['sizeMode'],
            'filter' => true,
            'inputType' => 'select',
            'options' => GoogleMapListener::SIZE_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default '".GoogleMapListener::SIZE_MODE_ASPECT_RATIO."'",
        ],
        'width' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['width'],
            'inputType' => 'inputUnit',
            'options' => ['px', '%', 'em', 'rem'],
            'eval' => ['includeBlankOption' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'height' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['height'],
            'inputType' => 'inputUnit',
            'options' => ['px', '%', 'em', 'rem'],
            'eval' => ['includeBlankOption' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'aspectRatioX' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['aspectRatioX'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(5) unsigned NOT NULL default '16'",
        ],
        'aspectRatioY' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['aspectRatioY'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(5) unsigned NOT NULL default '9'",
        ],
        'addClusterer' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addClusterer'],
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'clustererImg' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['clustererImg'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => false, 'maxlength' => 255],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'styles' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['styles'],
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['allowHtml' => true, 'tl_class' => 'clr', 'class' => 'monospace', 'rte' => 'ace|js', 'helpwizard' => true],
            'explanation' => 'insertTags',
            'sql' => 'text NULL',
        ],
        // behavior
        'disableDoubleClickZoom' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['disableDoubleClickZoom'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'scrollwheel' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['scrollwheel'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'draggable' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['draggable'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'staticMapNoscript' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapNoscript'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'staticMapWidth' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapWidth'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'staticMapHeight' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapHeight'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        // positioning
        'positioningMode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['positioningMode'],
            'filter' => true,
            'inputType' => 'select',
            'options' => GoogleMapListener::POSITIONING_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'boundMode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['boundMode'],
            'filter' => true,
            'inputType' => 'select',
            'options' => GoogleMapListener::BOUND_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'boundNorthEastLat' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['boundNorthEastLat'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(8,6) NOT NULL default '0.000000'",
        ],
        'boundNorthEastLng' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['boundNorthEastLng'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(9,6) NOT NULL default '0.000000'",
        ],
        'boundSouthWestLat' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['boundSouthWestLat'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(8,6) NOT NULL default '0.000000'",
        ],
        'boundSouthWestLng' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['boundSouthWestLng'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(9,6) NOT NULL default '0.000000'",
        ],
        'centerMode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['centerMode'],
            'filter' => true,
            'inputType' => 'select',
            'options' => GoogleMapListener::CENTER_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'centerLat' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['centerLat'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(8,6) NOT NULL default '0.000000'",
        ],
        'centerLng' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['centerLng'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => [
                'type' => 'decimal',
                'precision' => 9,
                'scale' => 6,
                'default' => '0.000000',
                'notnull' => true,
            ],
        ],
        'centerAddress' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['centerAddress'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'zoom' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['zoom'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 2, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(2) unsigned NOT NULL default '15'",
        ],
        // controls
        'mapTypesAvailable' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['mapTypesAvailable'],
            'inputType' => 'checkbox',
            'options' => GoogleMapListener::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'w50 autoheight'],
            'sql' => "varchar(255) NOT NULL default '".serialize(GoogleMapListener::TYPES)."'",
        ],
        'addMapTypeControl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addMapTypeControl'],
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'mapTypeControlStyle' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['controlStyle'],
            'inputType' => 'select',
            'options' => GoogleMapListener::MAP_CONTROL_STYLES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(16) NOT NULL default 'default'",
        ],
        'mapTypeControlPos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'inputType' => 'radioTable',
            'options' => GoogleMapListener::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql' => "varchar(16) NOT NULL default 'top_right'",
        ],
        'addZoomControl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addZoomControl'],
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'zoomControlPos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'inputType' => 'radioTable',
            'options' => GoogleMapListener::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql' => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addRotateControl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addRotateControl'],
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'rotateControlPos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'inputType' => 'radioTable',
            'options' => GoogleMapListener::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql' => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addFullscreenControl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addFullscreenControl'],
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'fullscreenControlPos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'inputType' => 'radioTable',
            'options' => GoogleMapListener::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql' => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addStreetViewControl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addStreetViewControl'],
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'streetViewControlPos' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'inputType' => 'radioTable',
            'options' => GoogleMapListener::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval' => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql' => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addScaleControl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['addScaleControl'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 clr'],
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'responsive' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['responsive'],
            'inputType' => 'multiColumnEditor',
            'eval' => [
                'tl_class' => 'clr',
                'multiColumnEditor' => [
                    'fields' => [
                        'breakpoint' => [
                            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['responsive_breakpoint'],
                            'inputType' => 'text',
                            'eval' => [
                                'groupStyle' => 'width:100px',
                                'rgxp' => 'digit',
                            ],
                        ],
                        'map' => [
                            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['responsive_map'],
                            'inputType' => 'select',
                            'options_callback' => ['huh.google_maps.data_container.google_map', 'getResponsiveMaps'],
                            'eval' => [
                                'groupStyle' => 'width:300px',
                                'includeBlankOption' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'sql' => 'blob NULL',
        ],
        // template
        'template' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['template'],
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => static fn () => Controller::getTemplateGroup('gmap_map_'),
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'overrideLanguage' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['overrideLanguage'],
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'language' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['language'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'rgxp' => 'language', 'maxlength' => 5, 'nospace' => true, 'doNotCopy' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(5) NOT NULL default ''",
        ],
    ],
];

Controller::loadDataContainer('tl_settings');
System::getContainer()->get('huh.google_maps.utils.dca')->addOverridableFields(['googlemaps_apiKey'], 'tl_settings', 'tl_google_map');
$GLOBALS['TL_DCA']['tl_google_map']['fields']['googlemaps_apiKey']['sql'] = "varchar(255) NOT NULL default ''";
