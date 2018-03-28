<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
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
        return [
            BundleConfig::create(HeimrichHannotContaoGoogleMapsBundle::class)->setLoadAfter([ContaoCoreBundle::class, IvoryGoogleMapBundle::class]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionConfig($extensionName, array $extensionConfigs, ContainerBuilder $container)
    {
        return ContainerUtil::mergeConfigFile(
            'huh_GoogleMaps',
            $extensionName,
            $extensionConfigs,
            $container->getParameter('kernel.project_dir').'/vendor/heimrichhannot/contao-GoogleMaps-bundle/src/Resources/config/config.yml'
        );
    }
}
