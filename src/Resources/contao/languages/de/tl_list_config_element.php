<?php

$lang = &$GLOBALS['TL_LANG']['tl_list_config_element'];

/**
 * Reference
 */
if (\Contao\System::getContainer()->get('huh.utils.container')->isBundleActive('HeimrichHannot\ListBundle\HeimrichHannotContaoListBundle')) {
    $lang['reference'] += [
        \HeimrichHannot\GoogleMapBundle\ConfigElementType\GoogleMapListConfigElementType::TYPE => 'Google Map',
    ];
}
