<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderApiEvent;
use HeimrichHannot\PrivacyCenterBundle\Generator\ProtectedCodeGenerator;
use HeimrichHannot\PrivacyCenterBundle\Manager\PrivacyCenterManager;
use HeimrichHannot\PrivacyCenterBundle\Script\ExternalScriptFile;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Ivory\GoogleMap\Helper\Formatter\Formatter;
use Ivory\GoogleMap\Helper\Renderer\Utility\SourceRenderer;
use Ivory\GoogleMap\Helper\Subscriber\ApiJavascriptSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PrivacyCenterListener implements EventSubscriberInterface
{
    private ProtectedCodeGenerator $protectedCodeGenerator;
    private PrivacyCenterManager $privacyCenterManager;

    public function __construct(ProtectedCodeGenerator $protectedCodeGenerator, PrivacyCenterManager $privacyCenterManager)
    {
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

    public static function getSubscribedEvents()
    {
        return [
            BeforeRenderApiEvent::class => 'onApiRenderEvent',
        ];
    }
}
