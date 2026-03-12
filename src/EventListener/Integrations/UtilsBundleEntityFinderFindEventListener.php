<?php

namespace HeimrichHannot\GoogleMapsBundle\EventListener\Integrations;

use Contao\ContentModel;
use Contao\ModuleModel;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use HeimrichHannot\UtilsBundle\EntityFinder\Element;
use HeimrichHannot\UtilsBundle\EntityFinder\EntityFinderHelper;
use HeimrichHannot\UtilsBundle\Event\EntityFinderFindEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
class UtilsBundleEntityFinderFindEventListener
{
    public function __construct(
        private readonly EntityFinderHelper $helper,
    ) {
    }

    public function __invoke(EntityFinderFindEvent $event): void
    {
        if (!in_array($event->table, [GoogleMapModel::getTable(), OverlayModel::getTable()])) {
            return;
        }

        if ($event->table === GoogleMapModel::getTable()) {
            $this->googleMaps($event);
        } else {
            $this->overlay($event);
        }
    }

    private function overlay(EntityFinderFindEvent $event): void
    {
        $overlay = OverlayModel::findById($event->id);
        if (!$overlay) {
            return;
        }

        $event->setElement(
            new Element(
                id: $overlay->id,
                table: OverlayModel::getTable(),
                description: 'Google Maps Overlay: '.$overlay->title.' (ID: '.$overlay->id.')',
                parents: (function () use ($overlay): \Generator {
                    yield [
                        'table' => GoogleMapModel::getTable(),
                        'id' => $overlay->pid,
                    ];
                })()
            )
        );
    }

    private function googleMaps(EntityFinderFindEvent $event): void
    {
        $model = GoogleMapModel::findById($event->id);
        if (!$model) {
            return;
        }

        $event->setElement(
            new Element(
                id: $model->id,
                table: GoogleMapModel::getTable(),
                description: 'Google Maps: '.$model->title.' (ID: '.$model->id.')',
                parents: (function () use ($model): \Generator {
                    $contentElements = ContentModel::findBy(['googlemaps_map=?'], [$model->id]) ?? [];

                    foreach ($contentElements as $contentElement) {
                        yield [
                            'table' => ContentModel::getTable(),
                            'id' => $contentElement->id,
                        ];
                    }
                    $frontendModules = ModuleModel::findBy(['googlemaps_map=?'], [$model->id]) ?? [];

                    foreach ($frontendModules as $frontendModule) {
                        yield [
                            'table' => ModuleModel::getTable(),
                            'id' => $frontendModule->id,
                        ];
                    }

                    foreach ($this->helper->findModulesByInserttag('html', 'html', 'google_map', $model->id) as $module) {
                        yield [
                            'table' => 'tl_module',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findModulesByInserttag('html', 'html', 'google_map_html', $model->id) as $module) {
                        yield [
                            'table' => 'tl_module',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findModulesByInserttag('html', 'html', 'google_map_css', $model->id) as $module) {
                        yield [
                            'table' => 'tl_module',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findModulesByInserttag('html', 'html', 'google_map_js', $model->id) as $module) {
                        yield [
                            'table' => 'tl_module',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findContentElementByInserttag('html', 'html', 'google_map', $model->id) as $module) {
                        yield [
                            'table' => 'tl_content',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findContentElementByInserttag('html', 'html', 'google_map_html', $model->id) as $module) {
                        yield [
                            'table' => 'tl_content',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findContentElementByInserttag('html', 'html', 'google_map_css', $model->id) as $module) {
                        yield [
                            'table' => 'tl_content',
                            'id' => $module->id,
                        ];
                    }
                    foreach ($this->helper->findContentElementByInserttag('html', 'html', 'google_map_js', $model->id) as $module) {
                        yield [
                            'table' => 'tl_content',
                            'id' => $module->id,
                        ];
                    }
                })()
            )
        );
    }
}
