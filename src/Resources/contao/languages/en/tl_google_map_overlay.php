<?php

declare(strict_types=1);

/*
 * Copyright (c) 2024 Heimrich & Hannot GmbH
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
 * Legends
 */
$lang['general_legend'] = 'General settings';
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
