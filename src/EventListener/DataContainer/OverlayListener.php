<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer;

use Contao\Backend;
use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Date;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Ivory\GoogleMap\Overlay\Animation;

class OverlayListener extends Backend
{
    const TYPE_MARKER = 'marker';

    const TYPE_INFO_WINDOW = 'infowindow';

    const TYPE_POLYLINE = 'polyline';

    const TYPE_POLYGON = 'polygon';

    const TYPE_CIRCLE = 'circle';

    const TYPE_RECTANGLE = 'rectangle';

    const TYPE_GROUND_OVERLAY = 'ground_overlay';

    const TYPE_KML_LAYER = 'kml';

    const TYPES = [
        self::TYPE_MARKER,
        self::TYPE_INFO_WINDOW,
        self::TYPE_POLYLINE,
        self::TYPE_POLYGON,
        self::TYPE_CIRCLE,
        self::TYPE_RECTANGLE,
        self::TYPE_GROUND_OVERLAY,
        self::TYPE_KML_LAYER,
    ];

    const TITLE_MODE_TITLE_FIELD = 'title_field';

    const TITLE_MODE_CUSTOM_TEXT = 'custom_text';

    const TITLE_MODES = [
        self::TITLE_MODE_TITLE_FIELD,
        self::TITLE_MODE_CUSTOM_TEXT,
    ];

    const MARKER_TYPE_SIMPLE = 'simple';

    const MARKER_TYPE_ICON = 'icon';

    const MARKER_TYPES = [
        self::MARKER_TYPE_SIMPLE,
        self::MARKER_TYPE_ICON,
    ];

    const CLICK_EVENT_LINK = 'link';

    const CLICK_EVENT_INFO_WINDOW = 'infowindow';

    const CLICK_EVENTS = [
        self::CLICK_EVENT_LINK,
        self::CLICK_EVENT_INFO_WINDOW,
    ];

    const POSITIONING_MODE_COORDINATE = 'coordinate';

    const POSITIONING_MODE_STATIC_ADDRESS = 'static_address';

    const POSITIONING_MODES = [
        self::POSITIONING_MODE_COORDINATE,
        self::POSITIONING_MODE_STATIC_ADDRESS,
    ];

    const ANIMATIONS = [
        Animation::BOUNCE,
        Animation::DROP,
    ];

    protected ContaoFramework $framework;

    protected Utils $utils;

    public function __construct(ContaoFramework $framework, Utils $utils)
    {
        $this->framework = $framework;
        $this->utils = $utils;
        parent::__construct();
    }

    #[AsCallback(table: 'tl_google_map_overlay', target: 'list.sorting.child_record')]
    public function listChildren($arrRow)
    {
        return '<div class="tl_content_left">'.($arrRow['title'] ?: $arrRow['id']).' <span style="color:#b3b3b3; padding-left:3px">['.
            Date::parse(Config::get('datimFormat'), $arrRow['dateAdded']).']</span></div>';
    }

    #[AsCallback(table: 'tl_google_map_overlay', target: 'config.onload')]
    public function modifyDca(DataContainer $dc): void
    {
        /** @var GoogleMapModel $adapter */
        $adapter = $this->framework->getAdapter(GoogleMapModel::class);

        if (null === ($overlay = $this->utils->model()->findModelInstanceByPk($dc->table, $dc->id))) {
            return;
        }

        /** @var GoogleMapModel $map */
        if (null === ($map = $adapter->findByPK($overlay->pid))) {
            return;
        }

        if (GoogleMapListener::MAP_TYPE_RESPONSIVE === $map->type) {
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['closed'] = true;
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['notCreatable'] = true;
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['notEditable'] = true;
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['notCopyable'] = true;
        }
    }
}
