<?php

$lang = &$GLOBALS['TL_LANG']['tl_google_map'];

/**
 * Fields
 */
// general
$lang['title'][0] = 'Titel';
$lang['title'][1] = 'Geben Sie hier bitte den Titel ein.';

// visualization
$lang['styles'][0]   = 'Styles';
$lang['styles'][1]   = 'Geben Sie hier ein Array von JSON-Objekten für das Styles-Attribut ein.';
$lang['sizeMode'][0] = 'Größenmodus';
$lang['sizeMode'][1] = 'Wählen Sie hier aus, wie die Größe der Google Map bestimmt werden soll.';

// positioning
$lang['zoom'][0] = 'Zoom';
$lang['zoom'][1] = 'Geben Sie hier eine Zoomstufe ein (1: Welt, 5: Landmasse/Kontinent, 10: Stadt, 15: Straßen, 20: Gebäude).';

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
