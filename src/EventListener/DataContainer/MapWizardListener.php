<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer;

use Contao\DataContainer;
use Contao\Image;
use Contao\StringUtil;
use Contao\System;

class MapWizardListener
{
    public static function addWizard(DataContainer $dc)
    {
        if ($dc->value < 1) {
            return '';
        }

        $label = System::getContainer()->get('translator')->trans('tl_google_map.edit.1', [], 'contao_tl_google_map');
        $title = \sprintf($label, $dc->value);
        $href = System::getContainer()->get('router')->generate('contao_backend', ['do' => 'google_maps', 'table' => 'tl_google_map', 'act' => 'edit', 'id' => $dc->value, 'popup' => '1', 'nb' => '1']);

        return ' <a href="'.StringUtil::specialcharsUrl($href).'" title="'.StringUtil::specialchars($title).'" onclick="Backend.openModalIframe({\'title\':\''.StringUtil::specialchars(str_replace("'", "\\'", $title)).'\',\'url\':this.href});return false">'.Image::getHtml('edit.svg', $title).'</a>';
    }
}
