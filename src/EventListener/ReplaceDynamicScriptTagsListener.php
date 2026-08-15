<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Heimrich & Hannot GmbH.
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use HeimrichHannot\GoogleMapsBundle\Manager\MapManager;

#[AsHook('replaceDynamicScriptTags')]
class ReplaceDynamicScriptTagsListener
{
    /**
     * @var MapManager
     */
    protected $mapManager;

    public function __construct(MapManager $mapManager)
    {
        $this->mapManager = $mapManager;
    }

    public function __invoke($buffer): string
    {
        $mapApi = $this->mapManager->renderApi();

        if (empty($mapApi)) {
            return $buffer;
        }

        // Registered here because maps render inside the body, by which point
        // a TL_JAVASCRIPT entry would come too late for the head.
        if ($this->mapManager->hasGeoJsonLayers()) {
            $GLOBALS['TL_JAVASCRIPT']['huh_google_maps_geojson'] = 'bundles/heimrichhannotgooglemaps/js/google-maps-geojson.js|static';
        }

        // fix the code for the case more than 1 map is on the page and not the first one
        // is clicked and add to body variable
        $GLOBALS['TL_BODY']['huhGoogleMaps'] = preg_replace(
            '@(ivory_google_map_init_requirement\()(ivory_google_map_map_[^,]+)@i',
            'typeof $2 !== \'undefined\' && $1$2', $mapApi);

        return $buffer;
    }
}
