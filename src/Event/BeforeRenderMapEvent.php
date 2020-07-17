<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\Event;

class BeforeRenderMapEvent extends Event
{
    const NAME = 'huh.maps.before_render_map';

    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $templateData;
    /**
     * @var Map
     */
    private $map;

    /**
     * BeforeRenderMapEvent constructor.
     */
    public function __construct(string $template, array $templateData, Map $map)
    {
        $this->template = $template;
        $this->templateData = $templateData;
        $this->map = $map;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    public function setTemplateData(array $templateData): void
    {
        $this->templateData = $templateData;
    }

    public function getMap(): Map
    {
        return $this->map;
    }

    public function setMap(Map $map): void
    {
        $this->map = $map;
    }
}
