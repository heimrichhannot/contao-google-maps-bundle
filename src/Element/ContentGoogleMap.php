<?php

namespace HeimrichHannot\GoogleMapsBundle\Element;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\System;
use HeimrichHannot\UtilsBundle\Arrays\ArrayUtil;
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

    public function __construct(ContentModel $objElement, $strColumn = 'main')
    {

        parent::__construct($objElement, $strColumn);

        $this->twig = System::getContainer()->get('twig');
        $this->arrayUtil = System::getContainer()->get('huh.utils.array');
    }

    public function generate()
    {
        if (TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['CTE'][$this->type][0]) . ' ###';
            $objTemplate->title    = $this->headline;

            if (null !== ($map = System::getContainer()->get('huh.utils.model')->findModelInstanceByPk('tl_google_map', $this->googlemaps_map)))
            {
                $objTemplate->id = $map->id;
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

        $templateData['map'] = System::getContainer()->get('huh.google_maps.manager')->prepareMap($this->arrData);
        $this->Template->map = $templateData['map'];

        $this->Template->renderedMap = $this->twig->render('@HeimrichHannotContaoGoogleMaps/google_map.html.twig', $templateData);
    }
}
