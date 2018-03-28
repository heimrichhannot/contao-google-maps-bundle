<?php

$GLOBALS['TL_DCA']['tl_google_map'] = [
    'config' => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_google_map_overlay'],
        'switchToEdit'                => true,
        'enableVersioning'  => true,
        'onload_callback' => [
            ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['huh.utils.dca', 'setDateAddedOnCopy'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary'
            ]
        ]
    ],
    'list' => [
        'label' => [
            'fields' => ['title'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'                  => 1,
            'fields'                => ['title'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;search,limit'
        ],
        'global_operations' => [
            'all'    => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"'
            ],
        ],
        'operations' => [
            'edit' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_google_map']['edit'],
                'href'                => 'table=tl_google_map_overlay',
                'icon'                => 'edit.gif'
            ],
            'editheader' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_google_map']['editheader'],
                'href'                => 'act=edit',
                'icon'                => 'header.gif',
                'button_callback'     => ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'editHeader']
            ],
            'copy' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_google_map']['copy'],
                'href'                => 'act=copy',
                'icon'                => 'copy.gif',
                'button_callback'     => ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'copyArchive']
            ],
            'delete' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_google_map']['copy'],
                'href'                => 'act=delete',
                'icon'                => 'delete.gif',
                'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
                'button_callback'     => ['HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'deleteArchive']
            ],
            'toggle' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_google_map']['toggle'],
                'href'                => 'act=toggle',
                'icon'                => 'toggle.gif'
            ],
            'show' => [
                'label'               => &$GLOBALS['TL_LANG']['tl_google_map']['show'],
                'href'                => 'act=show',
                'icon'                => 'show.gif'
            ],
        ]
    ],
    'palettes' => [
        '__selector__' => ['published'],
        'default' => '{general_legend},title;{publish_legend},published;'
    ],
    'subpalettes' => [
        'published'    => 'start,stop'
    ],
    'fields'   => [
        'id' => [
            'sql'                     => "int(10) unsigned NOT NULL auto_increment"
        ],
        'tstamp' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map']['tstamp'],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'dateAdded' => [
            'label'                   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting'                 => true,
            'flag'                    => 6,
            'eval'                    => ['rgxp'=>'datim', 'doNotCopy' => true],
            'sql'                     => "int(10) unsigned NOT NULL default '0'"
        ],
        'title' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map']['title'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'published' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map']['published'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['doNotCopy'=>true, 'submitOnChange' => true],
            'sql'                     => "char(1) NOT NULL default ''"
        ],
        'start' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map']['start'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ],
        'stop' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map']['stop'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ]
    ]
];