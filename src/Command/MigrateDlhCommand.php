<?php

namespace HeimrichHannot\GoogleMapsBundle\Command;

use Contao\Config;
use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\CoreBundle\Framework\FrameworkAwareInterface;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Contao\Database;
use Contao\Model;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap;
use HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay;
use HeimrichHannot\GoogleMapsBundle\Event\DlhMigrationModifyMapEvent;
use HeimrichHannot\GoogleMapsBundle\Event\DlhMigrationModifyOverlayEvent;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MigrateDlhCommand extends AbstractLockedCommand implements FrameworkAwareInterface
{
    use FrameworkAwareTrait;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var bool
     */
    private $skipUnsupportedFieldWarnings;

    /**
     * @var bool
     */
    private $cleanBeforeMigration;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('huh:google-maps:migrate-dlh')->setDescription('Migrates existing Maps created using delahaye/dlh_googlemaps.');
        $this->addOption('skip-unsupported-field-warnings', null, InputOption::VALUE_NONE, 'Skip warnings indicating that fields don\'t exist anymore in Google Maps v3.');
        $this->addOption('clean-before-migration', null, InputOption::VALUE_NONE, 'Deletes ALL entries of tl_google_map and tl_google_map_overlay. Use with Caution.');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->io      = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->getParameter('kernel.project_dir');
        $this->framework->initialize();

        $this->dispatcher = System::getContainer()->get('event_dispatcher');
        $this->skipUnsupportedFieldWarnings = $input->getOption('skip-unsupported-field-warnings');
        $this->cleanBeforeMigration = $input->getOption('clean-before-migration');

        // clean
        if ($this->cleanBeforeMigration && $this->io->ask('CAUTION: You set the parameter "clean-before-migration". This will delete ALL entries of tl_google_map and tl_google_map_overlay. Are you sure? [y,n]') === 'y')
        {
            Database::getInstance()->execute('DELETE FROM tl_google_map');
            Database::getInstance()->execute('DELETE FROM tl_google_map_overlay');

            $this->io->success('tl_google_map and tl_google_map_overlay cleaned successfully.');
        }

        // API key
        $this->migrateApiKeys();

        // tl_dlh_googlemaps -> tl_google_map
        $this->migrateMaps();

        $this->io->success('dlh_googlemaps migration finished');

        return 0;
    }

    protected function migrateApiKeys()
    {
        $this->io->text('Step 1: Migrating existing API key...');

        $globalApiKey = Config::get('dlh_googlemaps_apikey');

        if ($globalApiKey) {
            Config::persist('googlemaps_apiKey', $globalApiKey);
            $this->io->success('Successfully migrated api key from localconfig.php');
        } else {
            $this->io->caution('No api key found in localconfig.php');
        }

        $globalApiKey = Config::get('googlemaps_apiKey');

        if (null !== ($pages = System::getContainer()->get('huh.utils.model')->findAllModelInstances('tl_page'))) {
            foreach ($pages as $page) {
                if (!($apiKey = $page->dlh_googlemaps_apikey)) {
                    continue;
                }

                if ($page->googlemaps_apiKey && $page->overrideGooglemaps_apiKey && $page->googlemaps_apiKey != $apiKey) {
                    $this->io->caution('An api key has been found in the field "dlh_googlemaps_apikey" in page ID ' . $page->id . ', but it couldn\'t be migrated because a differing api key is already set in the field "googlemaps_apiKey".');
                } elseif ($globalApiKey && $apiKey !== $globalApiKey) {
                    $page->overrideGooglemaps_apiKey = true;
                    $page->googlemaps_apiKey         = $apiKey;
                    $page->save();

                    $this->io->success('Successfully migrated api key for page ID ' . $page->id);
                }
            }
        }
    }

    protected function migrateMaps()
    {
        $this->io->text('Step 2: Migrating existing google maps...');

        if (null !== ($legacyMaps = System::getContainer()->get('huh.utils.model')->findAllModelInstances('tl_dlh_googlemaps'))) {
            foreach ($legacyMaps as $legacyMap) {
                $this->io->text('Migrating dlh google map ID ' . $legacyMap->id . ' ("' . $legacyMap->title . '") ...');

                $map         = new GoogleMapModel();
                $map->type   = 'base';
                $map->tstamp = $map->dateAdded = time();

                /** @var Database $dbAdapter */
                $dbAdapter = $this->framework->getAdapter(Database::class);
                $db        = $dbAdapter->getInstance();

                $legacyFields = $db->getFieldNames('tl_dlh_googlemaps');

                $skipFields = [
                    'id',
                    'tstamp',
                    'dateAdded'
                ];

                $fieldsMappings = [
                    'useMapTypeControl'    => 'addMapTypeControl',
                    'useZoomControl'       => 'addZoomControl',
                    'useRotateControl'     => 'addRotateControl',
                    'usePanControl'        => 'addPanControl',
                    'useScaleControl'      => 'addScaleControl',
                    'useStreetViewControl' => 'addStreetViewControl',
                    'useClusterer'         => 'addClusterer',
                    'mapTypeId'            => 'mapType'
                ];

                $fieldsToLower = [
                    'mapTypeId',
                    'mapTypesAvailable',
                    'mapTypeControlStyle',
                    'mapTypeControlPos',
                    'zoomControlPos',
                    'rotateControlPos',
                    'streetViewControlPos'
                ];

                $removedFields = [
                    'usePanControl',
                    'panControlPos',
                    'panControlStyle',
                    'useOverviewMapControl',
                    'overviewMapControlOpened',
                    'zoomControlStyle',
                    'scaleControlPos',
                    'parameter',
                    'moreParameter'
                ];

                $messageFields = [
                    'staticMapNoscript' => 'The current google map has a static map set. Please set the width and height manually.'
                ];

                foreach ($legacyFields as $legacyField) {
                    if (in_array($legacyField, $skipFields)) {
                        continue;
                    }

                    if (in_array($legacyField, $removedFields)) {
                        if ($legacyMap->{$legacyField} && !$this->skipUnsupportedFieldWarnings) {
                            $this->io->caution('The field "' . $legacyField . '" which is different from NULL in the current google map is not used in Google Maps v3 anymore or not supported by this bundle. Please refer to https://developers.google.com/maps/documentation/javascript for further information.');
                        }

                        continue;
                    }

                    if (in_array($legacyField, array_keys($messageFields))) {
                        $this->io->caution($messageFields[$legacyField]);
                    }

                    $newField    = $legacyField;
                    $legacyValue = $legacyMap->{$legacyField};

                    if (in_array($legacyField, $fieldsToLower)) {
                        $legacyValue = strtolower($legacyValue);
                    }

                    if (in_array($legacyField, array_keys($fieldsMappings))) {
                        $newField = $fieldsMappings[$legacyField];
                    }

                    if (in_array($legacyField, array_keys($fieldsMappings))) {
                        $newField = $fieldsMappings[$legacyField];
                    }

                    $map->{$newField} = $legacyValue;
                }

                // positioning
                $map->positioningMode = GoogleMap::POSITIONING_MODE_STANDARD;

                if ($legacyMap->geocoderAddress) {
                    $map->centerMode = GoogleMap::CENTER_MODE_STATIC_ADDRESS;

                    $address = $legacyMap->geocoderAddress;

                    if ($legacyMap->geocoderCountry) {
                        $address .= ', ' . $GLOBALS['TL_LANG']['CNT'][$legacyMap->geocoderCountry];
                    }

                    $map->centerAddress = $address;
                } else {
                    $map->centerMode = GoogleMap::CENTER_MODE_COORDINATE;

                    if (strpos($legacyMap->center, ',')) {
                        $coordinates = explode(',', $legacyMap->center);

                        if (is_array($coordinates) && count($coordinates) > 1) {
                            $map->centerLat = $coordinates[0];
                            $map->centerLng = $coordinates[1];
                        }
                    }
                }

                // sizing
                $mapSize = StringUtil::deserialize($legacyMap->mapSize, true);

                if (count($mapSize) > 2) {
                    $map->sizeMode = \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::SIZE_MODE_STATIC;

                    $map->width = serialize([
                        'value' => preg_replace('/[^\d]/i', '', $mapSize[0]),
                        'unit'  => 'px'
                    ]);

                    $map->height = serialize([
                        'value' => preg_replace('/[^\d]/i', '', $mapSize[1]),
                        'unit'  => 'px'
                    ]);
                } else {
                    $map->sizeMode     = \HeimrichHannot\GoogleMapsBundle\DataContainer\GoogleMap::SIZE_MODE_ASPECT_RATIO;
                    $map->aspectRatioX = 16;
                    $map->aspectRatioY = 9;
                }

                $this->dispatcher->dispatch(DlhMigrationModifyMapEvent::NAME, new DlhMigrationModifyMapEvent(
                    $legacyMap,
                    $map
                ));

                $map->save();

                // tl_dlh_googlemaps_elements -> tl_google_map_overlay
                $this->migrateOverlays($legacyMap, $map);

                $this->io->success('Successfully migrated dlh google map ID ' . $legacyMap->id . ' ("' . $legacyMap->title . '") to google map ID ' . $map->id);
            }
        }
    }

    protected function migrateOverlays(Model $legacyMap, GoogleMapModel $map)
    {
        $this->io->text('Migrating overlays of dlh google map ID ' . $legacyMap->id . ' ("' . $legacyMap->title . '") ...');

        if (null !== ($legacyOverlays = System::getContainer()->get('huh.utils.model')->findModelInstancesBy('tl_dlh_googlemaps_elements', ['pid=?'], [$legacyMap->id]))) {
            foreach ($legacyOverlays as $legacyOverlay) {
                $this->io->text('Migrating dlh google map overlay ID ' . $legacyOverlay->id . ' ("' . $legacyOverlay->title . '") ...');

                $overlay         = new OverlayModel();
                $overlay->tstamp = $overlay->dateAdded = time();
                $overlay->pid    = $map->id;

                /** @var Database $dbAdapter */
                $dbAdapter = $this->framework->getAdapter(Database::class);
                $db        = $dbAdapter->getInstance();

                $legacyFields = $db->getFieldNames('tl_dlh_googlemaps_elements');

                $skipFields = [
                    'id',
                    'pid',
                    'tstamp',
                    'dateAdded'
                ];

                $fieldsMappings = [
                    'iconSRC'         => 'iconSrc',
                    'markerAction'    => 'clickEvent',
                    'useRouting'      => 'addRouting',
                    'linkTitle'       => 'titleText',
                    'infoWindow'      => 'infoWindowText',
                    'popupInfoWindow' => 'infoWindowAutoOpen'
                ];

                $fieldsToLower = [
                    'type',
                    'markerType',
                    'markerAction'
                ];

                $removedFields = [
                    'hasShadow',
                    'shadowSRC',
                    'shadowSize'
                ];

                $messageFields = [
                    'staticMapNoscript' => 'The current google map has a static map set. Please set the width and height manually.'
                ];

                foreach ($legacyFields as $legacyField) {
                    if (in_array($legacyField, $skipFields)) {
                        continue;
                    }

                    if (in_array($legacyField, $removedFields)) {
                        if ($legacyOverlay->{$legacyField} && !$this->skipUnsupportedFieldWarnings) {
                            $this->io->caution('The field "' . $legacyField . '" which is different from NULL in the current google map is not used in Google Maps v3 anymore or not supported by this bundle. Please refer to https://developers.google.com/maps/documentation/javascript for further information.');
                        }

                        continue;
                    }

                    if (in_array($legacyField, array_keys($messageFields))) {
                        $this->io->caution($messageFields[$legacyField]);
                    }

                    $newField    = $legacyField;
                    $legacyValue = $legacyOverlay->{$legacyField};

                    if (in_array($legacyField, $fieldsToLower)) {
                        $legacyValue = strtolower($legacyValue);
                    }

                    if (in_array($legacyField, array_keys($fieldsMappings))) {
                        $newField = $fieldsMappings[$legacyField];
                    }

                    if (in_array($legacyField, array_keys($fieldsMappings))) {
                        $newField = $fieldsMappings[$legacyField];
                    }

                    $overlay->{$newField} = $legacyValue;
                }

                if ($overlay->clickEvent == 'none') {
                    $overlay->clickEvent = '';
                }

                if ($legacyOverlay->markerShowTitle) {
                    $overlay->titleMode = Overlay::TITLE_MODE_TITLE_FIELD;
                }

                if ($legacyOverlay->markerAction == 'LINK' && $legacyOverlay->linkTitle) {
                    $overlay->titleMode = Overlay::TITLE_MODE_CUSTOM_TEXT;
                }

                if ($legacyOverlay->markerAction == 'INFO') {
                    $overlay->clickEvent = 'infowindow';
                }

                // positioning
                if ($legacyOverlay->singleCoords) {
                    $overlay->positioningMode = Overlay::POSITIONING_MODE_COORDINATE;

                    if (strpos($legacyOverlay->singleCoords, ',')) {
                        $coordinates = explode(',', str_replace(' ', '', $legacyOverlay->singleCoords));

                        if (is_array($coordinates) && count($coordinates) > 1) {
                            $overlay->positioningLat = $coordinates[0];
                            $overlay->positioningLng = $coordinates[1];
                        }
                    }
                } else {
                    $overlay->positioningMode = Overlay::POSITIONING_MODE_STATIC_ADDRESS;
                    $address                  = $legacyOverlay->geocoderAddress;

                    if ($legacyOverlay->geocoderCountry) {
                        $address .= ', ' . $GLOBALS['TL_LANG']['CNT'][$legacyOverlay->geocoderCountry];
                    }

                    $overlay->positioningAddress = $address;
                }

                // marker type
                switch ($overlay->markerType) {
                    case \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::MARKER_TYPE_ICON:
                        $iconSize = StringUtil::deserialize($legacyOverlay->iconSize, true);

                        $overlay->iconWidth = ['value' => $iconSize[0], 'unit' => 'px'];
                        $overlay->iconHeight = ['value' => $iconSize[1], 'unit' => 'px'];

                        $iconAnchor = StringUtil::deserialize($legacyOverlay->iconAnchor, true);

                        $overlay->iconAnchorX = ['value' => $iconAnchor[0], 'unit' => 'px'];
                        $overlay->iconAnchorY = ['value' => $iconAnchor[1], 'unit' => 'px'];

                        break;
                }

                // info window
                // sizing
                $infoWindowSize = StringUtil::deserialize($legacyOverlay->infoWindowSize, true);

                if (count($infoWindowSize) > 2) {
                    $overlay->infoWindowWidth = serialize([
                        'value' => $infoWindowSize[0],
                        'unit'  => 'px'
                    ]);

                    $overlay->infoWindowHeight = serialize([
                        'value' => $infoWindowSize[1],
                        'unit'  => 'px'
                    ]);
                }

                // anchor
                $infoWindowAnchor = StringUtil::deserialize($legacyOverlay->infoWindowAnchor, true);

                if (count($infoWindowAnchor) > 2) {
                    $overlay->infoWindowAnchorX = $infoWindowAnchor[0];
                    $overlay->infoWindowAnchorY = $infoWindowAnchor[1];
                }

                $this->dispatcher->dispatch(DlhMigrationModifyOverlayEvent::NAME, new DlhMigrationModifyOverlayEvent(
                    $legacyOverlay,
                    $overlay,
                    $legacyMap,
                    $map
                ));

                $overlay->save();

                $this->io->success('Successfully migrated dlh google map overlay ID ' . $legacyMap->id . ' ("' . $legacyOverlay->title . '") to google map overlay ID ' . $overlay->id);
            }
        }
    }
}
