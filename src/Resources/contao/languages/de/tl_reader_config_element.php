<?php

$lang = &$GLOBALS['TL_LANG']['tl_reader_config_element'];

/**
 * Reference
 */
if (\Contao\System::getContainer()->get('huh.utils.container')->isBundleActive('HeimrichHannot\ReaderBundle\HeimrichHannotContaoReaderBundle')) {
    $lang['reference'] += [
        \HeimrichHannot\GoogleMapBundle\ConfigElementType\GoogleMapReaderConfigElementType::TYPE => 'Google Map',
    ];
}