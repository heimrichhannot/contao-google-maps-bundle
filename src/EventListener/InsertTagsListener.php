<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\OutputType;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

#[AsInsertTag('google_map')]
#[AsInsertTag('google_map_html')]
#[AsInsertTag('google_map_css')]
#[AsInsertTag('google_map_js')]
class InsertTagsListener implements InsertTagResolverNestedResolvedInterface
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly MapManager $mapManager,
    ) {
    }

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $this->framework->initialize();

        $mapId = (int) $insertTag->getParameters()->get(0);

        return match ($insertTag->getName()) {
            'google_map' => new InsertTagResult(
                $this->mapManager->render($mapId),
                OutputType::html,
            ),
            'google_map_html' => new InsertTagResult(
                $this->mapManager->renderHtml($mapId),
                OutputType::html,
            ),
            'google_map_css' => new InsertTagResult(
                $this->mapManager->renderCss($mapId),
                OutputType::html,
            ),
            'google_map_js' => new InsertTagResult(
                $this->mapManager->renderJs($mapId),
                OutputType::html,
            ),
            default => new InsertTagResult(''),
        };
    }
}
