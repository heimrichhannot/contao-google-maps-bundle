<?php

namespace HeimrichHannot\GoogleMapsBundle\Module;

use Contao\BackendTemplate;
use Contao\Module;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Patchwork\Utf8;

class ModuleGoogleMap extends Module
{
    /**
     * @var string
     */
    protected $strTemplate = 'mod_google_map';

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

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            /** @var BackendTemplate|object $objTemplate */
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0]) . ' ###';
            $objTemplate->title = $this->headline;

            if (null !== ($map = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map', $this->googlemaps_map))) {
                $objTemplate->id   = $map->id;
                $objTemplate->link = $map->title;
                $objTemplate->href = 'contao/main.php?do=google_maps&amp;table=tl_google_map&amp;act=edit&amp;id=' . $map->id;
            }

            return $objTemplate->parse();
        }

        $this->mapManager = System::getContainer()->get('huh.google_maps.map_manager');
        $this->arrayUtil = System::getContainer()->get('huh.utils.array');
        $this->modelUtil = System::getContainer()->get('huh.utils.model');
        $this->twig      = System::getContainer()->get('twig');

        return parent::generate();
    }

    protected function compile()
    {
        $elementData = $this->arrayUtil->removePrefix('googlemaps_', $this->arrData);

        $this->Template->renderedMap = $this->mapManager->render($elementData['map'], $elementData);
    }
}
