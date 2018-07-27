<?php

$dca = &$GLOBALS['TL_DCA']['tl_reader_config_element'];

/**
 * Fields
 */
if (\Contao\System::getContainer()->get('huh.utils.container')->isBundleActive('HeimrichHannot\ReaderBundle\HeimrichHannotContaoReaderBundle')) {
    /**
     * Palettes
     */
    $dca['palettes'][\HeimrichHannot\GoogleMapBundle\ConfigElementType\GoogleMapReaderConfigElementType::TYPE] =
        '{title_type_legend},title,type;{config_legend},name,googlemaps_map,googlemaps_centerMode,googlemaps_skipHtml,googlemaps_skipCss,googlemaps_skipJs;';

    /**
     * Fields
     */
    \Contao\System::loadLanguageFile('tl_content');

    $fields = [
        'googlemaps_map'                    => [
            'label'            => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_map'],
            'exclude'          => true,
            'filter'           => true,
            'inputType'        => 'select',
            'options_callback' => ['\HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap', 'getMapChoices'],
            'eval'             => ['tl_class' => 'w50', 'mandatory' => true, 'includeBlankOption' => true, 'chosen' => true],
            'sql'              => "int(10) unsigned NOT NULL default '0'"
        ],
        'googlemaps_skipHtml'               => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipHtml'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50 clr'],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'googlemaps_skipCss'                => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipCss'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''"
        ],
        'googlemaps_skipJs'                 => [
            'label'     => &$GLOBALS['TL_LANG']['tl_content']['googlemaps_skipJs'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => "char(1) NOT NULL default ''"
        ],
    ];

    $dca['fields'] += $fields;
}