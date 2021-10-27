<?php

namespace HeimrichHannot\GoogleMapsBundle\EventSubscriber;

use Contao\Environment;
use HeimrichHannot\GoogleMapsBundle\Event\GoogleMapsPrepareExternalItemEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Manager\OverlayManager;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\ListBundle\Event\ListBeforeParseItemsEvent;
use HeimrichHannot\ListBundle\Event\ListBeforeRenderEvent;
use HeimrichHannot\ListBundle\Model\ListConfigModel;
use HeimrichHannot\UtilsBundle\Model\ModelUtil;
use Model\Collection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ListBundleSubscriber implements EventSubscriberInterface
{
    private $mapManager;
    /**
     * @var ModelUtil
     */
    private $modelUtil;
    /**
     * @var OverlayManager
     */
    private $overlayManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /** @var array  */
    private $maps = [];

    public function __construct(MapManager $mapManager, ModelUtil $modelUtil, OverlayManager $overlayManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->mapManager = $mapManager;
        $this->modelUtil = $modelUtil;
        $this->overlayManager = $overlayManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents()
    {
        $subscriptions = [];

        if (class_exists('HeimrichHannot\ListBundle\Event\ListBeforeParseItemsEvent')) {
            $subscriptions[ListBeforeParseItemsEvent::NAME] = 'onListBeforeParseItemsEvent';
        }

        if (class_exists('HeimrichHannot\ListBundle\Event\ListBeforeRenderEvent')) {
            $subscriptions[ListBeforeRenderEvent::NAME] = 'onListBeforeRenderEvent';
        }

        return $subscriptions;
    }

    public function onListBeforeParseItemsEvent(ListBeforeParseItemsEvent $event): void
    {
        if (!$event->getListConfig()->renderItemsAsMap) {
            return;
        }
        if (null === ($map = $this->modelUtil->findModelInstanceByPk('tl_google_map',
                $event->getListConfig()->itemMap))) {
            return;
        }

        $mapId = $event->getListConfig()->itemMap;
        $overlays = $this->transformItemsToOverlays($event->getItems(), $event->getListConfig());
        $templateData = $this->mapManager->prepareMap($mapId, $map->row(), $overlays);

        if (null === $templateData) {
            return;
        }

        $this->maps[$mapId] = $templateData['mapModel'];

        $markerVariableMapping = $this->overlayManager->getMarkerVariableMapping();

        $items = [];

        foreach ($event->getItems() as $item) {
            $item['markerVariable'] = $markerVariableMapping[$item['id']];
            $item['markerHref']     = Environment::get('uri') . '#' . $markerVariableMapping[$item['id']];

            $items[] = $item;
        }

        $event->setItems($items);
    }

    public function onListBeforeRenderEvent(ListBeforeRenderEvent $event): void
    {
        if (!$event->getListConfig()->renderItemsAsMap) {
            return;
        }

        $listConfig = $event->getListConfig();
        $mapId = $listConfig->itemMap;

        if (!isset($this->maps[$mapId])) {
            return;
        }

        $templateData = $event->getTemplateData();

        $templateData['renderedMap'] = $this->mapManager->renderMapObject($this->maps[$mapId], $mapId);
        // $this->mapManager->render($listConfig->itemMap, $map->row(), $overlays);
        $templateData['addMapControlList'] = (bool)$listConfig->addMapControlList;

        $event->setTemplateData($templateData);
    }

    public function transformItemsToOverlays(array $items, ListConfigModel $configModel)
    {
        $models = [];
        foreach ($items as $item) {
            $overlay = new OverlayModel();
            $overlay->setRow($item);
            /** @var GoogleMapsPrepareExternalItemEvent $event */
            $event = $this->eventDispatcher->dispatch(new GoogleMapsPrepareExternalItemEvent($item, $overlay, $configModel));
            if ($overlay = $event->getOverlayModel()) {
                $models[] = $overlay;
            }
        }
        return new Collection($models, 'tl_google_map_overlay');
    }
}