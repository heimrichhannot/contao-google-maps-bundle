<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Heimrich & Hannot GmbH.
 *
 * @license LGPL-3.0-or-later
 */
$lang = &$GLOBALS['TL_LANG']['tl_google_map_overlay'];

/*
 * Fields
 */
$lang['title'] = ['Title', 'Please enter a title.'];
$lang['published'] = ['Publish Overlay', 'Make the Overlay publicly visible on the website.'];
$lang['start'] = ['Show from', 'Do not publish the Overlay on the website before this date.'];
$lang['stop'] = ['Show until', 'Unpublish the Overlay on the website after this date.'];
$lang['tstamp'] = ['Revision date', ''];
$lang['fillColor'][0] = 'Area color';
$lang['fillColor'][1] = 'Please insert the area color as a hexadecimal value.';
$lang['pathCoordinates'][0] = 'Vertices';
$lang['pathCoordinates'][1] = 'Insert the vertices with their specific coordinates.';
$lang['strokeWeight'][0] = 'Line weight';
$lang['strokeWeight'][1] = 'Insert the line weight (pixel).';
$lang['strokeColor'][0] = 'Line color';
$lang['strokeColor'][1] = 'Insert the line color as a hexadecimal value.';
$lang['strokeOpacity'][0] = 'Line opacity';
$lang['strokeOpacity'][1] = 'Insert the line opacity in the range from 0 to 1.';
$lang['fillOpacity'][0] = 'Area opacity';
$lang['fillOpacity'][1] = 'Insert the area opacity in the range from 0 to 1.';

/*
 * GeoJSON
 */
$lang['geojsonSource'][0] = 'Source';
$lang['geojsonSource'][1] = 'Choose where the GeoJSON data is loaded from.';
$lang['geojsonFile'][0] = 'GeoJSON file';
$lang['geojsonFile'][1] = 'Select a .geojson file from the file manager.';
$lang['geojsonUrl'][0] = 'GeoJSON URL';
$lang['geojsonUrl'][1] = 'Insert an absolute URL to a .geojson file.';
$lang['geojsonClickable'][0] = 'Clickable';
$lang['geojsonClickable'][1] = 'If enabled, clicking a feature opens an info window with its properties.';
$lang['geojsonFitBounds'][0] = 'Zoom to content';
$lang['geojsonFitBounds'][1] = 'Adjust the map viewport to the loaded data.';
$lang['geojsonStrokeColor'][0] = 'Line color';
$lang['geojsonStrokeColor'][1] = 'Fallback color for lines and outlines when a feature carries no color of its own.';
$lang['geojsonStrokeWeight'][0] = 'Line weight';
$lang['geojsonStrokeWeight'][1] = 'Fallback line weight in pixels.';
$lang['geojsonStrokeOpacity'][0] = 'Line opacity';
$lang['geojsonStrokeOpacity'][1] = 'Fallback line opacity in the range from 0 to 1.';
$lang['geojsonFillColor'][0] = 'Area color';
$lang['geojsonFillColor'][1] = 'Fallback fill color for areas.';
$lang['geojsonFillOpacity'][0] = 'Area opacity';
$lang['geojsonFillOpacity'][1] = 'Fallback fill opacity in the range from 0 to 1.';
$lang['geojsonStylePropertiesEnabled'][0] = 'Use properties from the file';
$lang['geojsonStylePropertiesEnabled'][1] = 'Evaluate the simplestyle properties (stroke, stroke-width, fill, icon, ...) of each feature. The settings above then act as fallbacks only.';

/*
 * Legends
 */
$lang['general_legend'] = 'General settings';
$lang['config_legend'] = 'Configuration';
$lang['style_legend'] = 'Appearance';
$lang['publish_legend'] = 'Publish settings';

/*
 * Buttons
 */
$lang['new'] = ['New Overlay', 'Overlay create'];
$lang['edit'] = ['Edit Overlay', 'Edit Overlay ID %s'];
$lang['copy'] = ['Duplicate Overlay', 'Duplicate Overlay ID %s'];
$lang['delete'] = ['Delete Overlay', 'Delete Overlay ID %s'];
$lang['toggle'] = ['Publish/unpublish Overlay', 'Publish/unpublish Overlay ID %s'];
$lang['show'] = ['Overlay details', 'Show the details of Overlay ID %s'];
