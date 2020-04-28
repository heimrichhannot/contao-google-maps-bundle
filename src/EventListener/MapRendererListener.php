<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use Ivory\GoogleMap\Event\Event;
use Ivory\GoogleMap\Helper\Event\MapEvent;
use Ivory\GoogleMap\Helper\MapHelper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MapRendererListener
{
    /** @var GoogleMapModel */
    protected $model;

    /**
     * @var MapManager
     */
    protected $manager;

    /**
     * @var MapHelper
     */
    protected $mapHelper;

    public function __construct(GoogleMapModel $model, MapManager $manager, MapHelper $mapHelper)
    {
        $this->model = $model;
        $this->manager = $manager;
        $this->mapHelper = $mapHelper;
    }

    public function renderStylesheet(MapEvent $event, string $eventName, EventDispatcherInterface $dispatcher)
    {
        $this->mapHelper->getEventDispatcher()->removeListener('map.stylesheet', [$this, 'renderStylesheet']);

        $responsiveSettings = StringUtil::deserialize($this->model->responsive, true);

        // sort by breakpoint asc in order to maintain mobile first
        usort($responsiveSettings, function ($a, $b) {
            return $a['breakpoint'] <=> $b['breakpoint'];
        });

        /** @var GoogleMapModel $adapter */
        $adapter = System::getContainer()->get('contao.framework')->getAdapter(GoogleMapModel::class);

        foreach ($responsiveSettings as $responsiveSetting) {
            if (empty($responsiveSetting['map']) || null === ($responsiveMapModel = $adapter->findByPk($responsiveSetting['map']))) {
                continue;
            }

            $responsiveMap = clone $event->getMap();

            $this->manager->setVisualization($responsiveMap, $responsiveMapModel);

            $event->addCode(preg_replace('/(<\s*style[^>]*>)(.*?)(<\s*\/\s*style>)/i', '$1@media (min-width:'.$responsiveSetting['breakpoint'].'px){$2}$3', $this->mapHelper->renderStylesheet($responsiveMap)));
        }

        $resizeEvent = new Event('window', 'resize', 'function(){
            var center = '.$event->getMap()->getVariable().'.getCenter();
            google.maps.event.trigger('.$event->getMap()->getVariable().', "resize");
            '.$event->getMap()->getVariable().'.setCenter(center);  
        }');

        $event->getMap()->getEventManager()->addDomEvent($resizeEvent);
    }

    public function getManager(): MapManager
    {
        return $this->manager;
    }

    public function setManager(MapManager $manager): void
    {
        $this->manager = $manager;
    }

    public function getModel(): GoogleMapModel
    {
        return $this->model;
    }

    public function setModel(GoogleMapModel $model): void
    {
        $this->model = $model;
    }
}
