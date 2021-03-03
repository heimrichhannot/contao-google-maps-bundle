<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

class ReplaceInsertTagsListener
{
    /**
     * @var MapManager
     */
    private $mapManager;

    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    /**
     * @Hook("replaceInsertTags")
     */
    public function __invoke(
        string $insertTag,
        bool $useCache,
        string $cachedValue,
        array $flags,
        array $tags,
        array $cache,
        int $_rit,
        int $_cnt
    ) {
        $arrTag = explode('::', $insertTag);

        switch ($arrTag[0]) {
            case 'google_map':
                return $this->mapManager->render($arrTag[1]);

            case 'google_map_html':
                return $this->mapManager->renderHtml($arrTag[1]);

            case 'google_map_css':
                return $this->mapManager->renderCss($arrTag[1]);

            case 'google_map_js':
                return $this->mapManager->renderJs($arrTag[1]);
        }

        return false;
    }
}
