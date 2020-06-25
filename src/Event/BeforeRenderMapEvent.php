<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


namespace HeimrichHannot\GoogleMapsBundle\Event;


use Ivory\GoogleMap\Map;

class BeforeRenderMapEvent
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

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getTemplateData(): array
    {
        return $this->templateData;
    }

    /**
     * @param array $templateData
     */
    public function setTemplateData(array $templateData): void
    {
        $this->templateData = $templateData;
    }

    /**
     * @return Map
     */
    public function getMap(): Map
    {
        return $this->map;
    }

    /**
     * @param Map $map
     */
    public function setMap(Map $map): void
    {
        $this->map = $map;
    }


}