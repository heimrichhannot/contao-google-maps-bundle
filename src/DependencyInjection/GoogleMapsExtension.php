<?php

/*
 * Copyright (c) 2022 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\DependencyInjection;

use HeimrichHannot\GoogleMapsBundle\EventListener\ConsentBridgeListener;
use HeimrichHannot\GoogleMapsBundle\EventListener\PrivacyCenterListener;
use HeimrichHannot\PrivacyCenterBundle\HeimrichHannotPrivacyCenterBundle;
use Hofff\Contao\Consent\Bridge\HofffContaoConsentBridgeBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class GoogleMapsExtension extends Extension
{
    public function getAlias()
    {
        return 'huh_google_maps';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.yml');
        $loader->load('twig.yml');

        if (!class_exists(HofffContaoConsentBridgeBundle::class)) {
            $container->removeDefinition(ConsentBridgeListener::class);
        }

        if (!class_exists(HeimrichHannotPrivacyCenterBundle::class)) {
            $container->removeDefinition(PrivacyCenterListener::class);
        }
    }
}
