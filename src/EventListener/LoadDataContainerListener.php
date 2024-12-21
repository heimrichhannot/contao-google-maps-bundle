<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\System;

/**
 * @Hook("loadDataContainer")
 */
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_list_config_element':
            case 'tl_reader_config_element':
                $this->addElementFields($table);
                break;
            case 'tl_list_config':
                $this->addListConfigFields($table);
        }
    }

    public function addListConfigFields(string $table): void
    {
        Controller::loadLanguageFile('tl_content');

        $dca = &$GLOBALS['TL_DCA'][$table];

        PaletteManipulator::create()
            ->addLegend('google_maps_legend', 'sorting_legend', PaletteManipulator::POSITION_AFTER)
            ->addField('renderItemsAsMap', 'google_maps_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', $table)
        ;

        $dca['palettes']['__selector__'][] = 'renderItemsAsMap';
        $dca['subpalettes']['renderItemsAsMap'] = 'itemMap,addMapControlList';

        $fields = [
            'renderItemsAsMap' => [
                'label' => &$GLOBALS['TL_LANG']['tl_list_config']['renderItemsAsMap'],
                'inputType' => 'checkbox',
                'eval' => ['tl_class' => 'w50', 'submitOnChange' => true],
                'sql' => "char(1) NOT NULL default ''",
            ],
            'itemMap' => [
                'label' => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
                'filter' => true,
                'inputType' => 'select',
                'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
                'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
                'sql' => "int(10) unsigned NOT NULL default '0'",
            ],
            'addMapControlList' => [
                'label' => &$GLOBALS['TL_LANG']['tl_list_config']['addMapControlList'],
                'inputType' => 'checkbox',
                'eval' => ['tl_class' => 'w50'],
                'sql' => "char(1) NOT NULL default ''",
            ],
        ];

        $dca['fields'] = array_merge($dca['fields'], $fields);
    }

    /**
     * Add fields to list and reader bundle if tables are loaded.
     */
    protected function addElementFields(string $table): void
    {
        $dca = &$GLOBALS['TL_DCA'][$table];

        /*
         * Fields
         */
        System::loadLanguageFile('tl_content');

        $fields = [
            'googlemaps_map' => [
                'label' => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
                'filter' => true,
                'inputType' => 'select',
                'options_callback' => ['huh.google_maps.data_container.google_map', 'getMapChoices'],
                'eval' => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
                'sql' => "int(10) unsigned NOT NULL default '0'",
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
    }
}
