<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Ivory\GoogleMap\Map;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeRenderMapEvent extends Event
{
    /** @deprecated Use class FQN instead */
    const NAME = 'huh.maps.before_render_map';

    public function __construct(
        public string $template,
        public array $templateData,
        public Map $map
    )
    {
    }

    /**
     * @deprecated Use properties instead
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @deprecated Use properties instead
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @deprecated Use properties instead
     */
    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    /**
     * @deprecated Use properties instead
     */
    public function setTemplateData(array $templateData): void
    {
        $this->templateData = $templateData;
    }

    /**
     * @deprecated Use properties instead
     */
    public function getMap(): Map
    {
        return $this->map;
    }

    /**
     * @deprecated Use properties instead
     */
    public function setMap(Map $map): void
    {
        $this->map = $map;
    }
}
