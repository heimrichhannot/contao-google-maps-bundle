<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\GoogleMapsBundle\EventListener;


use Contao\System;

class LoadDataContainerListener
{
    /**
     * @Hook("loadDataContainer")
     */
    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_list_config_element':
            case 'tl_reader_config_element':
                $this->addElementFields($table);
                break;
        }
    }

    /**
     * Add fields to list and reader bundle if tables are loaded
     *
     * @param string $table
     */
    protected function addElementFields(string $table)
    {
        $dca = &$GLOBALS['TL_DCA'][$table];

        /**
         * Fields
         */
        System::loadLanguageFile('tl_content');

        $fields = [
            'googlemaps_map'      => [
                'label'            => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
                'exclude'          => true,
                'filter'           => true,
                'inputType'        => 'select',
                'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
                'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
                'sql'              => "int(10) unsigned NOT NULL default '0'"
            ],
            'googlemaps_skipHtml' => [
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipHtml'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['tl_class' => 'w50 clr'],
                'sql'       => "char(1) NOT NULL default ''"
            ],
            'googlemaps_skipCss'  => [
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipCss'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['tl_class' => 'w50'],
                'sql'       => "char(1) NOT NULL default ''"
            ],
            'googlemaps_skipJs'   => [
                'label'     => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipJs'],
                'exclude'   => true,
                'inputType' => 'checkbox',
                'eval'      => ['tl_class' => 'w50'],
                'sql'       => "char(1) NOT NULL default ''"
            ],
        ];

        $dca['fields'] = array_merge($dca['fields'], $fields);
    }
}