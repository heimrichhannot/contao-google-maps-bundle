<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\LayoutModel;
use Contao\PageModel;
use Contao\PageRegular;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

class GeneratePageListener
{
    /**
     * @var MapManager
     */
    protected $mapManager;

    /**
     * GeneratePageListener constructor.
     */
    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    public function __invoke(PageModel $pageModel, LayoutModel $layout, PageRegular $pageRegular): void
    {
        if ($mapApi = $this->mapManager->renderApi()) {
            if ($scripts = trim($layout->script)) {
                $mapApi .= $scripts."\n".$mapApi;
            }
            $layout->script = $mapApi;
        }
    }
}
