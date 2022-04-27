<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use HeimrichHannot\PrivacyCenter\Manager\PrivacyCenterManager;
use HeimrichHannot\PrivacyCenter\Model\TrackingObjectModel;

class PrivacyCenterListener
{
    /**
     * Adjust the generated map api. Priority -1 ensures it's called after the ReplaceDynamicScriptTagsListener
     * listener.
     *
     * @Hook("replaceDynamicScriptTags", priority=-1)
     */
    public function onReplaceDynamicScriptTags(string $buffer): string
    {
        if (!isset($GLOBALS['TL_BODY']['huhGoogleMaps'])) {
            return $buffer;
        }

        if (TrackingObjectModel::findBy(['localStorageAttribute=?'], ['google_maps'])) {
            // protect the code
            $GLOBALS['TL_BODY']['huhGoogleMaps'] = PrivacyCenterManager::getInstance()->addProtectedCode(
                $GLOBALS['TL_BODY']['huhGoogleMaps'],
                ['google_maps'],
                [
                    'addPoster' => false,
                ]
            );
        }

        return $buffer;
    }
}
