<?php

namespace HeimrichHannot\GoogleMapsBundle\Twig;

use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

class Extension extends \Twig_Extension
{
    /**
     * @var MapManager
     */
    private $mapManager;

    /**
     * @param MapManager $mapManager
     */
    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $functions = [];

        foreach ($this->getMapping() as $name => $method) {
            $functions[] = new \Twig_SimpleFunction($name, [$this, $method], ['is_safe' => ['html']]);
        }

        return $functions;
    }

    /**
     * @param int $mapId
     *
     * @return string
     */
    public function render(int $mapId)
    {
        return $this->mapManager->render($mapId);
    }

    /**
     * @param int $mapId
     *
     * @return string
     */
    public function renderHtml(int $mapId)
    {
        return $this->mapManager->renderHtml($mapId);
    }

    /**
     * @param int $mapId
     *
     * @return string
     */
    public function renderCss(int $mapId)
    {
        return $this->mapManager->renderCss($mapId);
    }

    /**
     * @param int $mapId
     *
     * @return string
     */
    public function renderJs(int $mapId)
    {
        return $this->mapManager->renderJs($mapId);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'huh_google_maps';
    }

    /**
     * @return string[]
     */
    private function getMapping()
    {
        return [
            'google_map'      => 'render',
            'google_map_html' => 'renderHtml',
            'google_map_css'  => 'renderCss',
            'google_map_js'   => 'renderJs',
        ];
    }
}
