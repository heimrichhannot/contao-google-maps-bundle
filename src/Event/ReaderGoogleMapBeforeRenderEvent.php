<?php

namespace HeimrichHannot\GoogleMapsBundle\Event;

use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\ReaderBundle\Item\ItemInterface;
use HeimrichHannot\ReaderBundle\Model\ReaderConfigElementModel;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\Event;

class ReaderGoogleMapBeforeRenderEvent extends Event
{
    const NAME = 'huh.google_maps.event.reader_before_render';

    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @var Map
     */
    protected $map;

    /**
     * @var GoogleMapModel
     */
    protected $mapConfig;

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
     * @return ItemInterface
     */
    public function getItem(): ItemInterface
    {
        return $this->item;
    }

    /**
     * @param ItemInterface $item
     */
    public function setItem(ItemInterface $item): void
    {
        $this->item = $item;
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

    /**
     * @return GoogleMapModel
     */
    public function getMapConfig(): GoogleMapModel
    {
        return $this->mapConfig;
    }

    /**
     * @param GoogleMapModel $mapConfig
     */
    public function setMapConfig(GoogleMapModel $mapConfig): void
    {
        $this->mapConfig = $mapConfig;
    }
}
