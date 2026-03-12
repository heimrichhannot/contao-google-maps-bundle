<?php

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\DataContainer;
use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderApiEvent;
use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderMapEvent;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Ivory\GoogleMap\Helper\Formatter\Formatter;
use Ivory\GoogleMap\Helper\Renderer\Utility\SourceRenderer;
use Ivory\GoogleMap\Helper\Subscriber\ApiJavascriptSubscriber;
use Ivory\GoogleMap\Map;
use Oveleon\ContaoCookiebar\Cookie;
use Oveleon\ContaoCookiebar\Cookiebar;
use Oveleon\ContaoCookiebar\Model\CookieModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\ByteString;
use Twig\Environment;

class OveleonContaoCookiebarListener
{
    public const TYPE = 'huh_google_maps';

    public function __construct(
        private readonly Utils $utils,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
    ) {
    }

    #[AsHook('loadDataContainer')]
    public function onLoadDataContainer(string $table): void
    {
        if ('tl_cookie' !== $table || !class_exists(Cookie::class)) {
            return;
        }

        $dca = &$GLOBALS['TL_DCA']['tl_cookie'];
        $dca['fields']['type']['options'][] = static::TYPE;
        $dca['palettes'][self::TYPE] = $dca['palettes']['default'];
        //        PaletteManipulator::create()
        //            ->addField('blockTemplate', 'description_legend', PaletteManipulator::POSITION_APPEND)
        //            ->applyToPalette(self::TYPE, 'tl_cookie');
    }

    #[AsCallback(table: 'tl_cookie', target: 'fields.token.load')]
    public function requireField(mixed $varValue, DataContainer $dc): mixed
    {
        if ((string) $dc->activeRecord->type === static::TYPE) {
            $GLOBALS['TL_DCA']['tl_cookie']['fields'][$dc->field]['eval']['mandatory'] = false;
        }

        return $varValue;
    }

    #[AsEventListener]
    public function onBeforeRenderApiEvent(BeforeRenderApiEvent $event): void
    {
        $config = $this->findConfig();
        if (null === $config) {
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
            $event->setCode($this->maskScript($event->getCode(), $config['id']));

            return;
        }

        $apiRenderer = $apiSubscriber->getApiRenderer();
        $source = $apiRenderer->getLoaderRenderer()->renderSource('ivory_google_map_init', $event->getApiEvent()->getLibraries());

        $this->addScriptToGlobals($this->maskExternalResource($source, $config['id'], 'gmap_library'));

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

    #[AsEventListener]
    public function onBeforeRenderMapEvent(BeforeRenderMapEvent $event): void
    {
        $config = $this->findConfig();
        if (null === $config) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $configModel = CookieModel::findByPk($config['id']);
        if (null === $configModel) {
            return;
        }

        $event->templateData['mapHtml'] = $this->parseHtml(
            $event->templateData['mapHtml'],
            $event->map,
            $request,
            $configModel,
        );
    }

    private function findConfig(): ?array
    {
        if (!class_exists(Cookiebar::class)) {
            return null;
        }

        $rootPage = $this->utils->request()->getCurrentRootPageModel();
        if (null === $rootPage) {
            return null;
        }

        $config = Cookiebar::getConfigByPage($rootPage);
        if (null === $config) {
            return null;
        }

        $cookies = Cookiebar::validateCookies($config);
        if (empty($cookies)) {
            return null;
        }

        return array_find($cookies, fn (array $cookie) => $cookie['type'] === static::TYPE);
    }

    private function maskScript(string $script, int $configId, ?string $ident = null): string
    {
        if (!$ident) {
            $ident = 'gmap_load_'.ByteString::fromRandom(4, '0123456789')->toString();
        }

        return <<< SCRIPT
            <script type="text/javascript">
            function {$ident}() {
                {$script}
            }
            document.addEventListener("DOMContentLoaded", function() {
                cookiebar.addModule({$configId}, {$ident})
            });
            </script>
            SCRIPT;
    }

    private function maskExternalResource(string $resource, int $configId, string $name): string
    {
        $script = <<< SCRIPT
            const script = document.createElement('script');
            script.src = '{$resource}';
            script.type = 'text/javascript';
            script.async = true;
            document.head.appendChild(script);
            SCRIPT;

        return $this->maskScript($script, $configId, $name);
    }

    private function addScriptToGlobals(string $script): void
    {
        $nonce = ByteString::fromRandom(4, '0123456789')->toString();
        $GLOBALS['TL_BODY']['huhGoogleMaps_'.$nonce] = $script;
    }

    private function parseHtml(string $content, Map $map, Request $request, CookieModel $configModel): string|array|bool|null
    {
        if (!str_contains($content, $map->getHtmlId())) {
            return null;
        }

        $template = '@Contao/google_maps/oveleon_cookiebar/blocker.html.twig';

        // support legacy path for bc
        $legacyTemplateName = '@Contao/oveleon_cookiebar/blocker/default.html.twig';
        if (
            $this->twig->getLoader()->exists($legacyTemplateName)
            && !str_contains(
                $this->twig->getLoader()->getSourceContext($legacyTemplateName)->getPath(),
                'heimrichhannot/contao-google-maps-bundle'
            )
        ) {
            trigger_deprecation(
                'heimrichhannot/contao-google-maps-bundle',
                '3.0.0-beta4',
                'The template path %s is deprecated and will not be supported anymore in version 4.0. Use %s instead.',
                $legacyTemplateName,
                $template,
            );
            $template = $legacyTemplateName;
        }

        $blocker = $this->twig->render($template, [
            'cookie' => array_merge($configModel->row(), [
                'iframeType' => 'googlemaps',
            ]),
            'redirect' => $request->getUri(),
            'locale' => $request->getLocale(),
        ]);

        return preg_replace(
            '/(<div id="'.$map->getHtmlId().'"[^>]*>)/',
            '$1'.$blocker,
            $content
        );
    }
}
