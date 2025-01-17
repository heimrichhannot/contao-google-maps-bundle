<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

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

        // fix the code for the case more than 1 map is on the page and not the first one
        // is clicked and add to body variable
        $GLOBALS['TL_BODY']['huhGoogleMaps'] = preg_replace(
            '@(ivory_google_map_init_requirement\()(ivory_google_map_map_[^,]+)@i',
            'typeof $2 !== \'undefined\' && $1$2', $mapApi);

        return $buffer;
    }
}
