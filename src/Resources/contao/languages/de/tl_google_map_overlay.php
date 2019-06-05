<?php

$lang = &$GLOBALS['TL_LANG']['tl_google_map_overlay'];

/**
 * Fields
 */
$lang['tstamp'][0]             = 'Änderungsdatum';
$lang['title'][0]              = 'Titel';
$lang['title'][1]              = 'Geben Sie hier bitte den Titel ein.';
$lang['type'][0]               = 'Typ';
$lang['type'][1]               = 'Wählen Sie hier den Typ des Overlays aus.';
$lang['titleMode'][0]          = 'Titelmodus';
$lang['titleMode'][1]          = 'Wählen Sie hier einen Titelmodus aus.';
$lang['positioningMode'][0]    = 'Positionierungsmodus';
$lang['positioningMode'][1]    = 'Wählen Sie hier einen Positionierungsmodus aus.';
$lang['positioningLat'][0]     = 'Latitude';
$lang['positioningLat'][1]     = 'Geben Sie hier eine Latitude ein (Beispiel: 13.733525).';
$lang['positioningLng'][0]     = 'Longitude';
$lang['positioningLng'][1]     = 'Geben Sie hier eine Longitude ein (Beispiel: 13.733525).';
$lang['positioningAddress'][0] = 'Adresse';
$lang['positioningAddress'][1] = 'Geben Sie hier eine Adresse ein.';
$lang['animation'][0]          = 'Animation';
$lang['animation'][1]          = 'Wählen Sie hier eine Animation aus.';
$lang['markerType'][0]         = 'Marker-Typ';
$lang['markerType'][1]         = 'Wählen Sie hier den Typ des Markers aus.';
$lang['iconSrc'][0]            = 'Bilddatei';
$lang['iconSrc'][1]            = 'Bitte wählen Sie hier die gewünschte Bilddatei aus.';
$lang['iconWidth'][0]          = 'Breite';
$lang['iconWidth'][1]          = 'Bitte geben Sie hier die Breite des Overlays ein.';
$lang['iconHeight'][0]         = 'Höhe';
$lang['iconHeight'][1]         = 'Bitte geben Sie hier die Höhe des Overlays ein.';
$lang['iconAnchorX'][0]        = 'Bildversatz (X-Achse)';
$lang['iconAnchorX'][1]        = 'Geben Sie hier einen Versatz in negative X-Richtung ein (positive Zahl = Verschiebung nach links).';
$lang['iconAnchorY'][0]        = 'Bildversatz (Y-Achse)';
$lang['iconAnchorY'][1]        = 'Geben Sie hier einen Versatz in negative Y-Richtung ein (positive Zahl = Verschiebung nach unten).';
$lang['clickEvent'][0]         = 'Klickereignis';
$lang['clickEvent'][1]         = 'Wählen Sie hier aus, was beim Klick auf das Overlay passieren soll.';
$lang['url'][0]                = 'Weiterleitungsseite';
$lang['url'][1]                = 'Geben Sie hier eine URL ein oder nutzen Sie den Seitenwähler.';
$lang['target'][0]             = 'Weiterleitungsseite in neuem Fenster öffnen';
$lang['target'][1]             = 'Wählen Sie diese Option, um die Weiterleitungsseite in einem neuen Fenster zu öffnen.';
$lang['linkTitle'][0]          = 'Link-Titel überschreiben';
$lang['linkTitle'][1]          = 'Geben Sie hier einen Link-Titel ein.';
$lang['infoWindowWidth'][0]    = 'Breite';
$lang['infoWindowWidth'][1]    = 'Bitte geben Sie hier die Breite der Infoblase ein.';
$lang['infoWindowHeight'][0]   = 'Höhe';
$lang['infoWindowHeight'][1]   = 'Bitte geben Sie hier die Höhe der Infoblase ein.';
$lang['infoWindowAnchorX'][0]  = 'Versatz (X-Achse)';
$lang['infoWindowAnchorX'][1]  = 'Geben Sie hier einen Versatz in X-Richtung ein.';
$lang['infoWindowAnchorY'][0]  = 'Versatz (Y-Achse)';
$lang['infoWindowAnchorY'][1]  = 'Geben Sie hier einen Versatz in Y-Richtung ein.';
$lang['infoWindowAutoOpen'][0] = 'Infoblase automatisch öffnen';
$lang['infoWindowAutoOpen'][1] = 'Wählen Sie diese Option, wenn die Infoblase automatisch geöffnet werden soll.';
$lang['infoWindowText'][0]     = 'Text';
$lang['infoWindowText'][1]     = 'Geben Sie hier den Text der Infoblase ein.';
$lang['addRouting'][0]         = 'Routenplaner hinzufügen';
$lang['addRouting'][1]         = 'Wählen Sie diese Option, um der Infoblase einen Routenplaner hinzuzufügen.';
$lang['routingAddress'][0]     = 'Ziel für den Routenplaner';
$lang['routingAddress'][1]     = 'Geben Sie hier die gewünschte Zieladresse rein.';
$lang['routingTemplate'][0]    = 'Template';
$lang['routingTemplate'][1]    = 'Wählen Sie hier bei Bedarf ein alternatives Routenplaner-Template aus.';
$lang['zIndex'][0]             = 'z-Index (CSS)';
$lang['zIndex'][1]             = 'Geben Sie hier bei Bedarf den gewünschten z-Index ein.';

