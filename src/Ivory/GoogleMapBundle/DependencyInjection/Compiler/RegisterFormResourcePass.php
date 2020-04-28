<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Ivory\GoogleMapBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class RegisterFormResourcePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasParameter($parameter = 'templating.helper.form.resources')) {
            $container->setParameter(
                $parameter,
                array_merge(
                    ['IvoryGoogleMapBundle:Form'],
                    $container->getParameter($parameter)
                )
            );
        }

        // FIX
//        if ($container->hasParameter($parameter = 'twig.form.resources')) {
//            $container->setParameter(
//                $parameter,
//                array_merge(
//                    ['IvoryGoogleMapBundle:Form:place_autocomplete_widget.html.twig'],
//                    $container->getParameter($parameter)
//                )
//            );
//        }
        // ENDFIX
    }
}
