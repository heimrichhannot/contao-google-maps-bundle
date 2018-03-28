<?php

namespace HeimrichHannot\GoogleMapsBundle\Element;

use Contao\ContentElement;
use Contao\ContentModel;
use Contao\System;
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

    public function __construct(ContentModel $objElement, $strColumn = 'main') {

        parent::__construct($objElement, $strColumn);

        $this->twig = System::getContainer()->get('twig');
    }

    public function generate()
    {
        if(TL_MODE == 'BE')
        {
            $objTemplate = new \BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### ' . Utf8::strtoupper($GLOBALS['TL_LANG']['CTE'][$this->type][0]) . ' ###';
            $objTemplate->title = $this->headline;

            // TODO add map here
//            $objTemplate->id = $this->id;
//            $objTemplate->link = $this->name;
//            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        return parent::generate();
    }

    protected function compile()
    {
        $map = new Map();

        $this->Template->map = $map;
        $this->Template->renderedMap   = $this->twig->render('@HeimrichHannotContaoGoogleMaps/map.html.twig', [
            'map' => $map
        ]);
    }
}
