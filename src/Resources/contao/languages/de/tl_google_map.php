<?php

$lang = &$GLOBALS['TL_LANG']['tl_google_map'];

/**
 * Fields
 */
// general
$lang['title'][0]  = 'Titel';
$lang['title'][1]  = 'Geben Sie hier bitte den Titel ein.';
$lang['htmlId'][0] = 'Abweichende HTML-ID';
$lang['htmlId'][1] = 'Geben Sie hier bei Bedarf eine vom Standard anweichende HTML-ID ein.';

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
$lang['clustererImg'][0] = 'Bilderverzeichnis';
$lang['clustererImg'][1] = 'Geben Sie hier bei Bedarf ein Verzeichnis, in dem sich die individuellen Bilder für das Clustering befinden (-> siehe <a href="https://github.com/googlemaps/js-marker-clusterer#usage">https://github.com/googlemaps/js-marker-clusterer#usage</a>).';
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
$lang['staticMapWidth'][0]         = 'Breite';
$lang['staticMapWidth'][1]         = 'Geben Sie hier die Breite der zu beziehenden statischen Karte in Pixel an.';
$lang['staticMapHeight'][0]        = 'Höhe';
$lang['staticMapHeight'][1]        = 'Geben Sie hier die Höhe der zu beziehenden statischen Karte in Pixel an.';

// positioning
$lang['zoom'][0]            = 'Zoom';
$lang['zoom'][1]            = 'Geben Sie hier eine Zoomstufe ein (1: Welt, 5: Landmasse/Kontinent, 10: Stadt, 15: Straßen, 20: Gebäude).';
$lang['positioningMode'][0] = 'Positionierungsmodus';
$lang['positioningMode'][1] = 'Wählen Sie hier einen Positionierungsmodus aus.';
$lang['centerMode'][0]      = 'Zentrierungsmodus';
$lang['centerMode'][1]      = 'Wählen Sie hier einen Zentrierungsmodus aus.';
$lang['centerLat'][0]       = 'Latitude';
$lang['centerLat'][1]       = 'Geben Sie hier eine Latitude ein (Beispiel: 13.733525).';
$lang['centerLng'][0]       = 'Longitude';
$lang['centerLng'][1]       = 'Geben Sie hier eine Longitude ein (Beispiel: 13.733525).';
$lang['centerAddress'][0]   = 'Adresse';
$lang['centerAddress'][1]   = 'Geben Sie hier eine Adresse ein.';
$lang['boundMode'][0]       = 'Bound-Modus';
$lang['boundMode'][1]       = 'Wählen Sie hier einen Modus aus.';

// controls
$lang['mapTypesAvailable'][0]    = 'Verfügbare Kartenansichten';
$lang['mapTypesAvailable'][1]    = 'Legen Sie fest, welche Ansichten verfügbar sein sollen.';
$lang['addMapTypeControl'][0]    = 'Bedienelement "Kartentyp" hinzufügen';
$lang['addMapTypeControl'][1]    = 'Wählen Sie diese Option, um das entsprechende Bedienelement einzubinden.';
$lang['controlStyle'][0]         = 'Stil';
$lang['controlStyle'][1]         = 'Wählen Sie hier einen Stil für das Bedienelement aus.';
$lang['controlPos'][0]           = 'Position';
$lang['controlPos'][1]           = 'Wählen Sie hier eine Position für das Bedienelement aus.';
$lang['addZoomControl'][0]       = 'Bedienelement "Zoom" hinzufügen';
$lang['addZoomControl'][1]       = 'Wählen Sie diese Option, um das entsprechende Bedienelement einzubinden.';
$lang['addRotateControl'][0]     = 'Bedienelement "Rotation" hinzufügen (nur bei verfügbaren 45°-Bildern)';
$lang['addRotateControl'][1]     = 'Wählen Sie diese Option, um das entsprechende Bedienelement einzubinden.';
$lang['addFullscreenControl'][0] = 'Bedienelement "Vollbild" hinzufügen';
$lang['addFullscreenControl'][1] = 'Wählen Sie diese Option, um das entsprechende Bedienelement einzubinden.';
$lang['addScaleControl'][0]      = 'Bedienelement "Maßstab" hinzufügen';
$lang['addScaleControl'][1]      = 'Wählen Sie diese Option, um das entsprechende Bedienelement einzubinden.';
$lang['addStreetViewControl'][0] = 'Bedienelement "StreetView" hinzufügen';
$lang['addStreetViewControl'][1] = 'Wählen Sie diese Option, um das entsprechende Bedienelement einzubinden.';

