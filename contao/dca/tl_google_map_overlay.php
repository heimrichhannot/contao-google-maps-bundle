<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use Contao\Controller;
use Contao\DataContainer;
use Contao\DC_Table;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\OverlayListener;
use HeimrichHannot\UtilsBundle\Dca\DateAddedField;

DateAddedField::register('tl_google_map_overlay')
    ->setFlag(DataContainer::SORT_DAY_DESC);

$GLOBALS['TL_DCA']['tl_google_map_overlay'] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ptable' => 'tl_google_map',
        'enableVersioning' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,start,stop,published' => 'index',
            ],
        ],
    ],
    'list' => [
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'sorting' => [
            'mode' => 4,
            'fields' => ['type', 'title'],
            'headerFields' => ['title'],
            'panelLayout' => 'filter;sort,search,limit',
        ],
        'operations' => [
            'edit',
            'copy',
            'delete',
            'toggle' => [
                'href' => 'act=toggle&amp;field=published',
                'icon' => 'visible.svg',
                'showInHeader' => true,
            ],
            'show',
        ],
    ],
    'palettes' => [
        '__selector__' => [
            'titleMode',
            'positioningMode',
            'markerType',
            'clickEvent',
            'addRouting',
            // CAUTION: type must be at this position, else a Contao palette error takes places!
            'type',
            'published',
        ],
        'default' => '{general_legend},title,type;{publish_legend},published;',
        OverlayListener::TYPE_MARKER => '{general_legend},title,type;{config_legend},titleMode,positioningMode,animation,markerType,clickEvent,zIndex;{publish_legend},published;',
        OverlayListener::TYPE_INFO_WINDOW => '{general_legend},title,type;{config_legend},positioningMode,infoWindowWidth,infoWindowHeight,infoWindowText,addRouting,zIndex;{publish_legend},published;',
        OverlayListener::TYPE_KML_LAYER => '{general_legend},title,type;{config_legend},kmlUrl,kmlClickable,kmlPreserveViewport,kmlScreenOverlays,kmlSuppressInfowindows,zIndex;{publish_legend},published;',
        OverlayListener::TYPE_POLYGON => '{general_legend},title,type;{config_legend},pathCoordinates,strokeColor,strokeOpacity,fillColor,fillOpacity,strokeWeight,zIndex;{publish_legend},published;',
    ],
    'subpalettes' => [
        'titleMode_'.OverlayListener::TITLE_MODE_CUSTOM_TEXT => 'titleText',
        'positioningMode_'.OverlayListener::POSITIONING_MODE_COORDINATE => 'positioningLat,positioningLng',
        'positioningMode_'.OverlayListener::POSITIONING_MODE_STATIC_ADDRESS => 'positioningAddress',
        'markerType_'.OverlayListener::MARKER_TYPE_ICON => 'iconSrc,iconWidth,iconHeight,iconAnchorX,iconAnchorY',
        'clickEvent_'.OverlayListener::CLICK_EVENT_LINK => 'url,target',
        'clickEvent_'.OverlayListener::CLICK_EVENT_INFO_WINDOW => 'infoWindowWidth,infoWindowHeight,infoWindowAnchorX,infoWindowAnchorY,infoWindowText,addRouting',
        'addRouting' => 'routingAddress,routingTemplate',
        'published' => 'start,stop',
    ],
    'fields' => [
        'id' => [
            'sql' => 'int(10) unsigned NOT NULL auto_increment',
        ],
        'pid' => [
            'foreignKey' => 'tl_google_map.title',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager'],
        ],
        'tstamp' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['tstamp'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        // general
        'title' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['title'],
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'type' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['type'],
            'filter' => true,
            'inputType' => 'select',
            'options' => OverlayListener::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        // config
        'titleMode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['titleMode'],
            'filter' => true,
            'inputType' => 'select',
            'options' => OverlayListener::TITLE_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'titleText' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['titleText'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'positioningMode' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningMode'],
            'filter' => true,
            'inputType' => 'select',
            'options' => OverlayListener::POSITIONING_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'positioningLat' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningLat'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(8,6) NOT NULL default '0.000000'",
        ],
        'positioningLng' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningLng'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "decimal(9,6) NOT NULL default '0.000000'",
        ],
        'positioningAddress' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningAddress'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'animation' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['animation'],
            'filter' => true,
            'inputType' => 'select',
            'options' => OverlayListener::ANIMATIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'markerType' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['markerType'],
            'filter' => true,
            'inputType' => 'select',
            'options' => OverlayListener::MARKER_TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval' => ['tl_class' => 'w50 clr', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'fillColor' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['fillColor'],
            'inputType' => 'text',
            'eval' => ['maxlength' => 6, 'isHexColor' => true, 'colorpicker' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(6) NOT NULL default ''",
        ],
        'iconSrc' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconSrc'],
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => 'gif,jpg,jpeg,png', 'mandatory' => true, 'tl_class' => 'clr'],
            'sql' => 'binary(16) NULL',
        ],
        'iconWidth' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconWidth'],
            'inputType' => 'inputUnit',
            'options' => ['px', '%', 'em', 'rem'],
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'iconHeight' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconHeight'],
            'inputType' => 'inputUnit',
            'options' => ['px', '%', 'em', 'rem'],
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'iconAnchorX' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconAnchorX'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(5) unsigned NOT NULL default '0'",
        ],
        'iconAnchorY' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconAnchorY'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(5) unsigned NOT NULL default '0'",
        ],
        'clickEvent' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['clickEvent'],
            'filter' => true,
            'inputType' => 'select',
            'options' => OverlayListener::CLICK_EVENTS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval' => ['includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'infoWindowText' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowText'],
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'long clr'],
            'explanation' => 'insertTags',
            'sql' => 'text NULL',
        ],
        'addRouting' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['addRouting'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'routingAddress' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['routingAddress'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'long clr'],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'routingTemplate' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['routingTemplate'],
            'filter' => true,
            'inputType' => 'select',
            'options_callback' => static fn () => Controller::getTemplateGroup('gmap_routing_'),
            'eval' => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'infoWindowWidth' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowWidth'],
            'inputType' => 'inputUnit',
            'options' => ['px', '%', 'em', 'rem'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'infoWindowHeight' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowHeight'],
            'inputType' => 'inputUnit',
            'options' => ['px', '%', 'em', 'rem'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'infoWindowAnchorX' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowAnchorX'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'infoWindowAnchorY' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowAnchorY'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'url' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['url'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50 wizard', 'mandatory' => true, 'dcaPicker' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'target' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['target'],
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'zIndex' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['zIndex'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'kmlUrl' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlUrl'],
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql' => "varchar(255) NOT NULL default ''",
        ],
        'kmlClickable' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlClickable'],
            'filter' => true,
            'default' => true,
            'eval' => ['tl_class' => 'clr m12'],
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'kmlPreserveViewport' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlPreserveViewport'],
            'filter' => true,
            'default' => false,
            'eval' => ['tl_class' => 'm12'],
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default ''",
        ],
        'kmlScreenOverlays' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlScreenOverlays'],
            'filter' => true,
            'default' => true,
            'eval' => ['tl_class' => 'm12'],
            'inputType' => 'checkbox',
            'sql' => "char(1) NOT NULL default '1'",
        ],
        'kmlSuppressInfowindows' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlSuppressInfowindows'],
            'filter' => true,
            'default' => false,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'm12'],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'pathCoordinates' => [
            'inputType' => 'group',
            'palette' => ['positioningLat', 'positioningLng'],
            'fields' => [
                '&positioningLat' => [
                    'eval' => ['mandatory' => true],
                ],
                '&positioningLng' => [
                    'eval' => ['mandatory' => true],
                ],
            ],
            'min' => 3,
            'sql' => [
                'type' => 'blob',
                'length' => MySqlPlatform::LENGTH_LIMIT_BLOB,
                'notnull' => false,
            ],
        ],
        'strokeWeight' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 3, 'tl_class' => 'w50'],
            'sql' => "varchar(3) NOT NULL default '2'",
        ],
        'strokeColor' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 6, 'isHexColor' => true, 'colorpicker' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(6) NOT NULL default ''",
        ],
        'strokeOpacity' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 3, 'tl_class' => 'w50'],
            'sql' => "varchar(3) NOT NULL DEFAULT '1.0'",
        ],
        'fillOpacity' => [
            'inputType' => 'text',
            'eval' => ['maxlength' => 3, 'tl_class' => 'w50'],
            'sql' => "varchar(3) NOT NULL DEFAULT '0.6'",
        ],
        // publish
        'published' => [
            'toggle' => true,
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['published'],
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'start' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['start'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'stop' => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['stop'],
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
    ],
];
