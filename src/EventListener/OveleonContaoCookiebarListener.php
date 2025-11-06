<?php

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\ContentModel;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\DataContainer;
use Contao\ModuleModel;
use HeimrichHannot\GoogleMapsBundle\Controller\ContentElement\GoogleMapsElementController;
use HeimrichHannot\GoogleMapsBundle\Event\BeforeRenderApiEvent;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Ivory\GoogleMap\Helper\Event\ApiEvents;
use Ivory\GoogleMap\Helper\Formatter\Formatter;
use Ivory\GoogleMap\Helper\Renderer\Utility\SourceRenderer;
use Ivory\GoogleMap\Helper\Subscriber\ApiJavascriptSubscriber;
use Oveleon\ContaoCookiebar\Cookie;
use Oveleon\ContaoCookiebar\Cookiebar;
use Oveleon\ContaoCookiebar\Model\CookieModel;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\String\ByteString;
use Twig\Environment;
use Twig\TemplateWrapper;

class OveleonContaoCookiebarListener
{
    public const TYPE = 'huh_google_maps';

    public function __construct(
        private readonly Utils $utils,
        private readonly Environment $twig, private readonly Environment $environment,
    )
    {
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
        if ((string)$dc->activeRecord->type === static::TYPE) {
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

    #[AsEventListener(event: 'kernel.response')]
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $content = $response->getContent();

        if ($request->attributes->has('contentModel')) {
            $contentModel = $request->attributes->get('contentModel');

            if (!$contentModel instanceof ContentModel) {
                $contentModel = ContentModel::findByPk($contentModel);
            }

            // renew $content because using insertTags in modules it could be that contentModel and moduleModel is set
            $content = $this->parseTemplates($contentModel, $content, $request);
            $response->setContent($content);
        }

        if ($request->attributes->has('moduleModel')) {
            $moduleModel = $request->attributes->get('moduleModel');

            if (!$moduleModel instanceof ModuleModel) {
                $moduleModel = ModuleModel::findByPk($moduleModel);
            }

            // renew $content because using insertTags in modules it could be that contentModel and moduleModel is set
            $content = $this->parseTemplates($moduleModel, $content, $request);
            $response->setContent($content);
        }
    }

    private function findConfig(): ?array
    {
        $config = Cookiebar::getConfigByPage($this->utils->request()->getCurrentRootPageModel());
        if (null === $config) {
            return null;
        }

        $cookies = Cookiebar::validateCookies($config);
        if (empty($cookies)) {
            return null;
        }

        return array_find($cookies, function (array $cookie) {
            return $cookie['type'] === static::TYPE;
        });
    }

    private function maskScript(string $script, int $configId, ?string $ident = null): string
    {
        if (!$ident) {
            $ident = 'gmap_load_' . ByteString::fromRandom(4, '0123456789')->toString();
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

    private function maskExternalResource(string $resource, int $configId, string $name)
    {
        $script = <<< SCRIPT
            const script = document.createElement('script');
            script.src = '{$resource}';
            script.type = 'text/javascript';
            document.head.appendChild(script);
            SCRIPT;

        return $this->maskScript($script, $configId, $name);
    }

    private function addScriptToGlobals(string $script): void
    {
        $nonce = ByteString::fromRandom(4, '0123456789')->toString();
        $GLOBALS['TL_BODY']['huhGoogleMaps_' . $nonce] = $script;
    }

    private function parseTemplates(ContentModel|ModuleModel $moduleModel, string|bool $content, Request $request): bool|string
    {
        if ($moduleModel->type !== GoogleMapsElementController::TYPE) {
            return $content;
        }

        $page = $this->utils->request()->getCurrentRootPageModel();
        $config = $this->findConfig();
        if (null === $config) {
            return $content;
        }
        $configModel = CookieModel::findByPk($config['id']);
        if (null === $configModel) {
            return $content;
        }

        $matches = [];
        preg_match_all('/map_canvas_[a-z0-9]+/', $content, $matches);
        if (empty($matches[0])) {
            return $content;
        }
        $canvas = $matches[0][0];
        $template = '@Contao/'.($configModel->blockTemplate ?: 'ccb/element_blocker').'.html.twig';
        $strBlockUrl = $request->getUri();

        $blocker = $this->twig->render('@Contao/oveleon_cookiebar/blocker/default.html.twig', [
            'cookie' => array_merge($configModel->row(), [
                'iframeType' => 'googlemaps',
            ]),
            'redirect' => $strBlockUrl,
            'locale' => $page->language,
        ]);

        $html = preg_replace(
            '/(<div id="'.$canvas.'"[^>]*>)/',
            '$1' . $blocker,
            $content
        );

        return $html;
    }

}