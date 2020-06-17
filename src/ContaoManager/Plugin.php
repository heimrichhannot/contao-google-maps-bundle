<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ContainerBuilder;
use Contao\ManagerPlugin\Config\ExtensionPluginInterface;
use HeimrichHannot\GoogleMapsBundle\HeimrichHannotContaoGoogleMapsBundle;
use HeimrichHannot\UtilsBundle\Container\ContainerUtil;
use Ivory\GoogleMapBundle\IvoryGoogleMapBundle;

class Plugin implements BundlePluginInterface, ExtensionPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        $loadAfterBundles = [ContaoCoreBundle::class, IvoryGoogleMapBundle::class];

        if (class_exists('HeimrichHannot\ReaderBundle\HeimrichHannotContaoReaderBundle')) {
            $loadAfterBundles[] = 'HeimrichHannot\ReaderBundle\HeimrichHannotContaoReaderBundle';
        }

        if (class_exists('HeimrichHannot\ListBundle\HeimrichHannotContaoListBundle')) {
            $loadAfterBundles[] = 'HeimrichHannot\ListBundle\HeimrichHannotContaoListBundle';
        }

        return [
            BundleConfig::create(IvoryGoogleMapBundle::class)->setLoadAfter([ContaoCoreBundle::class]),
            BundleConfig::create(HeimrichHannotContaoGoogleMapsBundle::class)->setLoadAfter($loadAfterBundles),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
    {
        $extensionConfigs = ContainerUtil::mergeConfigFile(
            'huh_reader',
            $extensionName,
            $extensionConfigs,
            __DIR__.'/../Resources/config/config_reader.yml'
        );

        $extensionConfigs = ContainerUtil::mergeConfigFile(
            'huh_list',
            $extensionName,
            $extensionConfigs,
            __DIR__.'/../Resources/config/config_list.yml'
        );

        return $extensionConfigs;
    }
}
