<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

use HeimrichHannot\GoogleMapsBundle\EventListener\DataContainer\OverlayListener;
use Ivory\GoogleMap\Overlay\Animation;

$lang = &$GLOBALS['TL_LANG']['tl_google_map_overlay'];

/*
 * Fields
 */
$lang['tstamp'][0] = 'Änderungsdatum';
$lang['title'][0] = 'Titel';
$lang['title'][1] = 'Geben Sie hier bitte den Titel ein.';
$lang['type'][0] = 'Typ';
$lang['type'][1] = 'Wählen Sie hier den Typ des Overlays aus.';
$lang['titleMode'][0] = 'Titelmodus';
$lang['titleMode'][1] = 'Wählen Sie hier einen Titelmodus aus.';
$lang['positioningMode'][0] = 'Positionierungsmodus';
$lang['positioningMode'][1] = 'Wählen Sie hier einen Positionierungsmodus aus.';
$lang['positioningLat'][0] = 'Latitude';
$lang['positioningLat'][1] = 'Geben Sie hier eine Latitude ein (Beispiel: 13.733525).';
$lang['positioningLng'][0] = 'Longitude';
$lang['positioningLng'][1] = 'Geben Sie hier eine Longitude ein (Beispiel: 13.733525).';
$lang['positioningAddress'][0] = 'Adresse';
$lang['positioningAddress'][1] = 'Geben Sie hier eine Adresse ein.';
$lang['animation'][0] = 'Animation';
$lang['animation'][1] = 'Wählen Sie hier eine Animation aus.';
$lang['markerType'][0] = 'Marker-Typ';
$lang['markerType'][1] = 'Wählen Sie hier den Typ des Markers aus.';
$lang['iconSrc'][0] = 'Bilddatei';
$lang['iconSrc'][1] = 'Bitte wählen Sie hier die gewünschte Bilddatei aus.';
$lang['iconWidth'][0] = 'Breite';
$lang['iconWidth'][1] = 'Bitte geben Sie hier die Breite des Overlays ein.';
$lang['iconHeight'][0] = 'Höhe';
$lang['iconHeight'][1] = 'Bitte geben Sie hier die Höhe des Overlays ein.';
$lang['iconAnchorX'][0] = 'Bildversatz (X-Achse)';
$lang['iconAnchorX'][1] = 'Geben Sie hier einen Versatz in negative X-Richtung ein (positive Zahl = Verschiebung nach links).';
$lang['iconAnchorY'][0] = 'Bildversatz (Y-Achse)';
$lang['iconAnchorY'][1] = 'Geben Sie hier einen Versatz in negative Y-Richtung ein (positive Zahl = Verschiebung nach unten).';
$lang['clickEvent'][0] = 'Klickereignis';
$lang['clickEvent'][1] = 'Wählen Sie hier aus, was beim Klick auf das Overlay passieren soll.';
$lang['url'][0] = 'Weiterleitungsseite';
$lang['url'][1] = 'Geben Sie hier eine URL ein oder nutzen Sie den Seitenwähler.';
$lang['target'][0] = 'Weiterleitungsseite in neuem Fenster öffnen';
$lang['target'][1] = 'Wählen Sie diese Option, um die Weiterleitungsseite in einem neuen Fenster zu öffnen.';
$lang['linkTitle'][0] = 'Link-Titel überschreiben';
$lang['linkTitle'][1] = 'Geben Sie hier einen Link-Titel ein.';
$lang['infoWindowWidth'][0] = 'Breite';
$lang['infoWindowWidth'][1] = 'Bitte geben Sie hier die Breite der Infoblase ein.';
$lang['infoWindowHeight'][0] = 'Höhe';
$lang['infoWindowHeight'][1] = 'Bitte geben Sie hier die Höhe der Infoblase ein.';
$lang['infoWindowAnchorX'][0] = 'Versatz (X-Achse)';
$lang['infoWindowAnchorX'][1] = 'Geben Sie hier einen Versatz in X-Richtung ein.';
$lang['infoWindowAnchorY'][0] = 'Versatz (Y-Achse)';
$lang['infoWindowAnchorY'][1] = 'Geben Sie hier einen Versatz in Y-Richtung ein.';
$lang['infoWindowText'][0] = 'Text';
$lang['infoWindowText'][1] = 'Geben Sie hier den Text der Infoblase ein.';
$lang['addRouting'][0] = 'Routenplaner hinzufügen';
$lang['addRouting'][1] = 'Wählen Sie diese Option, um der Infoblase einen Routenplaner hinzuzufügen.';
$lang['routingAddress'][0] = 'Ziel für den Routenplaner';
$lang['routingAddress'][1] = 'Geben Sie hier die gewünschte Zieladresse rein.';
$lang['routingTemplate'][0] = 'Template';
$lang['routingTemplate'][1] = 'Wählen Sie hier bei Bedarf ein alternatives Routenplaner-Template aus.';
$lang['zIndex'][0] = 'z-Index (CSS)';
$lang['zIndex'][1] = 'Geben Sie hier bei Bedarf den gewünschten z-Index ein.';
$lang['fillColor'][0] = 'Flächenfarbe';
$lang['fillColor'][1] = 'Geben Sie die Flächenfarbe in Hexadezimal an.';
$lang['pathCoordinates'][0] = 'Eckpunkte';
$lang['pathCoordinates'][1] = 'Fügen Sie die Eckpunkte samt Koordinaten hinzu.';
$lang['strokeWeight'][0] = 'Linienstärke';
$lang['strokeWeight'][1] = 'Die Stärke der Linie in Pixeln';
$lang['strokeColor'][0] = 'Linienfarbe';
$lang['strokeColor'][1] = 'Die Farbe der Linie in Hexadezimal.';
$lang['strokeOpacity'][0] = 'Liniendeckkraft';
$lang['strokeOpacity'][1] = 'Geben Sie die Deckkraft der Linie im Bereich von 0 bis 1 an.';
$lang['fillOpacity'][0] = 'Flächendeckkraft';
$lang['fillOpacity'][1] = 'Geben Sie die Deckkraft der Fläche im Bereich von 0 bis 1 an.';

