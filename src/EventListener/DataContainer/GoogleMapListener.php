<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer;

use Contao\BackendUser;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use HeimrichHannot\GoogleMapsBundle\Model\GoogleMapModel;
use Ivory\GoogleMap\Control\ControlPosition;
use Ivory\GoogleMap\Control\MapTypeControlStyle;
use Ivory\GoogleMap\MapTypeId;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class GoogleMapListener
{
    const SIZE_MODE_ASPECT_RATIO = 'aspect_ratio';

    const SIZE_MODE_STATIC = 'static';

    const SIZE_MODE_CSS = 'css';

    const SIZE_MODES = [
        self::SIZE_MODE_ASPECT_RATIO,
        self::SIZE_MODE_STATIC,
        self::SIZE_MODE_CSS,
    ];

    const MAP_TYPE_BASE = 'base';

    const MAP_TYPE_RESPONSIVE = 'responsive';

    const TYPES = [
        MapTypeId::ROADMAP,
        MapTypeId::SATELLITE,
        MapTypeId::TERRAIN,
        MapTypeId::HYBRID,
    ];

    const POSITIONING_MODE_STANDARD = 'standard';

    const POSITIONING_MODE_BOUND = 'bound';

    const POSITIONING_MODES = [
        self::POSITIONING_MODE_STANDARD,
        self::POSITIONING_MODE_BOUND,
    ];

    const BOUND_MODE_COORDINATES = 'coordinates';

    const BOUND_MODE_AUTOMATIC = 'automatic';

    const BOUND_MODES = [
        self::BOUND_MODE_COORDINATES,
        self::BOUND_MODE_AUTOMATIC,
    ];

    const CENTER_MODE_COORDINATE = 'coordinate';

    const CENTER_MODE_STATIC_ADDRESS = 'static_address';

    const CENTER_MODE_EXTERNAL = 'external';

    const CENTER_MODES = [
        self::CENTER_MODE_COORDINATE,
        self::CENTER_MODE_STATIC_ADDRESS,
        self::CENTER_MODE_EXTERNAL,
    ];

    const POSITIONS = [
        ControlPosition::TOP_LEFT,
        ControlPosition::TOP_CENTER,
        ControlPosition::TOP_RIGHT,
        ControlPosition::LEFT_TOP,
        'c1',
        ControlPosition::RIGHT_TOP,
        ControlPosition::LEFT_CENTER,
        'c2',
        ControlPosition::RIGHT_CENTER,
        ControlPosition::LEFT_BOTTOM,
        'c3',
        ControlPosition::RIGHT_BOTTOM,
        ControlPosition::BOTTOM_LEFT,
        ControlPosition::BOTTOM_CENTER,
        ControlPosition::BOTTOM_RIGHT,
    ];

    const MAP_CONTROL_STYLES = [
        MapTypeControlStyle::DEFAULT_,
        MapTypeControlStyle::DROPDOWN_MENU,
        MapTypeControlStyle::HORIZONTAL_BAR,
    ];

    protected ContaoFramework $framework;

    protected Connection $connection;

    protected Security $security;

    protected RequestStack $requestStack;

    public function __construct(ContaoFramework $framework, Connection $connection, Security $security, RequestStack $requestStack)
    {
        $this->framework = $framework;
        $this->connection = $connection;
        $this->security = $security;
        $this->requestStack = $requestStack;
    }

    public function getResponsiveMaps(DataContainer $dc)
    {
        $options = [];

        /** @var GoogleMapModel $configAdapter */
        $configAdapter = $this->framework->getAdapter(GoogleMapModel::class);

        if (null === ($configs = $configAdapter->findBy(['type = ?'], 'responsive'))) {
            return $options;
        }

        return $configs->fetchEach('title');
    }

    public function getMapChoices()
    {
        /** @var GoogleMapModel $configAdapter */
        $configAdapter = $this->framework->getAdapter(GoogleMapModel::class);

        if (null === ($configs = $configAdapter->findAll())) {
            return [];
        }

        return $configs->fetchEach('title');
    }

    #[AsCallback(table: 'tl_google_map', target: 'config.onload')]
    public function adjustDca(): void
    {
        $user = $this->security->getUser();

        if ($user instanceof BackendUser && $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        // Set root IDs
        if (empty($user->contao_google_maps_bundles) || !\is_array($user->contao_google_maps_bundles)) {
            $root = [0];
        } else {
            $root = $user->contao_google_maps_bundles;
        }

        $GLOBALS['TL_DCA']['tl_google_map']['list']['sorting']['root'] = $root;
    }

    #[AsCallback(table: 'tl_google_map', target: 'config.oncreate')]
    #[AsCallback(table: 'tl_google_map', target: 'config.oncopy')]
    public function adjustPermissions(string|int $insertId): void
    {
        // The oncreate_callback passes $insertId as second argument
        if (4 === \func_num_args()) {
            $insertId = func_get_arg(1);
        }

        $user = $this->security->getUser();

        if ($user instanceof BackendUser && $this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        // Set root IDs
        if (empty($user->contao_google_maps_bundles) || !\is_array($user->contao_google_maps_bundles)) {
            $root = [0];
        } else {
            $root = $user->contao_google_maps_bundles;
        }

        // The map is enabled already
        if (\in_array($insertId, $root, true)) {
            return;
        }

        $objSessionBag = $this->requestStack->getSession()->getBag('contao_backend');
        $newRecords = $objSessionBag->get('new_records');

        if (\is_array($newRecords['tl_google_map']) && \in_array($insertId, $newRecords['tl_google_map'], true)) {
            // Add the permissions on group level
            if ('custom' !== $user->inherit) {
                $qb = $this->connection->createQueryBuilder()
                    ->select('id, contao_google_maps_bundles, contao_google_maps_bundlep')
                    ->from('tl_user_group')
                ;
                $qb->where($qb->expr()->in('id', $user->groups));
                $results = $qb->executeQuery();

                foreach ($results->fetchAllAssociative() as $groupPermissions) {
                    $mapPermissions = StringUtil::deserialize($groupPermissions['contao_google_maps_bundlep']);

                    if (\is_array($mapPermissions) && \in_array('create', $mapPermissions, true)) {
                        $mapsAllowed = StringUtil::deserialize($groupPermissions['contao_google_maps_bundles'], true);
                        $mapsAllowed[] = $insertId;

                        $this->connection->update('tl_user_group', ['contao_google_maps_bundles' => serialize($mapsAllowed)], ['id' => $groupPermissions['id']]);
                    }
                }
            }

            // Add the permissions on user level
            if ('group' !== $user->inherit) {
                $qb = $this->connection->createQueryBuilder()
                    ->select('id, contao_google_maps_bundles, contao_google_maps_bundlep')
                    ->from('tl_user')
                ;
                $qb->where($qb->expr()->eq('id', $user->id));
                $userPermissions = $qb->executeQuery()->fetchAssociative();

                $mapPermissions = StringUtil::deserialize($userPermissions['contao_google_maps_bundlep']);

                if (\is_array($mapPermissions) && \in_array('create', $mapPermissions, true)) {
                    $mapsAllowed = StringUtil::deserialize($userPermissions['contao_google_maps_bundles'], true);
                    $mapsAllowed[] = $insertId;

                    $this->connection->update('tl_user', ['contao_google_maps_bundles' => serialize($mapsAllowed)], ['id' => $userPermissions['id']]);
                }
            }

            // Add the new element to the user object
            $root[] = $insertId;
            $user->contao_google_maps_bundles = $root;
        }
    }
}
