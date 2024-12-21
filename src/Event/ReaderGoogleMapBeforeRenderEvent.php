<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;

use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\ReaderBundle\Item\ItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use Ivory\GoogleMap\Map;

/**
 * @deprecated Use GoogleMapsPrepareExternalItemEvent instead
 */
class ReaderGoogleMapBeforeRenderEvent extends GoogleMapBeforeRenderEvent
{
    const NAME = 'huh.google_maps.event.reader_before_render';

    /**
     * @var ReaderConfigElementModel
     */
    protected $readerConfigElement;

    /**
     * ReaderGoogleMapBeforeRenderEvent constructor.
     */
    public function __construct(ItemInterface $item, Map $map, GoogleMapModel $mapConfig, ReaderConfigElementModel $readerConfigElement)
    {
        $this->item = $item;
        $this->map = $map;
        $this->mapConfig = $mapConfig;
        $this->readerConfigElement = $readerConfigElement;
    }

    public function getReaderConfigElement(): ReaderConfigElementModel
    {
        return $this->readerConfigElement;
    }

    public function setReaderConfigElement(ReaderConfigElementModel $readerConfigElement): void
    {
        $this->readerConfigElement = $readerConfigElement;
    }
}
