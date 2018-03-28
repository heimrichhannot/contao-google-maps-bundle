<?php

$GLOBALS['TL_DCA']['tl_google_map_overlay'] = [
    'config'   => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_google_map',
        'enableVersioning'  => true,
        'onload_callback' => [
            ['HeimrichHannot\GoogleMapsBundle\Backend\Overlay', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['huh.utils.dca', 'setDateAdded'],
        ],
        'oncopy_callback'   => [
            ['huh.utils.dca', 'setDateAddedOnCopy'],
        ],
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'pid,start,stop,published' => 'index'            ]
        ]
    ],
    'list'     => [
        'label' => [
            'fields' => ['id'],
            'format' => '%s'
        ],
        'sorting'           => [
            'mode'                  => 1,
            'fields'                => ['title'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;sort,search,limit',
            'child_record_callback' => ['HeimrichHannot\GoogleMapsBundle\Backend\Overlay', 'listChildren']
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
            'edit'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ],
            'copy'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif'
            ],
            'delete' => [
                'label'      => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ],
            'toggle' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['HeimrichHannot\GoogleMapsBundle\Backend\Overlay', 'toggleIcon']
            ],
            'show'   => [
                'label' => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
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
        'pid' => [
            'foreignKey'              => 'tl_google_map.title',
            'sql'                     => "int(10) unsigned NOT NULL default '0'",
            'relation'                => ['type'=>'belongsTo', 'load'=>'eager']
        ],
        'tstamp' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['tstamp'],
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
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['title'],
            'exclude'                 => true,
            'search'                  => true,
            'sorting'                 => true,
            'flag'                    => 1,
            'inputType'               => 'text',
            'eval'                    => ['mandatory' => true, 'tl_class'=>'w50'],
            'sql'                     => "varchar(255) NOT NULL default ''"
        ],
        'published' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['published'],
            'exclude'                 => true,
            'filter'                  => true,
            'inputType'               => 'checkbox',
            'eval'                    => ['doNotCopy'=>true, 'submitOnChange' => true],
            'sql'                     => "char(1) NOT NULL default ''"
        ],
        'start' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['start'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ],
        'stop' => [
            'label'                   => &$GLOBALS['TL_LANG']['tl_google_map_overlay']['stop'],
            'exclude'                 => true,
            'inputType'               => 'text',
            'eval'                    => ['rgxp'=>'datim', 'datepicker'=>true, 'tl_class'=>'w50 wizard'],
            'sql'                     => "varchar(10) NOT NULL default ''"
        ]
    ]
];