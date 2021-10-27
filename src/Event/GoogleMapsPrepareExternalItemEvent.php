<?php

namespace HeimrichHannot\GoogleMapsBundle\Event;

use Contao\Model;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use Symfony\Contracts\EventDispatcher\Event;

class GoogleMapsPrepareExternalItemEvent extends Event
{

    /**
     * @var array
     */
    private $itemData;
    /**
     * @var OverlayModel
     */
    private $overlayModel;
    /**
     * @var Model
     */
    private $configModel;

    public function __construct(array $itemData, OverlayModel $overlayModel, Model $configModel)
    {
        $this->itemData = $itemData;
        $this->overlayModel = $overlayModel;
        $this->configModel = $configModel;
    }

    /**
     * @return array
     */
    public function getItemData(): array
    {
        return $this->itemData;
    }

    /**
     * @return OverlayModel|null
     */
    public function getOverlayModel(): ?OverlayModel
    {
        return $this->overlayModel;
    }

    /**
     * Set the overlay model for the current item.
     * Set null to skip adding a marker for the current item.
     *
     * @param OverlayModel|null $overlayModel
     */
    public function setOverlayModel(OverlayModel $overlayModel = null): void
    {
        $this->overlayModel = $overlayModel;
    }

    /**
     * @return Model
     */
    public function getConfigModel(): Model
    {
        return $this->configModel;
    }


}