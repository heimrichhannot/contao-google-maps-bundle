<?php

namespace HeimrichHannot\GoogleMapsBundle\Element;

use Contao\Config;
use Contao\ContentElement;
use Contao\ContentModel;
use Contao\System;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Ivory\GoogleMap\Helper\Builder\ApiHelperBuilder;
use Ivory\GoogleMap\Helper\Builder\MapHelperBuilder;
use Ivory\GoogleMap\Map;
use Patchwork\Utf8;

class ContentGoogleMap extends ContentElement
{
    /**
     * @var string
     */
    protected $strTemplate = 'ce_google_map';

    /**
     * @var \Twig_Environment
     */
    protected $twig;

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

        $this->twig      = System::getContainer()->get('twig');
        $this->arrayUtil = System::getContainer()->get('huh.utils.array');
        $this->modelUtil = System::getContainer()->get('huh.utils.model');
    }

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['CTE'][$this->type][0]) . ' ###';
            $objTemplate->title    = $this->headline;

            if (null !== ($map = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map', $this->googlemaps_map))) {
                $objTemplate->id   = $map->id;
                $objTemplate->link = $map->title;
                $objTemplate->href = 'contao/main.php?do=google_maps&amp;table=tl_google_map&amp;act=edit&amp;id=' . $map->id;
            }

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
        $templateData = $this->arrayUtil->removePrefix('googlemaps_', $this->arrData);

        $templateData = System::getContainer()->get('huh.google_maps.map_manager')->prepareMap($templateData);
        $this->Template->map = $templateData['mapObject'];

        $template = $templateData['mapConfig']['template'] ?: 'gmap_map_default';
        $template = System::getContainer()->get('huh.utils.template')->getTemplate($template);

        $this->Template->renderedMap = $this->twig->render($template, $templateData);
    }
}
