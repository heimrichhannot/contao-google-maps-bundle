<?php

$lang = &$GLOBALS['TL_LANG']['tl_google_map'];

/**
 * Fields
 */
// general
$lang['title'][0] = 'Titel';
$lang['title'][1] = 'Geben Sie hier bitte den Titel ein.';

// visualization
$lang['mapType'][0]      = 'Stil';
$lang['mapType'][1]      = 'Wählen Sie hier den Stil der Karte aus.';
$lang['sizeMode'][0]     = 'Größenmodus';
$lang['sizeMode'][1]     = 'Wählen Sie hier aus, wie die Größe der Karte bestimmt werden soll.';
$lang['aspectRatioX'][0] = 'Horizontal';
$lang['aspectRatioX'][1] = 'Geben Sie hier den horizontalen Anteil des Verhältnisses ein.';
$lang['aspectRatioY'][0] = 'Vertikal';
$lang['aspectRatioY'][1] = 'Geben Sie hier den vertikalen Anteil des Verhältnisses ein.';
$lang['width'][0]        = 'Breite';
$lang['width'][1]        = 'Geben Sie hier die gewünschte Breite ein.';
$lang['height'][0]       = 'Höhe';
$lang['height'][1]       = 'Geben Sie hier die gewünschte Höhe ein.';
$lang['addClusterer'][0] = 'Clustering aktivieren';
$lang['addClusterer'][1] = 'Wählen Sie diese Option, um Clustering für die Karte zu verwenden.';
$lang['styles'][0]       = 'Styles';
$lang['styles'][1]       = 'Geben Sie hier ein Array von JSON-Objekten für das Styles-Attribut ein.';

// behavior
$lang['draggable'][0]              = 'Scrolling aktivieren';
$lang['draggable'][1]              = 'Wählen Sie diese Option, um die Karte scrollbar zu rendern.';
$lang['disableDoubleClickZoom'][0] = 'Zoom per Doppelklick deaktivieren';
$lang['disableDoubleClickZoom'][1] = 'Wählen Sie diese Option, um das Zoomen per Doppelklick zu verhindern.';
$lang['scrollwheel'][0]            = 'Mausrad-Zoom';
$lang['scrollwheel'][1]            = 'Wählen Sie diese Option, das Zoomen per Scrollrad zu erlauben.';
$lang['staticMapNoscript'][0]      = 'Statische Karte als Fallback';
$lang['staticMapNoscript'][1]      = 'Falls kein Javascript verfügbar ist, soll eine (eingeschränkte) statische Ansicht der Karte gezeigt werden.';

// positioning
$lang['zoom'][0] = 'Zoom';
$lang['zoom'][1] = 'Geben Sie hier eine Zoomstufe ein (1: Welt, 5: Landmasse/Kontinent, 10: Stadt, 15: Straßen, 20: Gebäude).';

// controls
$lang['mapTypesAvailable'][0] = 'Verfügbare Kartenansichten';
$lang['mapTypesAvailable'][1] = 'Legen Sie fest, welche Ansichten verfügbar sein sollen.';


// publish
$lang['published'][0] = 'Veröffentlichen';
$lang['published'][1] = 'Wählen Sie diese Option zum Veröffentlichen.';
$lang['start']        = ['Anzeigen ab', 'Google Map erst ab diesem Tag auf der Webseite anzeigen.'];
$lang['stop']         = ['Anzeigen bis', 'Google Map nur bis zu diesem Tag auf der Webseite anzeigen.'];

$lang['reference'] = [
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_ASPECT_RATIO => 'Seitenverhältnis',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_STATIC       => 'Statisch',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_CSS          => 'Selbst durch CSS festlegen'
];

/**
 * Legends
 */
$lang['general_legend']       = 'Allgemeine Einstellungen';
$lang['visualization_legend'] = 'Darstellung';
$lang['behavior_legend']      = 'Verhalten';
$lang['positioning_legend']   = 'Positionierung';
$lang['publish_legend']       = 'Veröffentlichung';

/**
 * Buttons
 */
$lang['new']    = ['Neue Google Map', 'Google Map erstellen'];
$lang['edit']   = ['Google Map bearbeiten', 'Google Map ID %s bearbeiten'];
$lang['copy']   = ['Google Map duplizieren', 'Google Map ID %s duplizieren'];
$lang['delete'] = ['Google Map löschen', 'Google Map ID %s löschen'];
$lang['toggle'] = ['Google Map veröffentlichen', 'Google Map ID %s veröffentlichen/verstecken'];
$lang['show']   = ['Google Map Details', 'Google Map-Details ID %s anzeigen'];
