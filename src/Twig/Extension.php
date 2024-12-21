<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Twig;

use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class Extension extends AbstractExtension
{
    /**
     * @var MapManager
     */
    private $mapManager;

    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    public function getFunctions()
    {
        $functions = [];

        foreach ($this->getMapping() as $name => $method) {
            $functions[] = new TwigFunction($name, [$this, $method], ['is_safe' => ['html']]);
        }

        return $functions;
    }

    /**
     * @return string
     */
    public function render(int $mapId)
    {
        return $this->mapManager->render($mapId);
    }

    /**
     * @return string
     */
    public function renderHtml(int $mapId)
    {
        return $this->mapManager->renderHtml($mapId);
    }

    /**
     * @return string
     */
    public function renderCss(int $mapId)
    {
        return $this->mapManager->renderCss($mapId);
    }

    /**
     * @return string
     */
    public function renderJs(int $mapId)
    {
        return $this->mapManager->renderJs($mapId);
    }

    public function getName()
    {
        return 'huh_google_maps';
    }

    /**
     * @return array<string>
     */
    private function getMapping()
    {
        return [
            'google_map' => 'render',
            'google_map_html' => 'renderHtml',
            'google_map_css' => 'renderCss',
            'google_map_js' => 'renderJs',
        ];
    }
}
