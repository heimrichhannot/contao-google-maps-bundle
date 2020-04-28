<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace Ivory\GoogleMapBundle;

use Ivory\GoogleMapBundle\DependencyInjection\Compiler\CleanTemplatingPass;
use Ivory\GoogleMapBundle\DependencyInjection\Compiler\RegisterControlRendererPass;
use Ivory\GoogleMapBundle\DependencyInjection\Compiler\RegisterExtendableRendererPass;
use Ivory\GoogleMapBundle\DependencyInjection\Compiler\RegisterFormResourcePass;
use Ivory\GoogleMapBundle\DependencyInjection\Compiler\RegisterHelperListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author GeLo <geloen.eric@gmail.com>
 */
class IvoryGoogleMapBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container
            ->addCompilerPass(new CleanTemplatingPass())
            ->addCompilerPass(new RegisterControlRendererPass())
            ->addCompilerPass(new RegisterExtendableRendererPass())
            ->addCompilerPass(new RegisterFormResourcePass());
        // FIX
//            ->addCompilerPass(new RegisterHelperListenerPass());
        // ENDFIX
    }
}
