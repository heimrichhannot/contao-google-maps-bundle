<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

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

    public function getItemData(): array
    {
        return $this->itemData;
    }

    public function getOverlayModel(): ?OverlayModel
    {
        return $this->overlayModel;
    }

    /**
     * Set the overlay model for the current item. Set null to skip adding a marker
     * for the current item.
     */
    public function setOverlayModel(?OverlayModel $overlayModel = null): void
    {
        $this->overlayModel = $overlayModel;
    }

    public function getConfigModel(): Model
    {
        return $this->configModel;
    }
}
