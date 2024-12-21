<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Command;

use Contao\Config;
use Contao\ContentModel;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Database;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use HeimrichHannot\GoogleMapsBundle\Event\DlhMigrationModifyMapEvent;
use HeimrichHannot\GoogleMapsBundle\Event\DlhMigrationModifyOverlayEvent;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\ContentListener;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\GoogleMapListener;
use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\OverlayListener;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use HeimrichHannot\GoogleMapsBundle\Model\OverlayModel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MigrateDlhCommand extends Command
{
    protected static $defaultName = 'huh:google-maps:migrate-dlh';

    protected static $defaultDescription = 'Migrates existing Maps created using delahaye/dlh_googlemaps.';

    protected bool $dryRun = false;

    protected array $mapMapper = [];

    private SymfonyStyle $io;

    private bool $skipUnsupportedFieldWarnings;

    private bool $cleanBeforeMigration;

    private EventDispatcherInterface $dispatcher;

    private ContaoFramework $framework;

    private Connection $connection;

    public function __construct(ContaoFramework $framework, EventDispatcherInterface $eventDispatcher, Connection $connection)
    {
        parent::__construct();
        $this->framework = $framework;
        $this->dispatcher = $eventDispatcher;
        $this->connection = $connection;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(static::$defaultDescription)
            ->addOption('skip-unsupported-field-warnings', null, InputOption::VALUE_NONE, 'Skip warnings indicating that fields don\'t exist anymore in Google Maps v3.')
            ->addOption('skip-contentelements', null, InputOption::VALUE_NONE, 'Skip migration of content elements.')
            ->addOption('skip-frontendmodules', null, InputOption::VALUE_NONE, 'Skip migration of frontend modules.')
            ->addOption('clean-before-migration', null, InputOption::VALUE_NONE, 'Deletes ALL entries of tl_google_map and tl_google_map_overlay. Use with Caution.')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Performs a run without making changes to the database.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Migrate dlh_googlemaps');

        if ($input->hasOption('dry-run') && $input->getOption('dry-run')) {
            $this->dryRun = true;
            $this->io->note('Dry run enabled.');
            $this->io->newLine();
        }
        $this->io->getFormatter()->setStyle('userwarning', new OutputFormatterStyle('red', null));

        $this->framework->initialize();

        $this->skipUnsupportedFieldWarnings = $input->getOption('skip-unsupported-field-warnings');
        $this->cleanBeforeMigration = $input->getOption('clean-before-migration');

        // clean
        if ($this->cleanBeforeMigration && $this->io->confirm('CAUTION: You set the parameter "clean-before-migration". This will delete ALL entries of tl_google_map and tl_google_map_overlay. Are you sure?')) {
            if (!$this->dryRun) {
                $this->connection->executeStatement('DELETE FROM tl_google_map');
                $this->connection->executeStatement('DELETE FROM tl_google_map_overlay');
            }

            $this->io->success('tl_google_map and tl_google_map_overlay cleaned successfully.');
        }

        // API key
        $this->migrateApiKeys();

        // tl_dlh_googlemaps -> tl_google_map
        $this->migrateMaps();

        if (!$input->hasOption('skip-contentelements') || !$input->getOption('skip-contentelements')) {
            $this->migrateContentElements();
        }

        if (!$input->hasOption('skip-frontendmodules') || !$input->getOption('skip-frontendmodules')) {
            $this->migrateFrontendModules();
        }

        $this->io->success('dlh_googlemaps migration finished');

        return 0;
    }

    protected function migrateApiKeys(): void
    {
        $this->io->section('Step 1: Migrating existing API key...');

        $globalApiKey = Config::get('dlh_googlemaps_apikey');

        if ($globalApiKey) {
            if (!$this->dryRun) {
                Config::persist('googlemaps_apiKey', $globalApiKey);
            }
            $this->io->success('Successfully migrated api key from localconfig.php');
        } else {
            $this->io->caution('No api key found in localconfig.php');
        }

        $globalApiKey = Config::get('googlemaps_apiKey');

        /** @var GoogleMapModel $configAdapter */
        $pageAdapter = $this->framework->getAdapter(PageModel::class);

        if (null !== ($pages = $pageAdapter->findAll())) {
            foreach ($pages as $page) {
                if (!($apiKey = $page->dlh_googlemaps_apikey)) {
                    continue;
                }

                if ($page->googlemaps_apiKey && $page->overrideGooglemaps_apiKey && $page->googlemaps_apiKey !== $apiKey) {
                    $this->io->caution('An api key has been found in the field "dlh_googlemaps_apikey" in page ID '.$page->id.', but it couldn\'t be migrated because a differing api key is already set in the field "googlemaps_apiKey".');
                } elseif ($globalApiKey && $apiKey !== $globalApiKey) {
                    $page->overrideGooglemaps_apiKey = true;
                    $page->googlemaps_apiKey = $apiKey;

                    if (!$this->dryRun) {
                        $page->save();
                    }

                    $this->io->success('Successfully migrated api key for page ID '.$page->id);
                }
            }
        }
    }

    protected function migrateMaps(): void
    {
        $this->io->section('Step 2: Migrating existing google maps...');

        $legacyMaps = $this->connection->fetchAllAssociative('SELECT * FROM tl_dlh_googlemaps');

        if (0 === \count($legacyMaps)) {
            $this->io->note('No existing maps found.');

            return;
        }

        foreach ($legacyMaps as $legacyMap) {
            $legacyMap = (object) $legacyMap;

            $this->io->newLine();
            $this->io->writeln('<options=bold>Migrating dlh google map ID '.$legacyMap->id.' ("'.$legacyMap->title.'") ...</>');

            $map = new GoogleMapModel();
            $map->type = 'base';
            $map->tstamp = $map->dateAdded = time();

            /** @var Database $dbAdapter */
            $dbAdapter = $this->framework->getAdapter(Database::class);
            $db = $dbAdapter->getInstance();

            $legacyFields = $db->getFieldNames('tl_dlh_googlemaps');

            $skipFields = [
                'id',
                'tstamp',
                'dateAdded',
            ];

            $fieldsMappings = [
                'useMapTypeControl' => 'addMapTypeControl',
                'useZoomControl' => 'addZoomControl',
                'useRotateControl' => 'addRotateControl',
                'usePanControl' => 'addPanControl',
                'useScaleControl' => 'addScaleControl',
                'useStreetViewControl' => 'addStreetViewControl',
                'useClusterer' => 'addClusterer',
                'mapTypeId' => 'mapType',
            ];

            $fieldsToLower = [
                'mapTypeId',
                'mapTypesAvailable',
                'mapTypeControlStyle',
                'mapTypeControlPos',
                'zoomControlPos',
                'rotateControlPos',
                'streetViewControlPos',
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
                'moreParameter',
            ];

            $messageFields = [
                'staticMapNoscript' => 'The current google map has a static map set. Please set the width and height manually.',
            ];

            foreach ($legacyFields as $legacyField) {
                if (\in_array($legacyField, $skipFields, true)) {
                    continue;
                }

                if (\in_array($legacyField, $removedFields, true)) {
                    if ($legacyMap->{$legacyField} && !$this->skipUnsupportedFieldWarnings) {
                        $this->usernotice('The field "'.$legacyField.'" which is different from NULL in the current google map is not used in Google Maps v3 anymore or not supported by this bundle. Please refer to https://developers.google.com/maps/documentation/javascript for further information.');
                    }

                    continue;
                }

                if (\in_array($legacyField, array_keys($messageFields), true)) {
                    $this->usernotice($messageFields[$legacyField]);
                }

                $newField = $legacyField;
                $legacyValue = $legacyMap->{$legacyField};

                if (\in_array($legacyField, $fieldsToLower, true)) {
                    $legacyValue = strtolower($legacyValue);
                }

                if (\in_array($legacyField, array_keys($fieldsMappings), true)) {
                    $newField = $fieldsMappings[$legacyField];
                }

                if (\in_array($legacyField, array_keys($fieldsMappings), true)) {
                    $newField = $fieldsMappings[$legacyField];
                }

                $map->{$newField} = $legacyValue;
            }

            // positioning
            $map->positioningMode = GoogleMapListener::POSITIONING_MODE_STANDARD;

            if ($legacyMap->geocoderAddress) {
                $map->centerMode = GoogleMapListener::CENTER_MODE_STATIC_ADDRESS;

                $address = $legacyMap->geocoderAddress;

                if ($legacyMap->geocoderCountry) {
                    $address .= ', '.$GLOBALS['TL_LANG']['CNT'][$legacyMap->geocoderCountry];
                }

                $map->centerAddress = $address;
            } else {
                $map->centerMode = GoogleMapListener::CENTER_MODE_COORDINATE;

                if (strpos($legacyMap->center, ',')) {
                    $coordinates = explode(',', $legacyMap->center);

                    if (\is_array($coordinates) && \count($coordinates) > 1) {
                        $map->centerLat = $coordinates[0];
                        $map->centerLng = $coordinates[1];
                    }
                }
            }

            // sizing
            $mapSize = StringUtil::deserialize($legacyMap->mapSize, true);

            if (\count($mapSize) > 2) {
                $map->sizeMode = GoogleMapListener::SIZE_MODE_STATIC;

                $map->width = serialize([
                    'value' => preg_replace('/[^\d]/i', '', $mapSize[0]),
                    'unit' => 'px',
                ]);

                $map->height = serialize([
                    'value' => preg_replace('/[^\d]/i', '', $mapSize[1]),
                    'unit' => 'px',
                ]);
            } else {
                $map->sizeMode = GoogleMapListener::SIZE_MODE_ASPECT_RATIO;
                $map->aspectRatioX = 16;
                $map->aspectRatioY = 9;
            }

            $this->dispatcher->dispatch(new DlhMigrationModifyMapEvent(
                $legacyMap,
                $map,
            ), DlhMigrationModifyMapEvent::NAME);

            if (!$this->dryRun) {
                $map->save();
            }

            $this->mapMapper[$legacyMap->id] = $map->id;

            // tl_dlh_googlemaps_elements -> tl_google_map_overlay
            $this->migrateOverlays($legacyMap, $map);

            $this->io->text('<fg=green>Successfully migrated dlh google map ID '.$legacyMap->id.' ("'.$legacyMap->title.'") to google map ID '.$map->id.'</>');
        }
    }

    protected function migrateOverlays(object $legacyMap, GoogleMapModel $map): void
    {
        $this->io->text('Migrating overlays of dlh google map ID '.$legacyMap->id.' ("'.$legacyMap->title.'") ...');

        $legacyOverlays = $this->connection->fetchAllAssociative('SELECT * FROM tl_dlh_googlemaps_elements WHERE pid=?', [$legacyMap->id]);

        if (\count($legacyOverlays) < 1) {
            $this->usernotice('No existing overlays for map found.');

            return;
        }

        foreach ($legacyOverlays as $legacyOverlay) {
            $legacyOverlay = (object) $legacyOverlay;

            $this->io->text('Migrating dlh google map overlay ID '.$legacyOverlay->id.' ("'.$legacyOverlay->title.'") ...');

            $overlay = new OverlayModel();
            $overlay->tstamp = $overlay->dateAdded = time();
            $overlay->pid = $map->id;

            /** @var Database $dbAdapter */
            $dbAdapter = $this->framework->getAdapter(Database::class);
            $db = $dbAdapter->getInstance();

            $legacyFields = $db->getFieldNames('tl_dlh_googlemaps_elements');

            $skipFields = [
                'id',
                'pid',
                'tstamp',
                'dateAdded',
            ];

            $fieldsMappings = [
                'iconSRC' => 'iconSrc',
                'markerAction' => 'clickEvent',
                'useRouting' => 'addRouting',
                'linkTitle' => 'titleText',
                'infoWindow' => 'infoWindowText',
            ];

            $fieldsToLower = [
                'type',
                'markerType',
                'markerAction',
            ];

            $removedFields = [
                'hasShadow',
                'shadowSRC',
                'shadowSize',
            ];

            $messageFields = [
                'staticMapNoscript' => 'The current google map has a static map set. Please set the width and height manually.',
            ];

            foreach ($legacyFields as $legacyField) {
                if (\in_array($legacyField, $skipFields, true)) {
                    continue;
                }

                if (\in_array($legacyField, $removedFields, true)) {
                    if ($legacyOverlay->{$legacyField} && !$this->skipUnsupportedFieldWarnings) {
                        $this->usernotice('The field "'.$legacyField.'" which is different from NULL in the current google map is not used in Google Maps v3 anymore or not supported by this bundle. Please refer to https://developers.google.com/maps/documentation/javascript for further information.');
                    }

                    continue;
                }

                if (\in_array($legacyField, array_keys($messageFields), true)) {
                    $this->usernotice($messageFields[$legacyField]);
                }

                $newField = $legacyField;
                $legacyValue = $legacyOverlay->{$legacyField};

                if (\in_array($legacyField, $fieldsToLower, true)) {
                    $legacyValue = strtolower($legacyValue);
                }

                if (\in_array($legacyField, array_keys($fieldsMappings), true)) {
                    $newField = $fieldsMappings[$legacyField];
                }

                if (\in_array($legacyField, array_keys($fieldsMappings), true)) {
                    $newField = $fieldsMappings[$legacyField];
                }

                $overlay->{$newField} = $legacyValue;
            }

            if ('none' === $overlay->clickEvent) {
                $overlay->clickEvent = '';
            }

            if ($legacyOverlay->markerShowTitle) {
                $overlay->titleMode = OverlayListener::TITLE_MODE_TITLE_FIELD;
            }

            if ('LINK' === $legacyOverlay->markerAction && $legacyOverlay->linkTitle) {
                $overlay->titleMode = OverlayListener::TITLE_MODE_CUSTOM_TEXT;
            }

            if ('INFO' === $legacyOverlay->markerAction) {
                $overlay->clickEvent = 'infowindow';
            }

            // positioning
            if ($legacyOverlay->singleCoords) {
                $overlay->positioningMode = OverlayListener::POSITIONING_MODE_COORDINATE;

                if (strpos($legacyOverlay->singleCoords, ',')) {
                    $coordinates = explode(',', str_replace(' ', '', $legacyOverlay->singleCoords));

                    if (\is_array($coordinates) && \count($coordinates) > 1) {
                        $overlay->positioningLat = $coordinates[0];
                        $overlay->positioningLng = $coordinates[1];
                    }
                }
            } else {
                $overlay->positioningMode = OverlayListener::POSITIONING_MODE_STATIC_ADDRESS;
                $address = $legacyOverlay->geocoderAddress;

                if ($legacyOverlay->geocoderCountry) {
                    $address .= ', '.$GLOBALS['TL_LANG']['CNT'][$legacyOverlay->geocoderCountry];
                }

                $overlay->positioningAddress = $address;
            }

            // marker type
            switch ($overlay->markerType) {
                case OverlayListener::MARKER_TYPE_ICON:
                    $iconSize = StringUtil::deserialize($legacyOverlay->iconSize, true);

                    $overlay->iconWidth = ['value' => $iconSize[0], 'unit' => 'px'];
                    $overlay->iconHeight = ['value' => $iconSize[1], 'unit' => 'px'];

                    $iconAnchor = StringUtil::deserialize($legacyOverlay->iconAnchor, true);

                    $overlay->iconAnchorX = (int) $iconAnchor[0];
                    $overlay->iconAnchorY = (int) $iconAnchor[1];

                    break;
            }

            // info window sizing
            $infoWindowSize = StringUtil::deserialize($legacyOverlay->infoWindowSize, true);

            if (\count($infoWindowSize) > 2) {
                $overlay->infoWindowWidth = serialize([
                    'value' => $infoWindowSize[0],
                    'unit' => 'px',
                ]);

                $overlay->infoWindowHeight = serialize([
                    'value' => $infoWindowSize[1],
                    'unit' => 'px',
                ]);
            }

            // anchor
            $infoWindowAnchor = StringUtil::deserialize($legacyOverlay->infoWindowAnchor, true);

            if (\count($infoWindowAnchor) > 2) {
                $overlay->infoWindowAnchorX = $infoWindowAnchor[0];
                $overlay->infoWindowAnchorY = $infoWindowAnchor[1];
            }

            $this->dispatcher->dispatch(new DlhMigrationModifyOverlayEvent(
                $legacyOverlay,
                $overlay,
                $legacyMap,
                $map,
            ), DlhMigrationModifyOverlayEvent::NAME);

            if (!$this->dryRun) {
                $overlay->save();
            }

            $this->io->text('<fg=green>Successfully migrated dlh google map overlay ID '.$legacyMap->id.' ("'.$legacyOverlay->title.'") to google map overlay ID '.$overlay->id.'</>');
        }
    }

    protected function migrateContentElements(): void
    {
        $this->io->section('Migrate content elements');
        $contentElements = ContentModel::findByType('dlh_googlemaps');

        if (!$contentElements) {
            $this->io->text('Found no content elements');

            return;
        }

        foreach ($contentElements as $contentElement) {
            if ($this->io->isVerbose()) {
                $this->io->text('Migration content element with ID '.$contentElement->id);
            }
            $contentElement->type = ContentListener::ELEMENT_GOOGLE_MAP;
            $contentElement->googlemaps_skipCss = $contentElement->dlh_googlemap_nocss;

            if ($contentElement->dlh_googlemap_static) {
                $this->usernotice('Static maps in content elements are not supported. Please adjust config (ID '.$contentElement->id.').');
            }

            if ($contentElement->dlh_googlemap_zoom) {
                $this->usernotice('Zoom in content elements is not supported. Please adjust config (ID '.$contentElement->id.').');
            }

            if ($contentElement->dlh_googlemap_size) {
                $this->usernotice('Map size in content elements is not supported. Please adjust config (ID '.$contentElement->id.').');
            }

            if ($contentElement->dlh_googlemap_tabs) {
                $this->usernotice('Tab/accordion setting in content elements is not supported. Please adjust config (ID '.$contentElement->id.').');
            }

            if (isset($this->mapMapper[$contentElement->dlh_googlemap])) {
                $contentElement->googlemaps_map = $this->mapMapper[$contentElement->dlh_googlemap];
            } elseif (!$this->dryRun) {
                $this->usernotice('Map for content element with ID '.$contentElement->id.' could not be found. Please adjust manually.');
            }

            if (!$this->dryRun) {
                $contentElement->save();
            }
        }
        $this->io->text('<fg=green>Finished migration content elements');
    }

    private function migrateFrontendModules(): void
    {
        $this->io->section('Migrate frontend modules');

        $frontendModules = ModuleModel::findByType('dlh_googlemaps');

        if (!$frontendModules) {
            $this->io->text('Found no frontend modules');

            return;
        }

        foreach ($frontendModules as $frontendModule) {
            if ($this->io->isVerbose()) {
                $this->io->text('Migration frontend module '.$frontendModule->name.' with ID '.$frontendModule->id);
            }
            $frontendModule->type = ContentListener::ELEMENT_GOOGLE_MAP;
            $frontendModule->googlemaps_skipCss = $frontendModule->dlh_googlemap_nocss;

            if ($frontendModule->dlh_googlemap_static) {
                $this->usernotice('Static maps in frontend modules are not supported. Please adjust config (ID '.$frontendModule->id.').');
            }

            if ($frontendModule->dlh_googlemap_zoom) {
                $this->usernotice('Zoom in frontend modules is not supported. Please adjust config (ID '.$frontendModule->id.').');
            }

            if ($frontendModule->dlh_googlemap_size) {
                $this->usernotice('Map size in frontend modules is not supported. Please adjust config (ID '.$frontendModule->id.').');
            }

            if ($frontendModule->dlh_googlemap_tabs) {
                $this->usernotice('Tab/accordion setting in frontend modules is not supported. Please adjust config (ID '.$frontendModule->id.').');
            }

            if (isset($this->mapMapper[$frontendModule->dlh_googlemap])) {
                $frontendModule->googlemaps_map = $this->mapMapper[$frontendModule->dlh_googlemap];
            } elseif (!$this->dryRun) {
                $this->usernotice('Map for content element with ID '.$frontendModule->id.' could not be found. Please adjust manually.');
            }

            if (!$this->dryRun) {
                $frontendModule->save();
            }
        }
        $this->io->text('<fg=green>Finished migration of frontend modules');
    }

    private function usernotice(string $message): void
    {
        $this->io->block($message, null, 'userwarning', '    ');
    }
}
