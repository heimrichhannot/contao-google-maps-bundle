<?php

namespace HeimrichHannot\GoogleMapsBundle\MapBuilder;

use Contao\Model;
use Contao\Model\Collection;
use HeimrichHannot\GoogleMapsBundle\Event\GoogleMapsPrepareExternalItemEvent;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;
use HeimrichHannot\GoogleMapsBundle\Manager\OverlayManager;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MapBuilder implements \Stringable
{
    private array|Collection $overlays;
    private int $mapId;

    private bool $prepared = false;
    private array $mapTemplateData;

    public function __construct(
        private readonly MapManager $mapManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OverlayManager $overlayManager,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function setMapId(int $id): self
    {
        $this->mapId = $id;

        return $this;
    }

    public function setConfig(array $config): self
    {
        $this->mapTemplateData = $config;

        return $this;
    }

    public function addConfig(string $key, $value): self
    {
        $this->mapTemplateData[$key] = $value;

        return $this;
    }

    public function setMapIfIfExist(mixed $mapId): self
    {
        if (empty($mapId)) {
            return $this;
        }

        if (is_scalar($mapId)) {
            return $this->setMapId((int) $mapId);
        }

        if (is_array($mapId)) {
            foreach ($mapId as $id) {
                if (is_scalar($id)) {
                    $this->setMapIfIfExist((int) $id);
                    if (isset($this->mapId)) {
                        return $this;
                    }
                }
            }
        }

        return $this;
    }

    public function addOverlays(array|Collection $overlays): self
    {
        if ($this->prepared) {
            throw new \RuntimeException('Map already build.');
        }
        $this->overlays = $overlays;

        return $this;
    }

    public function buildIfExist(): ?self
    {
        try {
            $this->build();
        } catch (\RuntimeException) {
            return null;
        }

        return $this;
    }

    public function build(array $config = []): self
    {
        if (!isset($this->mapId)) {
            throw new \RuntimeException('Map ID not set.');
        }
        if ($this->prepared) {
            return $this;
        }
        $overlays = $this->buildOverlays();

        $templateData = $this->mapManager->prepareMap($this->mapId, $config, $overlays);

        if (null === $templateData) {
            throw new \RuntimeException('Map data not found in template.');
        }

        $this->mapTemplateData = $templateData;

        $this->prepared = true;

        return $this;
    }

    public function getMarker(int $id): ?MarkerHelper
    {
        if (!$this->prepared) {
            throw new \RuntimeException('Map must be build before accessing marker.');
        }

        $markerVariableMapping = $this->overlayManager->getMarkerVariableMapping();
        if (!isset($markerVariableMapping[$id])) {
            return null;
        }

        return new MarkerHelper(
            $markerVariableMapping[$id],
            $this->requestStack->getCurrentRequest() ?: new Request()
        );
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        if (!$this->prepared) {
            $this->build();
        }

        return $this->mapManager->renderMapObject(
            $this->mapTemplateData['mapModel'],
            $this->mapId,
            $this->mapTemplateData['mapConfigModel'],
            $this->mapTemplateData
        );
    }

    public function withConfig(array $config = []): self
    {
        $new = clone $this;
        $new->mapTemplateData = array_merge($new->mapTemplateData ?? [], $config);

        return $new;
    }

    public function html(): self
    {
        return $this->withConfig([
            'skipCss' => false,
            'skipJs' => false,
        ]);
    }

    public function js(): self
    {
        return $this->withConfig([
            'skipHtml' => true,
            'skipCss' => false,
        ]);
    }

    public function css(): self
    {
        return $this->withConfig([
            'skipHtml' => true,
            'skipJs' => false,
        ]);
    }

    private function buildOverlays(): ?Collection
    {
        if (empty($this->overlays)) {
            return null;
        }

        $overlays = [];

        foreach ($this->overlays as $overlayData) {
            if ($overlayData instanceof OverlayModel) {
                $model = $overlayData;
                $overlayData = $model->row();
            } else {
                if ($overlayData instanceof Model) {
                    $overlayData = $overlayData->row();
                    unset($overlayData['id']);
                } elseif (!is_array($overlayData)) {
                    throw new \RuntimeException('Invalid data passed as overlay definition.');
                }
                $model = new OverlayModel();
                $model->setRow($overlayData);
            }

            $event = new GoogleMapsPrepareExternalItemEvent(
                $overlayData,
                $model,
                [
                    'mapId' => $this->mapId,
                ]
            );
            $this->eventDispatcher->dispatch($event);
            $overlays[] = $event->overlayModel;
        }

        return new Collection($overlays, OverlayModel::getTable());
    }
}