$lang['published'][0] = 'Veröffentlichen';
$lang['published'][1] = 'Wählen Sie diese Option zum Veröffentlichen.';
$lang['start'][0] = 'Anzeigen ab';
$lang['start'][1] = 'Overlay erst ab diesem Tag auf der Webseite anzeigen.';
$lang['stop'][0] = 'Anzeigen bis';
$lang['stop'][1] = 'Overlay nur bis zu diesem Tag auf der Webseite anzeigen.';

/*
 * Reference
 */
$lang['reference'] = [
    OverlayListener::TITLE_MODE_TITLE_FIELD => 'Titelfeld',
    OverlayListener::TITLE_MODE_CUSTOM_TEXT => 'Eigener Text',
    OverlayListener::POSITIONING_MODE_COORDINATE => 'Koordinate',
    OverlayListener::POSITIONING_MODE_STATIC_ADDRESS => 'Statische Adresse',
    Animation::DROP => 'Drop',
    Animation::BOUNCE => 'Bound',
    OverlayListener::TYPE_MARKER => 'Marker',
    OverlayListener::TYPE_INFO_WINDOW => 'Infoblase',
    OverlayListener::TYPE_POLYLINE => 'Polyline',
    OverlayListener::TYPE_POLYGON => 'Polygon',
    OverlayListener::TYPE_CIRCLE => 'Kreis',
    OverlayListener::TYPE_RECTANGLE => 'Rechteck',
    OverlayListener::TYPE_GROUND_OVERLAY => 'Ground-Overlay',
    OverlayListener::TYPE_KML_LAYER => 'KML-Layer',
    OverlayListener::MARKER_TYPE_SIMPLE => 'Standard',
    OverlayListener::MARKER_TYPE_ICON => 'Individuelles Bild',
    OverlayListener::CLICK_EVENT_LINK => 'Link',
];

/*
 * Legends
 */
$lang['general_legend'] = 'Allgemeine Einstellungen';
$lang['config_legend'] = 'Konfiguration';
$lang['publish_legend'] = 'Veröffentlichung';

/*
 * Buttons
 */
$lang['new'] = ['Neues Overlay', 'Overlay erstellen'];
$lang['edit'] = ['Overlay bearbeiten', 'Overlay ID %s bearbeiten'];
$lang['copy'] = ['Overlay duplizieren', 'Overlay ID %s duplizieren'];
$lang['delete'] = ['Overlay löschen', 'Overlay ID %s löschen'];
$lang['toggle'] = ['Overlay veröffentlichen', 'Overlay ID %s veröffentlichen/verstecken'];
$lang['show'] = ['Overlay Details', 'Overlay-Details ID %s anzeigen'];

/*
 * KML
 */
$lang['kmlUrl'][0] = 'URL';
$lang['kmlUrl'][1] = 'Geben Sie eine Url zu einer .kml-Datei (mit http://) ein. Die URL muss absolut angegeben werden und öffentlich erreichbar sein für Google!';
$lang['kmlClickable'][0] = 'Klickbar';
$lang['kmlClickable'][1] = 'Wenn aktiv, reagiert der Layer auf Mausklicks.';
$lang['kmlPreserveViewport'][0] = 'Anzeigebereich erhalten';
$lang['kmlPreserveViewport'][1] = 'Der Anzeigebereich wird durch den KML Layer nicht verändert.';
$lang['kmlScreenOverlays'][0] = 'Overlays zeigen';
$lang['kmlScreenOverlays'][1] = 'Die Overlays des Layers rendern.';
$lang['kmlSuppressInfowindows'][0] = 'Infoblasen unterdrücken';
$lang['kmlSuppressInfowindows'][1] = 'Unterdrückt die Anzeige von Infoblasen im Layer.';
