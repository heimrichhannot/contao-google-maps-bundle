<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use HeimrichHannot\GoogleMapsBundle\HeimrichHannotGoogleMapsBundle;
use HeimrichHannot\ListBundle\HeimrichHannotContaoListBundle;
use HeimrichHannot\ReaderBundle\HeimrichHannotContaoReaderBundle;
use Hofff\Contao\Consent\Bridge\HofffContaoConsentBridgeBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(HeimrichHannotGoogleMapsBundle::class)->setLoadAfter([
                ContaoCoreBundle::class,
                HeimrichHannotContaoReaderBundle::class,
                HeimrichHannotContaoListBundle::class,
                HofffContaoConsentBridgeBundle::class,
            ]),
        ];
    }
}
