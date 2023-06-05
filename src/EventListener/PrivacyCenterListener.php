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
use Ivory\GoogleMap\Helper\Renderer\Utility\SourceRenderer;
use Ivory\GoogleMap\Helper\Subscriber\ApiJavascriptSubscriber;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class PrivacyCenterListener implements ServiceSubscriberInterface, EventSubscriberInterface
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
        if (!$this->privacyCenterManager->isActivatedOnCurrentPage()) {
            return;
        }

        $listeners = $event->getApiHelper()->getEventDispatcher()->getListeners(ApiEvents::JAVASCRIPT);
        $apiSubscriber = null;

        foreach ($listeners as $listener) {
            if ($listener[0] instanceof ApiJavascriptSubscriber) {
                $apiSubscriber = $listener[0];

                break;
            }
        }

        if (!$apiSubscriber) {
            $protectedCode = $this->protectedCodeGenerator->generateProtectedCode($event->getCode(), ['google_maps']);

            if (empty($protectedCode)) {
                $event->setCode($event->getCode());
            } else {
                $event->setCode($protectedCode);
            }

            return;
        }

        $apiRenderer = $apiSubscriber->getApiRenderer();
        $source = $apiRenderer->getLoaderRenderer()->renderSource('ivory_google_map_init', $event->getApiEvent()->getLibraries());
        $script = new ExternalScriptFile('google_maps', $source);
        $this->privacyCenterManager->addProtectedScript($script);

        $sourceRenderer = $apiRenderer->getSourceRenderer();
        $sourceRenderer = new class($sourceRenderer->getFormatter()) extends SourceRenderer {
            public function render($name, $source = null, $variable = null, $newLine = true)
            {
                if ('ivory_google_map_init_source' === $name) {
                    return '';
                }

                return parent::render($name, $source, $variable, $newLine);
            }
        };
        $apiRenderer->setSourceRenderer($sourceRenderer);

        $formatter = $apiRenderer->getFormatter();
        $formatter = new class($formatter->isDebug(), $formatter->getIndentationStep()) extends Formatter {
            public function renderCall($method, array $arguments = [], $semicolon = false, $newLine = false)
            {
                if ('ivory_google_map_init_source' === $method) {
                    return '';
                }

                return parent::renderCall($method, $arguments, $semicolon, $newLine);
            }
        };
        $apiRenderer->setFormatter($formatter);

        $apiSubscriber->handle($event->getApiEvent(), ApiEvents::JAVASCRIPT, $event->getApiHelper()->getEventDispatcher());
        $event->setCode($event->getApiEvent()->getCode());
    }

    /**
     * Adjust the generated map api. Priority -1 ensures it's called after the ReplaceDynamicScriptTagsListener
     * listener.
     *
     * @Hook("replaceDynamicScriptTags", priority=-1)
     */
    public function onReplaceDynamicScriptTags(string $buffer): string
    {
        return $buffer;

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

    public static function getSubscribedEvents()
    {
        return [
            BeforeRenderApiEvent::class => 'onApiRenderEvent',
        ];
    }
}
