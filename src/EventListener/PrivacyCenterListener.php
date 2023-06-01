<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderApiEvent;
use HeimrichHannot\PrivacyCenter\Model\TrackingObjectModel;
use HeimrichHannot\PrivacyCenterBundle\Generator\ProtectedCodeGenerator;
use HeimrichHannot\PrivacyCenterBundle\Manager\PrivacyCenterManager;
use HeimrichHannot\PrivacyCenterBundle\Script\ExternalScriptFile;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Ivory\GoogleMap\Helper\Formatter\Formatter;
use Ivory\GoogleMap\Helper\Renderer\ApiRenderer;
use Ivory\GoogleMap\Helper\Renderer\Html\JavascriptTagRenderer;
use Ivory\GoogleMap\Helper\Subscriber\ApiJavascriptSubscriber;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class PrivacyCenterListener implements ServiceSubscriberInterface
{
    private ContainerInterface $container;
//    private ApiRenderer $apiRenderer;
    private ProtectedCodeGenerator $protectedCodeGenerator;
    private PrivacyCenterManager $privacyCenterManager;

    public function __construct(ContainerInterface $container, ProtectedCodeGenerator $protectedCodeGenerator, PrivacyCenterManager $privacyCenterManager)
    {
        $this->container = $container;
        $this->protectedCodeGenerator = $protectedCodeGenerator;
        $this->privacyCenterManager = $privacyCenterManager;
    }

    public function onApiRenderEvent(BeforeRenderApiEvent $event): void
    {
        return;

        if (!$this->privacyCenterManager->isActivatedOnCurrentPage()) {
            return;
        }

        $code = $event->getApiHelper()->render($event->getMapCollection()->getMaps());
        $listeners = $event->getApiHelper()->getEventDispatcher()->getListeners(ApiEvents::JAVASCRIPT);
        $apiSubscriber = null;

        foreach ($listeners as $listener) {
            if ($listener instanceof ApiJavascriptSubscriber) {
                $apiSubscriber = $listener;

                break;
            }
        }

        if (!$apiSubscriber) {
            $protectedCode = $this->protectedCodeGenerator->generateProtectedCode($code, ['google_maps']);

            if (empty($protectedCode)) {
                $event->setCode($code);
            } else {
                $event->setCode($protectedCode);
            }

            return;
        }

        $apiRenderer = $apiSubscriber->getApiRenderer();
        $formatter = $apiRenderer->getFormatter();
        $apiRenderer->getLoaderRenderer()->renderSource('ivory_google_map_init', $event->getApiHelper());

        $script = new ExternalScriptFile('google_maps', );

        $formatter = (new class() extends Formatter {
        })();

        $this->privacyCenterManager->addProtectedScript($script);

//        $event->setCode($this->javascriptTagRenderer->render($this->apiRenderer->render(
//            $event->getCallbacks(),
//            $event->getRequirements(),
//            $event->getSources(),
//            $event->getLibraries()
//        )));
    }

    /**
     * Adjust the generated map api. Priority -1 ensures it's called after the ReplaceDynamicScriptTagsListener
     * listener.
     *
     * @Hook("replaceDynamicScriptTags", priority=-1)
     */
    public function onReplaceDynamicScriptTags(string $buffer): string
    {
        if (
            !class_exists(ProtectedCodeGenerator::class) ||
            !$this->container->has(ProtectedCodeGenerator::class) ||
            !isset($GLOBALS['TL_BODY']['huhGoogleMaps'])) {
            return $buffer;
        }

        if (TrackingObjectModel::findBy(['localStorageAttribute=?'], ['google_maps'])) {
            $code = $this->container->get(ProtectedCodeGenerator::class)->generateProtectedCode(
                $GLOBALS['TL_BODY']['huhGoogleMaps'],
                ['google_maps'],
            );

            if (!empty($code)) {
                $GLOBALS['TL_BODY']['huhGoogleMaps'] = $code;
            }

            // protect the code
//            $GLOBALS['TL_BODY']['huhGoogleMaps'] = PrivacyCenterManager::getInstance()->addProtectedCode(
//                $GLOBALS['TL_BODY']['huhGoogleMaps'],
//                ['google_maps'],
//                [
//                    'addPoster' => false,
//                ]
//            );
        }

        return $buffer;
    }

    public static function getSubscribedServices()
    {
        return [
            '?'.ProtectedCodeGenerator::class,
        ];
    }
}
