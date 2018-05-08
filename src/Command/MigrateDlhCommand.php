<?php

namespace HeimrichHannot\StylesheetManagerBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Contao\CoreBundle\Framework\FrameworkAwareTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateDlhCommand extends AbstractLockedCommand
{
    use FrameworkAwareTrait;

    /**
     * @var SymfonyStyle
     */
    private $io;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('google-maps:migrate-dlh')->setDescription('Migrates existing Maps created using delahaye/dlh_googlemaps.');
    }

    /**
     * {@inheritdoc}
     */
    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {
        $this->io      = new SymfonyStyle($input, $output);
        $this->rootDir = $this->getContainer()->getParameter('kernel.project_dir');

        // tl_google_map

        // tlsettings
        // dlh_googlemaps_apikey -> googlemaps_apiKey

        // tl_page
        // dlh_googlemaps_apikey -> overrideGooglemaps_apiKey

        // useMapTypeControl -> add
        // useZoomControl
        // useRotateControl
        // usePanControl
        // useScaleControl
        // useStreetViewControl
        // useClusterer -> default
        // clustererImg -> file now
        // mapTypeId -> mapType

        // tolower:
        // mapTypesAvailable
        // mapTypeControlStyle
        // mapTypeControlPos
        // zoomControlPos
        // rotateControlPos
        // panControlPos
        // streetViewControlPos

        // useOverviewMapControl & overviewMapControlOpened -> deprecated -> ignored
        // zoomControlStyle, scaleControlPos deprecated -> ignored

        // parameter, moreParameter -> ignored

        // tl_google_map_overlay
        // type -> tolower
        // parameter -> ignored
        // markerType == ICON => isImageMarker
        // iconSRC -> iconSrc, iconAnchor -> siehe unten
        // Markertype => tolower
        // shadow raus -> hasShadow
        // markerAction -> clickEvent
        // useRouting -> addRouting
        // positioning
        // markerShowTitle -> ja -> titlemode = title field, titlemode=customtext wenn linktitle
        // infoWindow -> infoWindowText

        return 0;
    }
}