<?php

$GLOBALS['TL_DCA']['tl_google_map'] = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_google_map_overlay'],
        'switchToEdit'      => true,
        'enableVersioning'  => true,
        'onload_callback'   => [
            ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'checkPermission'],
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
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'         => 1,
            'fields'       => ['title'],
            'headerFields' => ['title'],
            'panelLayout'  => 'filter;search,limit'
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
                'label' => &$GLOBALS['TL_LANG']['tl_google_map']['edit'],
                'href'  => 'table=tl_google_map_overlay',
                'icon'  => 'edit.gif'
            ],
            'editheader' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['editheader'],
                'href'            => 'act=edit',
                'icon'            => 'header.gif',
                'button_callback' => ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'editHeader']
            ],
            'copy'       => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.gif',
                'button_callback' => ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'copyArchive']
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map']['copy'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                     . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'deleteArchive']
            ],
            'toggle'     => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map']['toggle'],
                'href'  => 'act=toggle',
                'icon'  => 'toggle.gif'
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            ],
        ]
    ],
    'palettes'    => [
        '__selector__' => [
            // visualization
            'sizeMode',
            'addClusterer',
            // positioning
            'positioningMode',
            'boundMode',
            'centerMode',
            // controls
            'addMapTypeControl',
            'addZoomControl',
            'addPanControl',
            'addRotateControl',
            'addScaleControl',
            'addStreetViewControl',
            'addOverviewMapControl',
            // published
            'published'
        ],
        'default'      => '{general_legend},title;'
                          . '{visualization_legend},mapType,sizeMode,addClusterer,styles;{behavior_legend},disableDoubleClickZoom,draggable,scrollwheel,staticMapNoscript;{positioning_legend},positioningMode;'
                          . '{control_legend},mapTypesAvailable,addMapTypeControl,addZoomControl,addRotateControl,addPanControl,addScaleControl,addStreetViewControl,addOverviewMapControl;'
                          . '{publish_legend},published;'
    ],
    'subpalettes' => [
        // visualization
        'sizeMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_ASPECT_RATIO           => 'aspectRatioX,aspectRatioY',
        'sizeMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_STATIC                 => 'width,height',
        'addClusterer'                                                                                     => 'clustererImg',
        // positioning
        'positioningMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::POSITIONING_MODE_STANDARD => 'centerMode,zoom',
        'positioningMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::POSITIONING_MODE_BOUND    => 'boundMode',
        'boundMode_'
        . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::BOUND_MODE_COORDINATES                       => 'boundNorthEastLat,boundNorthEastLng,boundSouthWestLat,boundSouthWestLng',
        'boundMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::BOUND_MODE_AUTOMATIC            => '',
        'centerMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::CENTER_MODE_COORDINATE         => 'centerLat,centerLng',
        'centerMode_' . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::CENTER_MODE_STATIC_ADDRESS     => 'centerAddress',
        // controls
        'addMapTypeControl'                                                                                => 'mapTypeControlStyle,mapTypeControlPos',
        'addZoomControl'                                                                                   => 'zoomControlStyle,zoomControlPos',
        'addRotateControl'                                                                                 => 'rotateControlStyle,rotateControlPos',
        'addPanControl'                                                                                    => 'panControlStyle,panControlPos',
        'addScaleControl'                                                                                  => 'scaleControlPos',
        'addStreetViewControl'                                                                             => 'streetViewControlPos',
        'addOverviewMapControl'                                                                            => 'overviewMapControlOpened',
        // published
        'published'                                                                                        => 'start,stop'
    ],
    'fields'      => [
        'id'                       => [
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp'                   => [
            'label' => &$GLOBALS['TL_LANG']['tl_google_map']['tstamp'],
            'sql'   => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded'                => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'"
        ],
        'title'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        // visualization
        'mapType'                  => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['mapType'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => [
                \Ivory\GoogleMap\MapTypeId::ROADMAP,
                \Ivory\GoogleMap\MapTypeId::SATELLITE,
                \Ivory\GoogleMap\MapTypeId::TERRAIN,
                \Ivory\GoogleMap\MapTypeId::HYBRID
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true],
            'sql'       => "varchar(64) NOT NULL default '" . \Ivory\GoogleMap\MapTypeId::ROADMAP . "'"
        ],
        'sizeMode'                 => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['sizeMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default '" . \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_ASPECT_RATIO . "'"
        ],
        'width'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['width'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['includeBlankOption' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'height'                   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['height'],
            'inputType' => 'inputUnit',
            'options'   => $GLOBALS['TL_CSS_UNITS'],
            'eval'      => ['includeBlankOption' => true, 'rgxp' => 'digit_auto_inherit', 'maxlength' => 20, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'aspectRatioX'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['aspectRatioX'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(5) unsigned NOT NULL default '0'"
        ],
        'aspectRatioY'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['aspectRatioY'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 5, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(5) unsigned NOT NULL default '0'"
        ],
        'addClusterer'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addClusterer'],
            'exclude'   => true,
            'filter'    => true,
            'default'   => false,
            'inputType' => 'checkbox',
            'eval'      => ['submitOnChange' => true, 'tl_class' => 'clr m12'],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'clustererImg'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['clustererImg'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['mandatory' => false, 'maxlength' => 255],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'styles'                   => [
            'label'       => &$GLOBALS['TL_LANG']['tl_google_map']['styles'],
            'exclude'     => true,
            'search'      => true,
            'inputType'   => 'textarea',
            'eval'        => ['allowHtml' => true, 'tl_class' => 'clr', 'class' => 'monospace', 'rte' => 'ace|js', 'helpwizard' => true],
            'explanation' => 'insertTags',
            'sql'         => "text NULL"
        ],
        // behavior
        'disableDoubleClickZoom'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['disableDoubleClickZoom'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'scrollwheel'              => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['scrollwheel'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'draggable'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['draggable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'staticMapNoscript'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['staticMapNoscript'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        // positioning
        'positioningMode'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['positioningMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::POSITIONING_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'boundMode'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::BOUND_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'boundNorthEastLat'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundNorthEastLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'boundNorthEastLng'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundNorthEastLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'boundSouthWestLat'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundSouthWestLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'boundSouthWestLng'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['boundSouthWestLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'centerMode'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerMode'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'options'   => \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::CENTER_MODES,
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['reference'],
            'eval'      => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'submitOnChange' => true],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'centerLat'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerLat'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'centerLng'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerLng'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 16, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "float(10,6) unsigned NOT NULL default '0.0'"
        ],
        'centerAddress'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['centerAddress'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "varchar(255) NOT NULL default ''"
        ],
        'zoom'                     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['zoom'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'digit', 'maxlength' => 2, 'tl_class' => 'w50', 'mandatory' => true],
            'sql'       => "int(2) unsigned NOT NULL default '15'"
        ],
        // controls
        'mapTypesAvailable'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['mapTypesAvailable'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'options'   => ['HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN'],
            'default'   => serialize(['HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN']),
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['mandatory' => true, 'multiple' => true],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'addMapTypeControl'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addMapTypeControls'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'mapTypeControlStyle'      => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['mapTypeControlStyle'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['DEFAULT', 'DROPDOWN_MENU', 'HORIZONTAL_BAR'],
            'default'   => 'DEFAULT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['mandatory' => true],
            'sql'       => "varchar(16) NOT NULL default 'DEFAULT'",
        ],
        'mapTypeControlPos'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'default'   => 'TOP_RIGHT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle'],
            'sql'       => "varchar(16) NOT NULL default 'TOP_RIGHT'",
        ],
        'addZoomControl'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addZoomControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'zoomControlStyle'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['zoomControlStyle'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['ANDROID', 'DEFAULT', 'SMALL', 'ZOOM_PAN'],
            'default'   => 'DEFAULT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['mandatory' => true],
            'sql'       => "varchar(16) NOT NULL default 'DEFAULT'",
        ],
        'zoomControlPos'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'default'   => 'TOP_LEFT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle'],
            'sql'       => "varchar(16) NOT NULL default 'TOP_LEFT'",
        ],
        'addRotateControl'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addRotateControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'rotateControlPos'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'default'   => 'TOP_LEFT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle'],
            'sql'       => "varchar(16) NOT NULL default 'TOP_LEFT'",
        ],
        'addPanControl'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addPanControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'panControlPos'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'default'   => 'TOP_LEFT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle'],
            'sql'       => "varchar(16) NOT NULL default 'TOP_LEFT'",
        ],
        'addStreetViewControl'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addStreetViewControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'streetViewControlPos'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'default'   => 'TOP_LEFT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle'],
            'sql'       => "varchar(16) NOT NULL default 'TOP_LEFT'",
        ],
        'addOverviewMapControl'    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addOverviewMapControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'overviewMapControlOpened' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['overviewMapControlOpened'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'addScaleControl'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['addScaleControl'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'default'   => '1',
            'eval'      => ['submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default '1'",
        ],
        'scaleControlPos'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['controlPos'],
            'exclude'   => true,
            'inputType' => 'radioTable',
            'options'   => [
                'TOP_LEFT',
                'TOP_CENTER',
                'TOP_RIGHT',
                'LEFT_TOP',
                'C1',
                'RIGHT_TOP',
                'LEFT_CENTER',
                'C2',
                'RIGHT_CENTER',
                'LEFT_BOTTOM',
                'C3',
                'RIGHT_BOTTOM',
                'BOTTOM_LEFT',
                'BOTTOM_CENTER',
                'BOTTOM_RIGHT',
            ],
            'default'   => 'BOTTOM_LEFT',
            'reference' => &$GLOBALS['TL_LANG']['tl_google_map']['references'],
            'eval'      => ['cols' => 3, 'tl_class' => 'google-maps-bundle'],
            'sql'       => "varchar(16) NOT NULL default 'BOTTOM_LEFT'",
        ],
        // published
        'published'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['published'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['doNotCopy' => true, 'submitOnChange' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'start'                    => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['start'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ],
        'stop'                     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_google_map']['stop'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql'       => "varchar(10) NOT NULL default ''"
        ]
    ]
];