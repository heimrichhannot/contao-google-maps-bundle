<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\PrivacyCenter\Manager\PrivacyCenterManager;

/**
 * @Hook("replaceDynamicScriptTags")
 */
class ReplaceDynamicScriptTagsListener
{
    /**
     * @var MapManager
     */
    protected $mapManager;

    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    public function __invoke($buffer): string
    {
        $mapApi = $this->mapManager->renderApi();
        if (empty($mapApi)) {
            return $buffer;
        }

        // fix the code for the case more than 1 map is on the page and not the first one is clicked
        $mapApi = preg_replace(
            '@(ivory_google_map_init_requirement\()(ivory_google_map_map_[^,]+)@i',
            'typeof $2 !== \'undefined\' && $1$2', $mapApi);

        if (class_exists('HeimrichHannot\PrivacyCenterBundle\HeimrichHannotPrivacyCenterBundle')) {
            // protect the code
            $GLOBALS['TL_BODY']['huhGoogleMaps'] = PrivacyCenterManager::getInstance()->addProtectedCode(
                $mapApi,
                ['google_maps'],
                [
                    'addPoster' => false,
                ]
            );
        } else {
            $GLOBALS['TL_BODY']['huhGoogleMaps'] = $mapApi;
        }

        return $buffer;
    }
}
