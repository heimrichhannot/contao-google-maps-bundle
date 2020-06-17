<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Ivory\GoogleMap\Helper\Renderer\Html;

use Ivory\GoogleMap\Helper\Formatter\Formatter;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class StylesheetTagRenderer extends AbstractTagRenderer
{
    /**
     * @var StylesheetRenderer
     */
    private $stylesheetRenderer;

    public function __construct(Formatter $formatter, TagRenderer $tagRenderer, StylesheetRenderer $stylesheetRenderer)
    {
        parent::__construct($formatter, $tagRenderer);

        $this->setStylesheetRenderer($stylesheetRenderer);
    }

    /**
     * @return StylesheetRenderer
     */
    public function getStylesheetRenderer()
    {
        return $this->stylesheetRenderer;
    }

    public function setStylesheetRenderer(StylesheetRenderer $stylesheetRenderer)
    {
        $this->stylesheetRenderer = $stylesheetRenderer;
    }

    /**
     * @param string   $name
     * @param string[] $stylesheets
     * @param string[] $attributes
     * @param bool     $newLine
     *
     * @return string
     */
    public function render($name, array $stylesheets, array $attributes = [], $newLine = true)
    {
        $formatter = $this->getFormatter();

        $tagStylesheets = [];

        foreach ($stylesheets as $stylesheet => $value) {
            $tagStylesheets[] = $this->stylesheetRenderer->render($stylesheet, $value);
        }

        return $this->getTagRenderer()->render(
            'style',
            $formatter->renderLines([
                $name.$formatter->renderSeparator().'{',
                $formatter->renderIndentation($formatter->renderLines($tagStylesheets, true, false)),
                '}',
            ], !empty($tagStylesheets), false),
            // FIX
            array_merge(['type' => 'text/css'], $attributes),
            // ENDFIX
            $newLine
        );
    }
}
