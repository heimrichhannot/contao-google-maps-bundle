<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Element;

use Contao\BackendTemplate;
use Contao\ContentElement;
use Contao\ContentModel;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;

class ContentGoogleMap extends ContentElement
{
    const TYPE = 'google_map';

    /**
     * @var string
     */
    protected $strTemplate = 'ce_google_map';

    /**
     * @var MapManager
     */
    protected $mapManager;

    /**
     * @var ArrayUtil
     */
    protected $arrayUtil;

    /**
     * @var ModelUtil
     */
    protected $modelUtil;

    public function __construct(ContentModel $objElement, $strColumn = 'main')
    {
        parent::__construct($objElement, $strColumn);
        $this->mapManager = System::getContainer()->get('huh.google_maps.map_manager');
        $this->arrayUtil = System::getContainer()->get('huh.utils.array');
        $this->modelUtil = System::getContainer()->get('huh.utils.model');
    }

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### '.mb_strtoupper($GLOBALS['TL_LANG']['CTE'][$this->type][0]).' ###';
            $objTemplate->title = $this->headline;

            if (null !== ($map = $this->modelUtil->findModelInstanceByPk('tl_google_map', $this->googlemaps_map))) {
                $objTemplate->id = $map->id;
                $objTemplate->link = $map->title;
                $objTemplate->href = 'contao?do=google_maps&amp;table=tl_google_map&amp;act=edit&amp;id='.$map->id;
            }

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
        $elementData = $this->arrayUtil->removePrefix('googlemaps_', $this->arrData);

        $this->Template->renderedMap = $this->mapManager->render($elementData['map'], $elementData);
    }
}
