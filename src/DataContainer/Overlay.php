<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\DataContainer;

use Contao\Backend;
use Contao\DataContainer;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use Ivory\GoogleMap\Overlay\Animation;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Contao\Config;
use Contao\BackendUser;
use Contao\Database;
use Contao\Input;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Contao\System;
use Contao\StringUtil;
use Contao\Image;
use Contao\Date;
use Contao\Versions;

class Overlay extends Backend
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

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    public function listChildren($arrRow)
    {
        return '<div class="tl_content_left">'.($arrRow['title'] ?: $arrRow['id']).' <span style="color:#b3b3b3; padding-left:3px">['.
            Date::parse(Config::get('datimFormat'), trim($arrRow['dateAdded'])).']</span></div>';
    }

    /**
     * Modify palettes.
     */
    public function modifyDca(DataContainer $dc)
    {
        /** @var GoogleMapModel $adapter */
        $adapter = $this->container->get('contao.framework')->getAdapter(GoogleMapModel::class);

        if (null === ($overlay = $this->container->get('huh.utils.model')->findModelInstanceByPk($dc->table, $dc->id))) {
            return;
        }

        /** @var GoogleMapModel $map */
        if (null === ($map = $adapter->findByPK($overlay->pid))) {
            return;
        }

        if (GoogleMap::MAP_TYPE_RESPONSIVE === $map->type) {
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['closed'] = true;
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['notCreatable'] = true;
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['notEditable'] = true;
            $GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['notCopyable'] = true;
        }
    }

    public function checkPermission()
    {
        $user = BackendUser::getInstance();
        $database = Database::getInstance();

        if ($user->isAdmin) {
            return;
        }

        // Set the root IDs
        if (!\is_array($user->contao_google_maps_bundles) || empty($user->contao_google_maps_bundles)) {
            $root = [0];
        } else {
            $root = $user->contao_google_maps_bundles;
        }

        $id = \strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

        // Check current action
        switch (Input::get('act')) {
            case 'paste':
                // Allow
                break;

            case 'create':
                if (!\strlen(Input::get('pid')) || !\in_array(Input::get('pid'), $root)) {
                    throw new AccessDeniedException('Not enough permissions to create google_map_overlay items in google_map_overlay archive ID '.Input::get('pid').'.');
                }

                break;

            case 'cut':
            case 'copy':
                if (!\in_array(Input::get('pid'), $root)) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' google_map_overlay item ID '.$id.' to google_map_overlay archive ID '.Input::get('pid').'.');
                }
            // no break STATEMENT HERE

            case 'edit':
            case 'show':
            case 'delete':
            case 'toggle':
            case 'feature':
                $objArchive = $database->prepare('SELECT pid FROM tl_google_map_overlay WHERE id=?')
                    ->limit(1)
                    ->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new AccessDeniedException('Invalid google_map_overlay item ID '.$id.'.');
                }

                if (!\in_array($objArchive->pid, $root)) {
                    throw new AccessDeniedException('Not enough permissions to '.Input::get('act').' google_map_overlay item ID '.$id.' of google_map_overlay archive ID '.$objArchive->pid.'.');
                }

                break;

            case 'select':
            case 'editAll':
            case 'deleteAll':
            case 'overrideAll':
            case 'cutAll':
            case 'copyAll':
                if (!\in_array($id, $root)) {
                    throw new AccessDeniedException('Not enough permissions to access google_map_overlay archive ID '.$id.'.');
                }

                $objArchive = $database->prepare('SELECT id FROM tl_google_map_overlay WHERE pid=?')
                    ->execute($id);

                if ($objArchive->numRows < 1) {
                    throw new AccessDeniedException('Invalid google_map_overlay archive ID '.$id.'.');
                }

                /** @var SessionInterface $session */
                $session = System::getContainer()->get('session');

                $session = $session->all();
                $session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
                $session->replace($session);

                break;

            default:
                if (\strlen(Input::get('act'))) {
                    throw new AccessDeniedException('Invalid command "'.Input::get('act').'".');
                } elseif (!\in_array($id, $root)) {
                    throw new AccessDeniedException('Not enough permissions to access google_map_overlay archive ID '.$id.'.');
                }

                break;
        }
    }

    public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
    {
        $user = BackendUser::getInstance();

        if (\strlen(Input::get('tid'))) {
            $this->toggleVisibility(Input::get('tid'), ('1' === Input::get('state')), (@func_get_arg(12) ?: null));
            $this->redirect($this->getReferer());
        }

        // Check permissions AFTER checking the tid, so hacking attempts are logged
        if (!$user->hasAccess('tl_google_map_overlay::published', 'alexf')) {
            return '';
        }

        $href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

        if (!$row['published']) {
            $icon = 'invisible.svg';
        }

        return '<a href="'.$this->addToUrl($href).'" title="'.StringUtil::specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label, 'data-state="'.($row['published'] ? 1 : 0).'"').'</a> ';
    }

    public function toggleVisibility($intId, $blnVisible, \DataContainer $dc = null)
    {
        $user = BackendUser::getInstance();
        $database = Database::getInstance();

        // Set the ID and action
        Input::setGet('id', $intId);
        Input::setGet('act', 'toggle');

        if ($dc) {
            $dc->id = $intId; // see #8043
        }

        // Trigger the onload_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['onload_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['onload_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        // Check the field access
        if (!$user->hasAccess('tl_google_map_overlay::published', 'alexf')) {
            throw new AccessDeniedException('Not enough permissions to publish/unpublish google_map_overlay item ID '.$intId.'.');
        }

        // Set the current record
        if ($dc) {
            $objRow = $database->prepare('SELECT * FROM tl_google_map_overlay WHERE id=?')
                ->limit(1)
                ->execute($intId);

            if ($objRow->numRows) {
                $dc->activeRecord = $objRow;
            }
        }

        $objVersions = new Versions('tl_google_map_overlay', $intId);
        $objVersions->initialize();

        // Trigger the save_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_google_map_overlay']['fields']['published']['save_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_google_map_overlay']['fields']['published']['save_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $blnVisible = $this->{$callback[0]}->{$callback[1]}($blnVisible, $dc);
                } elseif (\is_callable($callback)) {
                    $blnVisible = $callback($blnVisible, $dc);
                }
            }
        }

        $time = time();

        // Update the database
        $database->prepare("UPDATE tl_google_map_overlay SET tstamp=$time, published='".($blnVisible ? '1' : '')."' WHERE id=?")
            ->execute($intId);

        if ($dc) {
            $dc->activeRecord->tstamp = $time;
            $dc->activeRecord->published = ($blnVisible ? '1' : '');
        }

        // Trigger the onsubmit_callback
        if (\is_array($GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['onsubmit_callback'])) {
            foreach ($GLOBALS['TL_DCA']['tl_google_map_overlay']['config']['onsubmit_callback'] as $callback) {
                if (\is_array($callback)) {
                    $this->import($callback[0]);
                    $this->{$callback[0]}->{$callback[1]}($dc);
                } elseif (\is_callable($callback)) {
                    $callback($dc);
                }
            }
        }

        $objVersions->create();
    }
}
