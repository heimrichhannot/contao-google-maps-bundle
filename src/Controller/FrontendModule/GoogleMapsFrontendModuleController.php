<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Controller\FrontendModule;

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\ModuleModel;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Util\ArrayUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(self::TYPE, category: 'maps', template: 'mod_google_map')]
class GoogleMapsFrontendModuleController extends AbstractFrontendModuleController
{
    public const TYPE = 'google_map';

    protected MapManager $mapManager;

    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    /**
     * @throws \Exception
     */
    protected function getResponse(FragmentTemplate $template, ModuleModel $model, Request $request): Response
    {
        $this->initializeContaoFramework();

        $elementData = ArrayUtil::removePrefix('googlemaps_', $model->row());
        $template->renderedMap = $this->mapManager->render($elementData['map'], $elementData);

        return $template->getResponse();
    }
}
