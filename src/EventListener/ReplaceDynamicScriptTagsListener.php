<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

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

    public function __invoke($buffer)
    {
        if ($mapApi = $this->mapManager->renderApi()) {
            $GLOBALS['TL_BODY']['huhGoogleMaps'] = $mapApi;
        }

        return $buffer;
    }
}
