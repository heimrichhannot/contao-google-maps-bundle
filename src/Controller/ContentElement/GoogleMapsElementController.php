<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Twig\FragmentTemplate;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Util\ArrayUtil;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement(self::TYPE, category: 'maps', template: 'ce_google_map')]
class GoogleMapsElementController extends AbstractContentElementController
{
    public const TYPE = 'google_map';

    protected MapManager $mapManager;

    private ScopeMatcher $scopeMatcher;

    private Utils $utils;

    public function __construct(ScopeMatcher $scopeMatcher, Utils $utils, MapManager $mapManager)
    {
        $this->scopeMatcher = $scopeMatcher;
        $this->utils = $utils;
        $this->mapManager = $mapManager;
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $this->initializeContaoFramework();

        if ($this->scopeMatcher->isBackendRequest($request)) {
            return $this->getBackendWildcard($template, $model);
        }

        $elementData = ArrayUtil::removePrefix('googlemaps_', $model->row());
        $template->renderedMap = $this->mapManager->render($elementData['map'], $elementData);

        return $template->getResponse();
    }

    protected function getBackendWildcard(FragmentTemplate $template, ContentModel $model): Response
    {
        $wilcardTemplate = new BackendTemplate('be_wildcard');
        $wilcardTemplate->wildcard = '### '.mb_strtoupper($GLOBALS['TL_LANG']['CTE'][$model->type][0]).' ###';
        $wilcardTemplate->title = $template->headline;

        if (null !== ($map = $this->utils->model()->findModelInstanceByPk('tl_google_map', $model->googlemaps_map))) {
            $wilcardTemplate->id = $map->id;
            $wilcardTemplate->link = $map->title;
            $wilcardTemplate->href = 'contao?do=google_maps&amp;table=tl_google_map&amp;act=edit&amp;id='.$map->id;
        }

        return $wilcardTemplate->getResponse();
    }
}
