<?php

namespace HeimrichHannot\GoogleMapsBundle\Event;

use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\ReaderBundle\Item\ItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\Event;

class ReaderGoogleMapBeforeRenderEvent extends GoogleMapBeforeRenderEvent
{
    const NAME = 'huh.google_maps.event.reader_before_render';

    /**
     * @var ReaderConfigElementModel
     */
    protected $readerConfigElement;

    /**
     * ReaderGoogleMapBeforeRenderEvent constructor.
     *
     * @param $templateName
     * @param array         $templateData
     * @param ItemInterface $item
     */
    public function __construct(ItemInterface $item, Map $map, GoogleMapModel $mapConfig, ReaderConfigElementModel $readerConfigElement)
    {
        $this->item = $item;
        $this->map = $map;
        $this->mapConfig = $mapConfig;
        $this->readerConfigElement = $readerConfigElement;
    }

    /**
     * @return ReaderConfigElementModel
     */
    public function getReaderConfigElement(): ReaderConfigElementModel
    {
        return $this->readerConfigElement;
    }

    /**
     * @param ReaderConfigElementModel $readerConfigElement
     */
    public function setReaderConfigElement(ReaderConfigElementModel $readerConfigElement): void
    {
        $this->readerConfigElement = $readerConfigElement;
    }
}
