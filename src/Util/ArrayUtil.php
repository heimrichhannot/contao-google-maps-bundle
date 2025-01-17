<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\Util;

class ArrayUtil
{
    public static function removePrefix(string $prefix, array $array): array
    {
        $array = array_combine(
            array_map(static fn ($key) => str_starts_with($key, $prefix) ?
            substr($key, \strlen($prefix)) : $key, array_keys($array)),
            $array,
        );

        return $array;
    }
}
