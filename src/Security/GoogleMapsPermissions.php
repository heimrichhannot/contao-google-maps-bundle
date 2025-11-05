<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Security;

final class GoogleMapsPermissions
{
    public const USER_CAN_ACCESS_MODULE = 'contao_user.modules.google_maps';

    public const USER_CAN_EDIT_MAP = 'contao_user.contao_google_maps_bundles';

    public const USER_CAN_CREATE_MAPS = 'contao_user.contao_google_maps_bundlep.create';

    public const USER_CAN_DELETE_MAPS = 'contao_user.contao_google_maps_bundlep.delete';
}
