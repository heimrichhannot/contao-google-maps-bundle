<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventSubscriber;

use Contao\ContentModel;
use Contao\ModuleModel;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\UtilsBundle\Event\ExtendEntityFinderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityFinderSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ExtendEntityFinderEvent::class => 'onExtendEntityFinderEvent',
        ];
    }

    public function onExtendEntityFinderEvent(ExtendEntityFinderEvent $event): void
    {
        switch ($event->getTable()) {
            case GoogleMapModel::getTable():
                $map = GoogleMapModel::findByPk($event->getId());

                if ($map) {
                    if (!$event->isOnlyText()) {
                        $contentElements = ContentModel::findBy(['googlemaps_map=?'], [$map->id]);

                        if ($contentElements) {
                            foreach ($contentElements as $contentElement) {
                                $event->addParent(ContentModel::getTable(), $contentElement->id);
                            }
                        }
                        $frontendModules = ModuleModel::findBy(['googlemaps_map=?'], [$map->id]);

                        if ($frontendModules) {
                            foreach ($frontendModules as $frontendModule) {
                                $event->addParent(ModuleModel::getTable(), $frontendModule->id);
                            }
                        }
                    }
                    $event->setOutput('Google Maps: '.$map->title.' (ID: '.$map->id.')');
                    $event->addInserttag('{{google_map::'.$map->id.'}}');
                    $event->addInserttag('{{google_map_html::'.$map->id.'}}');
                    $event->addInserttag('{{google_map_css::'.$map->id.'}}');
                    $event->addInserttag('{{google_map_js::'.$map->id.'}}');
                }

                break;

            case OverlayModel::getTable():
                $overlay = OverlayModel::findByPk($event->getId());

                if ($overlay) {
                    $event->addParent(GoogleMapModel::getTable(), $overlay->pid);
                    $event->setOutput('Google Maps Overlay: '.$overlay->title.' (ID: '.$overlay->id.')');
                }
        }
    }
}