$lang['published'][0] = 'Veröffentlichen';
$lang['published'][1] = 'Wählen Sie diese Option zum Veröffentlichen.';
$lang['start'][0]     = 'Anzeigen ab';
$lang['start'][1]     = 'Overlay erst ab diesem Tag auf der Webseite anzeigen.';
$lang['stop'][0]      = 'Anzeigen bis';
$lang['stop'][1]      = 'Overlay nur bis zu diesem Tag auf der Webseite anzeigen.';

/**
 * Reference
 */
$lang['reference'] = [
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TITLE_MODE_TITLE_FIELD          => 'Titelfeld',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TITLE_MODE_CUSTOM_TEXT          => 'Eigener Text',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::POSITIONING_MODE_COORDINATE     => 'Koordinate',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::POSITIONING_MODE_STATIC_ADDRESS => 'Statische Adresse',
    \Ivory\GoogleMap\Overlay\Animation::DROP                                          => 'Drop',
    \Ivory\GoogleMap\Overlay\Animation::BOUNCE                                        => 'Bound',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TITLE_MODE_CUSTOM_TEXT          => 'Eigener Text',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_MARKER                     => 'Marker',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_INFO_WINDOW                => 'Infoblase',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_POLYLINE                   => 'Polyline',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_POLYGON                    => 'Polygon',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_CIRCLE                     => 'Kreis',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_RECTANGLE                  => 'Rechteck',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::TYPE_GROUND_OVERLAY             => 'Ground-Overlay',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::MARKER_TYPE_SIMPLE              => 'Standard',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::MARKER_TYPE_ICON                => 'Individuelles Bild',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::CLICK_EVENT_LINK                => 'Link',
    \HeimrichHannot\GoogleMapsBundle\DataContainer\Overlay::CLICK_EVENT_INFO_WINDOW         => 'Infoblase'
];

/**
 * Legends
 */
$lang['general_legend'] = 'Allgemeine Einstellungen';
$lang['config_legend']  = 'Konfiguration';
$lang['publish_legend'] = 'Veröffentlichung';

/**
 * Buttons
 */
$lang['new']    = ['Neues Overlay', 'Overlay erstellen'];
$lang['edit']   = ['Overlay bearbeiten', 'Overlay ID %s bearbeiten'];
$lang['copy']   = ['Overlay duplizieren', 'Overlay ID %s duplizieren'];
$lang['delete'] = ['Overlay löschen', 'Overlay ID %s löschen'];
$lang['toggle'] = ['Overlay veröffentlichen', 'Overlay ID %s veröffentlichen/verstecken'];
$lang['show']   = ['Overlay Details', 'Overlay-Details ID %s anzeigen'];
