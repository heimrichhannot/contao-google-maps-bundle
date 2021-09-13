<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay;
use Contao\System;
$GLOBALS['TL_DCA']['tl_google_map_overlay'] = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_google_map',
        'enableVersioning'  => true,
        'onload_callback'   => [
            ['huh.google_maps.data_container.google_map_overlay', 'modifyDca'],
            ['huh.google_maps.data_container.google_map_overlay', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['huh.utils.dca', 'setDateAddedOnCopy'],
        ],
        'sql'               => [
            'keys' => [
                'id'                       => 'primary',
                'pid,start,stop,published' => 'index'
            ]
        ]
    ],
    'list'        => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => ['type', 'title'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;sort,search,limit',
            'child_record_callback' => ['huh.google_maps.data_container.google_map_overlay', 'listChildren']
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
        ],
        'operations'        => [
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.svg'
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.svg'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['toggle'],
                'icon'            => 'visible.svg',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['huh.google_maps.data_container.google_map_overlay', 'toggleIcon']
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.svg'
            ],
        ]
    ],
    'palettes'    => [
        '__selector__'                                                => [
            'titleMode',
            'positioningMode',
            'markerType',
            'clickEvent',
            'addRouting',
            // CAUTION: type must be at this position, else a Contao palette error takes places!
            'type',
            'published'
        ],
        'default'                                                     => '{general_legend},title,type;{publish_legend},published;',
        Overlay::TYPE_MARKER =>
            '{general_legend},title,type;{config_legend},titleMode,positioningMode,animation,markerType,clickEvent,zIndex;{publish_legend},published;',
        Overlay::TYPE_INFO_WINDOW =>
            '{general_legend},title,type;{config_legend},positioningMode,infoWindowWidth,infoWindowHeight,infoWindowText,addRouting,zIndex;{publish_legend},published;',
        Overlay::TYPE_KML_LAYER =>
            '{general_legend},title,type;{config_legend},kmlUrl,kmlClickable,kmlPreserveViewport,kmlScreenOverlays,kmlSuppressInfowindows,zIndex;{publish_legend},published;',

    ],
    'subpalettes' => [
        'titleMode_' . Overlay::TITLE_MODE_CUSTOM_TEXT                => 'titleText',
        'positioningMode_' . Overlay::POSITIONING_MODE_COORDINATE     => 'positioningLat,positioningLng',
        'positioningMode_' . Overlay::POSITIONING_MODE_STATIC_ADDRESS => 'positioningAddress',
        'markerType_' . Overlay::MARKER_TYPE_ICON                     => 'iconSrc,iconWidth,iconHeight,iconAnchorX,iconAnchorY',
        'clickEvent_' . Overlay::CLICK_EVENT_LINK                     => 'url,target',
        'clickEvent_' . Overlay::CLICK_EVENT_INFO_WINDOW              => 'infoWindowWidth,infoWindowHeight,infoWindowAnchorX,infoWindowAnchorY,infoWindowText,addRouting',
        'addRouting'                                                                                           => 'routingAddress,routingTemplate',
        'published'                                                                                            => 'start,stop'
    ],
    'fields'      => [
        'id'                 => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'pid'                => [
            'foreignKey' => 'tl_google_map.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'belongsTo', 'load' => 'eager']
        ],
        'tstamp'             => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded'          => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'"
        ],
        // general
        'title'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'type'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['type'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => Overlay::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        // config
        'titleMode'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['titleMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => Overlay::TITLE_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval'      => ['tl_class' => 'w50', 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'titleText'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['titleText'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'positioningMode'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => Overlay::POSITIONING_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval'      => ['tl_class' => 'w50 clr', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'positioningLat'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'positioningLng'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'positioningAddress' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['positioningAddress'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'animation'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['animation'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => Overlay::ANIMATIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval'      => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'markerType'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['markerType'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => Overlay::MARKER_TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval'      => ['tl_class' => 'w50 clr', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'fillColor'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['fillColor'],
            'inputType' => 'text',
            'eval'      => ['maxlength' => 6, 'isHexColor' => true, 'colorpicker' => true, 'decodeEntities' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(6) NOT NULL default ''",
        ],
        'iconSrc'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconSrc'],
            'exclude'   => true,
            'inputType' => 'fileTree',
            'eval'      => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => 'gif,jpg,jpeg,png', 'mandatory' => true, 'tl_class' => 'clr'],
            'sql'       => "binary(16) NULL",
        ],
        'iconWidth'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconWidth'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'iconHeight'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconHeight'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'iconAnchorX'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconAnchorX'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(5) unsigned NOT NULL default '0'"
        ],
        'iconAnchorY'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['iconAnchorY'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(5) unsigned NOT NULL default '0'"
        ],
        'clickEvent'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['clickEvent'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => Overlay::CLICK_EVENTS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['reference'],
            'eval'      => ['includeBlankOption' => true, 'submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'infoWindowText'         => [
            'label'       => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowText'],
            'exclude'     => true,
            'search'      => true,
            'inputType'   => 'textarea',
            'eval'        => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'long clr'],
            'explanation' => 'insertTags',
            'sql'         => "text NULL",
        ],
        'addRouting'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['addRouting'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'routingAddress'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['routingAddress'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'long clr'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'routingTemplate' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['routingTemplate'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'select',
            'options_callback' => function() {
                return System::getContainer()->get('huh.utils.choice.twig_template')->getCachedChoices(['gmap_routing_']);
            },
            'eval'                    => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql'                     => "varchar(64) NOT NULL default ''"
        ],
        'infoWindowWidth'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowWidth'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'infoWindowHeight'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowHeight'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'infoWindowAnchorX'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowAnchorX'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'],
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ],
        'infoWindowAnchorY'  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['infoWindowAnchorY'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50'],
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ],
        'url'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['url'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50 wizard', 'mandatory' => true, 'dcaPicker'=>true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'target'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['target'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'zIndex'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['zIndex'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ],
        'kmlUrl'                 => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlUrl'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'kmlClickable'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlClickable'],
            'exclude'   => true,
            'filter'    => true,
            'default'   => true,
            'eval'      => ['tl_class' => 'clr m12'],
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'kmlPreserveViewport'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlPreserveViewport'],
            'exclude'   => true,
            'filter'    => true,
            'default'   => false,
            'eval'      => ['tl_class' => 'm12'],
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'kmlScreenOverlays'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlScreenOverlays'],
            'exclude'   => true,
            'filter'    => true,
            'default'   => true,
            'eval'      => ['tl_class' => 'm12'],
            'inputType' => 'checkbox',
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'kmlSuppressInfowindows' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['kmlSuppressInfowindows'],
            'exclude'   => true,
            'filter'    => true,
            'default'   => false,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'm12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        // publish
        'published'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['published'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'start'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['start'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ],
        'stop'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['stop'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ]
    ]
];