// language
$lang['overrideLanguage'] = ['Sprache überschreiben','Wählen Sie diese Option wenn Sie die Sprache in der die Karte ausgegeben wird anpassen möchten. Standardmäßig wird die Sprache genutzt, die in der Seitenkonfiguration festgelegt wurde.'];
$lang['language'] = ['Sprache','Bitte geben Sie die Sprache der Seite gemäß des ISO-639-1 Standards ein (z.B. "de" für Deutsch oder "de-CH"; für Schweizerdeutsch).'];

// template
$lang['template'][0] = 'Template';
$lang['template'][1] = 'Wählen Sie hier bei Bedarf ein alternatives Template für die Karte aus.';

// publish
$lang['published'][0] = 'Veröffentlichen';
$lang['published'][1] = 'Wählen Sie diese Option zum Veröffentlichen.';
$lang['start']        = ['Anzeigen ab', 'Google Map erst ab diesem Tag auf der Webseite anzeigen.'];
$lang['stop']         = ['Anzeigen bis', 'Google Map nur bis zu diesem Tag auf der Webseite anzeigen.'];

$lang['reference'] = [
    \Ivory\GoogleMap\MapTypeId::ROADMAP                                            => 'Roadmap',
    \Ivory\GoogleMap\MapTypeId::SATELLITE                                          => 'Satellit',
    \Ivory\GoogleMap\MapTypeId::TERRAIN                                            => 'Terrain',
    \Ivory\GoogleMap\MapTypeId::HYBRID                                             => 'Hybrid',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_ASPECT_RATIO     => 'Seitenverhältnis',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_STATIC           => 'Statisch',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::SIZE_MODE_CSS              => 'CSS',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::POSITIONING_MODE_STANDARD  => 'Standard',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::POSITIONING_MODE_BOUND     => 'Rahmen (Bounding)',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::BOUND_MODE_AUTOMATIC       => 'Automatisch',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::BOUND_MODE_COORDINATES     => 'Koordinaten',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::CENTER_MODE_COORDINATE     => 'Koordinate',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::CENTER_MODE_STATIC_ADDRESS => 'Adresse',
    \HeimrichHannot\GoogleMapsBundle\Backend\GoogleMap::CENTER_MODE_EXTERNAL       => 'Wird extern festgelegt (bspw. durch ein anderes Modul)',
    \Ivory\GoogleMap\Control\MapTypeControlStyle::DEFAULT_                         => 'Standard',
    \Ivory\GoogleMap\Control\MapTypeControlStyle::DROPDOWN_MENU                    => 'Dropdown-Menü',
    \Ivory\GoogleMap\Control\MapTypeControlStyle::HORIZONTAL_BAR                   => 'Horizontale Bar'
];

/**
 * Legends
 */
$lang['general_legend']       = 'Allgemeine Einstellungen';
$lang['visualization_legend'] = 'Darstellung';
$lang['behavior_legend']      = 'Verhalten';
$lang['positioning_legend']   = 'Positionierung';
$lang['control_legend']       = 'Bedienelemente';
$lang['template_legend']      = 'Template';
$lang['publish_legend']       = 'Veröffentlichung';
$lang['language_legend']       = 'Sprache';

/**
 * Buttons
 */
$lang['new']    = ['Neue Google Map', 'Google Map erstellen'];
$lang['edit']   = ['Google Map bearbeiten', 'Google Map ID %s bearbeiten'];
$lang['copy']   = ['Google Map duplizieren', 'Google Map ID %s duplizieren'];
$lang['delete'] = ['Google Map löschen', 'Google Map ID %s löschen'];
$lang['toggle'] = ['Google Map veröffentlichen', 'Google Map ID %s veröffentlichen/verstecken'];
$lang['show']   = ['Google Map Details', 'Google Map-Details ID %s anzeigen'];
