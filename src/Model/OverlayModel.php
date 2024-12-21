<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Model;

use Contao\Model;

/**
 * @property string $id
 * @property string $pid
 * @property string $tstamp
 * @property string $dateAdded
 * @property string $title
 * @property string $type
 * @property string $titleMode
 * @property string $titleText
 * @property string $positioningMode
 * @property string $positioningLat
 * @property string $positioningLng
 * @property string $positioningAddress
 * @property string $infoWindowWidth
 * @property string $infoWindowHeight
 * @property string $infoWindowText
 * @property string $addRouting
 * @property string $routingAddress
 * @property string $routingTemplate
 * @property string $animation
 * @property string $markerType
 * @property string $iconSrc
 * @property string $iconWidth
 * @property string $iconHeight
 * @property string $iconAnchorX
 * @property string $iconAnchorY
 * @property string $clickEvent
 * @property string $url
 * @property string $target
 * @property string $infoWindowAnchorX
 * @property string $infoWindowAnchorY
 * @property string $kmlUrl
 * @property string $kmlClickable
 * @property string $kmlPreserveViewport
 * @property string $kmlScreenOverlays
 * @property string $kmlSuppressInfowindows
 * @property string $pathCoordinates
 * @property string $strokeWeight
 * @property string $strokeColor
 * @property string $strokeOpacity
 * @property string $fillColor
 * @property string $fillOpacity
 * @property string $zIndex
 * @property string $published
 * @property string $start
 * @property string $stop
 */
class OverlayModel extends Model
{
    protected static $strTable = 'tl_google_map_overlay';
}
