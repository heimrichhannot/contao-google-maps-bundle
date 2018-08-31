<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 30.08.18
 * Time: 16:57
 */

namespace HeimrichHannot\GoogleMapsBundle\Event;


use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use Ivory\GoogleMap\Map;
use Symfony\Component\EventDispatcher\Event;


class GoogleMapBeforeRenderEvent extends Event
{
    
    protected $item;
    
    /**
     * @var Map
     */
    protected $map;
    
    /**
     * @var GoogleMapModel
     */
    protected $mapConfig;
    
    
    
    
    
    public function getItem()
    {
        return $this->item;
    }
    
    
    public function setItem($item): void
    {
        $this->item = $item;
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