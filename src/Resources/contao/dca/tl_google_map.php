<?php

$GLOBALS['TL_DCA']['tl_google_map'] = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_google_map_overlay'],
        'switchToEdit'      => true,
        'enableVersioning'  => true,
        'onload_callback'   => [
            ['huh.google_maps.data_container.google_map', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['huh.utils.dca', 'setDateAddedOnCopy'],
        ],
        'sql'               => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list'        => [

        'label'             => [
            'fields' => ['title', 'type'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'         => 2,
            'fields'       => ['title', 'type'],
            'headerFields' => ['title'],
            'panelLayout'  => 'filter;sort,search,limit',
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
            'edit'       => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['edit'],
                'href'            => 'table=tl_google_map_overlay',
                'button_callback' => ['huh.google_maps.data_container.google_map', 'edit'],
                'icon'            => 'edit.svg'
            ],
            'editheader' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['editheader'],
                'href'            => 'act=edit',
                'icon'            => 'header.svg',
                'button_callback' => ['huh.google_maps.data_container.google_map', 'editHeader']
            ],
            'copy'       => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.svg',
                'button_callback' => ['huh.google_maps.data_container.google_map', 'copyArchive']
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['copy'],
                'href'            => 'act=delete',
                'icon'            => 'delete.svg',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                    . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => ['huh.google_maps.data_container.google_map', 'deleteArchive']
            ],
            'toggle'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map']['toggle'],
                'href'  => 'act=toggle',
                'icon'  => 'toggle.svg'
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.svg'
            ],
        ]
    ],
    'palettes'    => [
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
            'overrideLanguage'
        ],
        'default'      => '{general_legend},type,title,htmlId,overrideGooglemaps_apiKey;'
            . '{visualization_legend},mapType,sizeMode,addClusterer,styles;'
            . '{behavior_legend},disableDoubleClickZoom,draggable,scrollwheel,staticMapNoscript;'
            . '{positioning_legend},positioningMode;'
            . '{control_legend},mapTypesAvailable,addMapTypeControl,addZoomControl,addRotateControl,addFullscreenControl,addStreetViewControl,addScaleControl;'
            . '{language_legend},overrideLanguage;'
            . '{responsive_legend},responsive;'
            . '{template_legend},template;',
        'responsive'   => '{general_legend},type,title;'
            . '{visualization_legend},sizeMode;'
    ],
    'subpalettes' => [
        // visualization
        'sizeMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::SIZE_MODE_ASPECT_RATIO           => 'aspectRatioX,aspectRatioY',
        'sizeMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::SIZE_MODE_STATIC                 => 'width,height',
        'addClusterer'                                                                                           => 'clustererImg',
        // behavior
        'staticMapNoscript'                                                                                      => 'staticMapWidth,staticMapHeight',
        // positioning
        'positioningMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONING_MODE_STANDARD => 'centerMode,zoom',
        'positioningMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONING_MODE_BOUND    => 'boundMode',
        'boundMode_'
        . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::BOUND_MODE_COORDINATES                       => 'boundNorthEastLat,boundNorthEastLng,boundSouthWestLat,boundSouthWestLng',
        'boundMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::BOUND_MODE_AUTOMATIC            => '',
        'centerMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::CENTER_MODE_COORDINATE         => 'centerLat,centerLng',
        'centerMode_' . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::CENTER_MODE_STATIC_ADDRESS     => 'centerAddress',
        // controls
        'addMapTypeControl'                                                                                      => 'mapTypeControlPos,mapTypeControlStyle',
        'addZoomControl'                                                                                         => 'zoomControlPos',
        'addRotateControl'                                                                                       => 'rotateControlPos',
        'addFullscreenControl'                                                                                   => 'fullscreenControlPos',
        'addStreetViewControl'                                                                                   => 'streetViewControlPos',
        // language
        'overrideLanguage'                                                                                       => 'language'
    ],
    'fields'      => [
        'id'                     => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp'                 => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded'              => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'"
        ],
        'type'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['type'],
            'exclude'   => true,
            'search'    => true,
            'flag'      => 12,
            'sorting'   => true,
            'default'   => 'base',
            'inputType' => 'select',
            'options'   => [
                \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::MAP_TYPE_BASE,
                \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::MAP_TYPE_RESPONSIVE,
            ],
            'reference' => $GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['mandatory' => true, 'submitOnChange' => true],
            'sql'       => "varchar(12) NOT NULL default ''",
        ],
        'title'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''"
        ],
        'htmlId'                 => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['htmlId'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        // visualization
        'mapType'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['mapType'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true],
            'sql'       => "varchar(64) NOT NULL default '" . \Ivory\GoogleMap\MapTypeId::ROADMAP . "'"
        ],
        'sizeMode'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['sizeMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::SIZE_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default '" . \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::SIZE_MODE_ASPECT_RATIO . "'"
        ],
        'width'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['width'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['includeBlankOption' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'height'                 => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['height'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['includeBlankOption' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'aspectRatioX'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['aspectRatioX'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(5) unsigned NOT NULL default '0'"
        ],
        'aspectRatioY'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['aspectRatioY'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(5) unsigned NOT NULL default '0'"
        ],
        'addClusterer'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addClusterer'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'clustererImg'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['clustererImg'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'styles'                 => [
            'label'       => &$GLOBALS['TL_LANG']['tl_google_map']['styles'],
            'exclude'     => true,
            'search'      => true,
            'inputType'   => 'textarea',
            'eval'        => ['allowHtml' => true, 'tl_class' => 'clr', 'class' => 'monospace', 'rte' => 'ace|js', 'helpwizard' => true],
            'explanation' => 'insertTags',
            'sql'         => "text NULL"
        ],
        // behavior
        'disableDoubleClickZoom' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['disableDoubleClickZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'scrollwheel'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['scrollwheel'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'draggable'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['draggable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'staticMapNoscript'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapNoscript'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'staticMapWidth'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapWidth'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ],
        'staticMapHeight'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapHeight'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 10, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ],
        // positioning
        'positioningMode'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['positioningMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONING_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'boundMode'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::BOUND_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'boundNorthEastLat'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundNorthEastLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'boundNorthEastLng'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundNorthEastLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'boundSouthWestLat'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundSouthWestLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'boundSouthWestLng'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundSouthWestLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'centerMode'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::CENTER_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'centerLat'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'centerLng'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'centerAddress'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerAddress'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'zoom'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['zoom'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 2, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(2) unsigned NOT NULL default '15'"
        ],
        // controls
        'mapTypesAvailable'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['mapTypesAvailable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::TYPES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'w50 autoheight'],
            'sql'       => "varchar(255) NOT NULL default '" . serialize(\HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::TYPES) . "'",
        ],
        'addMapTypeControl'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addMapTypeControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'mapTypeControlStyle'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlStyle'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::MAP_CONTROL_STYLES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(16) NOT NULL default 'default'",
        ],
        'mapTypeControlPos'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql'       => "varchar(16) NOT NULL default 'top_right'",
        ],
        'addZoomControl'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addZoomControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'zoomControlPos'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql'       => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addRotateControl'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addRotateControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'rotateControlPos'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql'       => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addFullscreenControl'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addFullscreenControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'fullscreenControlPos'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql'       => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addStreetViewControl'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addStreetViewControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'streetViewControlPos'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::POSITIONS,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle w50 autoheight'],
            'sql'       => "varchar(16) NOT NULL default 'top_left'",
        ],
        'addScaleControl'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addScaleControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'responsive'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['responsive'],
            'inputType' => 'multiColumnEditor',
            'exclude'   => true,
            'eval'      => [
                'tl_class'          => 'clr',
                'multiColumnEditor' => [
                    'fields' => [
                        'breakpoint' => [
                            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['responsive_breakpoint'],
                            'exclude'   => true,
                            'inputType' => 'text',
                            'eval'      => [
                                'groupStyle' => 'width:100px',
                                'rgxp'       => 'digit',
                            ],
                        ],
                        'map'        => [
                            'label'            => &$GLOBALS['TL_LANG']['tl_google_map']['responsive_map'],
                            'inputType'        => 'select',
                            'options_callback' => ['huh.google_maps.data_container.google_map', 'getResponsiveMaps'],
                            'eval'             => [
                                'groupStyle'         => 'width:300px',
                                'includeBlankOption' => true,
                            ],
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL",
        ],
        // template
        'template'               => [
            'label'            => &$GLOBALS['TL_LANG']['tl_google_map']['template'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => function () {
                return System::getContainer()->get('huh.utils.choice.twig_template')->getCachedChoices(['gmap_map_']);
            },
            'eval'             => ['tl_class' => 'w50', 'includeBlankOption' => true],
            'sql'              => "varchar(64) NOT NULL default ''"
        ],
        'overrideLanguage'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['overrideLanguage'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'language'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['language'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'rgxp' => 'language', 'maxlength' => 5, 'nospace' => true, 'doNotCopy' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(5) NOT NULL default ''"
        ],
    ]
];

\Contao\Controller::loadDataContainer('tl_settings');
System::getContainer()->get('huh.utils.dca')->addOverridableFields(['googlemaps_apiKey'], 'tl_settings', 'tl_google_map');
$GLOBALS['TL_DCA']['tl_google_map']['fields']['googlemaps_apiKey']['sql'] = "varchar(255) NOT NULL default ''";
