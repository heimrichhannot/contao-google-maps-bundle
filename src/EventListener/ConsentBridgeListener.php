<?php

declare(strict_types=1);

namespace HeimrichHannot\GoogleMapsBundle\EventListener;

use Contao\Config;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\DataContainer;
use Contao\Template;
use HeimrichHannot\UtilsBundle\Dca\DcaUtil;
use Hofff\Contao\Consent\Bridge\ConsentId;
use Hofff\Contao\Consent\Bridge\ConsentId\ConsentIdParser;
use Hofff\Contao\Consent\Bridge\ConsentToolManager;

final class ConsentBridgeListener
{
    /** @var ConsentToolManager */
    private $consentToolManager;

    /** @var ConsentIdParser */
    private $consentIdParser;
    /**
     * @var DcaUtil
     */
    private $dcaUtil;

    public function __construct(ConsentToolManager $consentManager, ConsentIdParser $consentIdParser, DcaUtil $dcaUtil)
    {
        $this->consentToolManager = $consentManager;
        $this->consentIdParser    = $consentIdParser;
        $this->dcaUtil            = $dcaUtil;
    }

    /**
     * Adjust the data containers for the consent bridge support. High priority required so that the service tags
     * can be applied.
     *
     * @Hook("loadDataContainer", priority=255)
     */
    public function onLoadDataContainer(string $table): void
    {
        if ($this->consentToolManager->consentTools() === []) {
            return;
        }

        switch ($table) {
            case 'tl_page':
                $this->dcaUtil->addOverridableFields(['googlemaps_consentId'], 'tl_settings', 'tl_page');
                $GLOBALS['TL_DCA']['tl_page']['fields']['googlemaps_consentId']['sql'] = [
                    'type'    => 'string',
                    'default' => null,
                    'notnull' => false,
                ];
                break;

            case 'tl_settings':
                $GLOBALS['TL_DCA']['tl_settings']['fields']['googlemaps_consentId'] = [
                    'exclude'   => true,
                    'inputType' => 'select',
                    'eval'      => [
                        'tl_class'           => 'w50',
                        'includeBlankOption' => true,
                        'chosen'             => true,
                    ],
                ];

                break;
        }
    }

    /**
     * @Callback(table="tl_settings", target="config.onload")
     */
    public function onLoadSettings(DataContainer $table): void
    {
        if ($this->consentToolManager->consentTools() === []) {
            return;
        }

        PaletteManipulator::create()
            ->addField('googlemaps_consentId', 'huh_google_maps_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_settings');
    }

    /**
     * @Callback(table="tl_page", target="config.onload")
     */
    public function onLoadPage(DataContainer $table): void
    {
        if ($this->consentToolManager->consentTools() === []) {
            return;
        }

        PaletteManipulator::create()
            ->addField('overrideGooglemaps_consentId', 'hofff_consent_bridge_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('root', 'tl_page')
            ->applyToPalette('rootfallback', 'tl_page');
    }

    /**
     * @Callback(table="tl_settings", target="fields.googlemaps_consentId.options")
     * @Callback(table="tl_page", target="fields.googlemaps_consentId.options")
     */
    public function consentIdOptions(): array
    {
        /** @var array<string, array<string, string>> $options */
        $options = [];

        foreach ($this->consentToolManager->consentTools() as $consentTool) {
            $toolOptions                   = $consentTool->consentIdOptions();
            $options[$consentTool->name()] = [];

            foreach ($toolOptions as $label => $consentId) {
                $options[$consentTool->name()][$consentId->serialize()] = \is_numeric($label)
                    ? $consentId->toString()
                    : $label;
            }
        }

        if (\count($options) === 1) {
            return \current($options);
        }

        return $options;
    }

    /**
     * Adjust the generated map api. Priority -1 ensures it's called after the ReplaceDynamicScriptTagsListener
     * listener.
     *
     * @Hook("replaceDynamicScriptTags", priority=-1)
     */
    public function onReplaceDynamicScriptTags(string $buffer): string
    {
        if (!isset($GLOBALS['TL_BODY']['huhGoogleMaps'])) {
            return $buffer;
        }

        $consentId   = $this->determineConsentId();
        $consentTool = $this->consentToolManager->activeConsentTool();
        if ($consentTool === null || $consentId === null) {
            return $buffer;
        }

        $GLOBALS['TL_BODY']['huhGoogleMaps'] = $consentTool->renderRaw(
            $GLOBALS['TL_BODY']['huhGoogleMaps'],
            $consentId
        );

        return $buffer;
    }

    /**
     * @Hook("parseTemplate")
     */
    public function onParseTemplate(Template $template): void
    {
        if (! \preg_match('#^(ce|mod)_google_map#', $template->getName())) {
            return;
        }

        $consentTool = $this->consentToolManager->activeConsentTool();
        $consentId   = $this->determineConsentId();
        if ($consentTool === null || $consentId === null) {
            return;
        }

        $template->renderedMap = $consentTool->renderContent(
            $template->renderedMap,
            $consentId,
            null,
            'google_map_consent_placeholder'
        );
    }

    private function determineConsentId(): ?ConsentId
    {
        global $objPage;
        static $consentId = null;

        if ($consentId === false) {
            return null;
        }
        if ($consentId !== null) {
            return $consentId;
        }

        if (!isset($objPage)) {
            return null;
        }

        $consentIdAsString = $this->dcaUtil->getOverridableProperty(
            'googlemaps_consentId',
            [
                (object) ['googlemaps_consentId' => Config::get('googlemaps_consentId')],
                ['tl_page', $objPage->rootId ?: $objPage->id],
            ]
        );

        if (!$consentIdAsString) {
            $consentId = false;

            return null;
        }

        $consentId = $this->consentIdParser->parse($consentIdAsString);

        return $consentId;
    }
}
