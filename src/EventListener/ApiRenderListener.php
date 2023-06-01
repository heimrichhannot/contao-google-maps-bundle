<?php

/*
 * Copyright (c) 2023 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use HeimrichHannot\PrivacyCenterBundle\Script\ExternalScriptFile;
use Ivory\GoogleMap\Helper\ApiHelper;
use Ivory\GoogleMap\Helper\Event\ApiEvent;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Ivory\GoogleMap\Helper\Formatter\Formatter;
use Ivory\GoogleMap\Helper\Subscriber\ApiJavascriptSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ApiRenderListener implements EventSubscriberInterface
{
    private ApiHelper $apiHelper;

    public function __construct(ApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            ApiEvents::JAVASCRIPT => [
                ['onApiRender', -10],
            ],
        ];
    }

    public function onApiRender(ApiEvent $event)
    {
        $listeners = $this->apiHelper->getEventDispatcher()->getListeners(ApiEvents::JAVASCRIPT);
        $apiSubscriber = null;

        foreach ($listeners as $listener) {
            if ($listener[0] instanceof ApiJavascriptSubscriber) {
                $apiSubscriber = $listener[0];

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
        $source = $apiRenderer->getLoaderRenderer()->renderSource('ivory_google_map_init', $event->getLibraries());
        $script = new ExternalScriptFile('google_maps', $source, []);

        $formatter = new class($formatter->isDebug(), $formatter->getIndentationStep()) extends Formatter {
            public function renderCall($method, array $arguments = [], $semicolon = false, $newLine = false)
            {
                return '';
            }
        };
        $apiRenderer->setFormatter($formatter);

        $apiSubscriber->handle($event, ApiEvents::JAVASCRIPT, $this->apiHelper->getEventDispatcher());
    }
}
