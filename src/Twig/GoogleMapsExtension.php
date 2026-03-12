<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Twig;

use HeimrichHannot\GoogleMapsBundle\MapBuilder\MapBuilder;
use Twig\DeprecatedCallableInfo;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Runtime\EscaperRuntime;
use Twig\TwigFunction;

class GoogleMapsExtension extends AbstractExtension
{
    public function __construct(
        private readonly Environment $environment,
    ) {
        $escaperRuntime = $this->environment->getRuntime(EscaperRuntime::class);
        $escaperRuntime->addSafeClass(MapBuilder::class, ['html', 'contao_html']);
    }

    public function getFunctions(): array
    {
        $functions = [
            new TwigFunction(
                'google_map',
                [GoogleMapsRuntime::class, 'create'],
                [
                    'is_safe' => ['html'],
                ]
            ),
        ];

        foreach ($this->getMapping() as $name => $method) {
            $functions[] = new TwigFunction(
                $name,
                [GoogleMapsRuntime::class, $method],
                [
                    'is_safe' => ['html'],
                    'deprecation_info' => new DeprecatedCallableInfo(
                        'heimrichhannot/contao-google-maps-bundle',
                        '3.0.0',
                        'google_map'
                    ),
                ]
            );
        }

        return $functions;
    }

    private function getMapping(): array
    {
        return [
            'google_map_html' => 'renderHtml',
            'google_map_css' => 'renderCss',
            'google_map_js' => 'renderJs',
        ];
    }
}
