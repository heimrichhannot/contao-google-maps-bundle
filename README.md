# Contao Google Maps Bundle

![](https://img.shields.io/packagist/v/heimrichhannot/contao-google-maps-bundle.svg)
![](https://img.shields.io/packagist/dt/heimrichhannot/contao-google-maps-bundle.svg)

This bundle adds google maps integration to [Contao](https://contao.org/de/). It's based on [ivory/google-map](https://github.com/bresam/ivory-google-map) and [ivory/google-map-bundle](https://github.com/bresam/ivory-google-map-bundle).

## Features

- introduces a simple Contao backend entity to configure your Google Map and overlays (markers, info windows, ...)
- frontend module and content element
- insert tag and twig function
- easy contao command based migration tool for [delahaye/dlh_googlemaps](https://github.com/delahaye/dlh_googlemaps) (courtesy to delahaye!)
- responsive support (mobile first), provide responsive configurations that will update the map upon reaching the value (greater than breakpoint)
- support for [List bundle](https://github.com/heimrichhannot/contao-list-bundle) and [Reader bundle](https://github.com/heimrichhannot/contao-reader-bundle)
- support for [hofff/contao-consent-bridge](https://github.com/hofff/contao-consent-bridge)

## Setup and usage

### Setup

1. Install with contao manager or composer and update database afterwards

       composer require heimrichhannot/contao-google-maps-bundle

2. Optional: If you have already google maps created with [delahaye/dlh_googlemaps](https://github.com/delahaye/dlh_googlemaps) refer to the section "Migrating from dlh_googlemaps".
3. Set your Google API key (capable of Google Maps and Google Static Maps) if not already done in one of the following places (ascending priority):
    - global Contao settings (`tl_settings`)
    - page root (`tl_page`)
    - Google Maps config (`tl_google_map`)

### Usage

1. Create a Google Map using the corresponding menu entry in Contao on the left.
2. Optional: create markers with the created google map configuration (markers are child entities of a map)
4. Now you can integrate the map in your website using one of the following build-in ways:
    - Content element
    - Module
    - Insert tag (see below)
    - Twig function (see below)
    - render a list as map, list config element or reader config element

#### List and reader bundle

For both list and reader bundle a config element is provided to add maps to the items.

For lists you also get the option to render the complete list as map. 
In your list configuration, check the `renderItemsAsMap` option and do the additional configuration.
You can use or adapt the bundled default template: `list_google_maps_default.html.twig` and `list_item_google_maps_default.html.twig`.

Typical, your list items don't have the correct fields added and filled to be used as markers on a maps.
So you need to implement an event listener for the `GoogleMapsPrepareExternalItemEvent` 
and create or update an overlay object that can be displayed on the map.

```php
class GoogleMapsSubscriber implements EventSubscriberInterface {

    public static function getSubscribedEvents() {
        return [GoogleMapsPrepareExternalItemEvent::class => 'onGoogleMapsPrepareExternalItemEvent',];
    }

    public function onGoogleMapsPrepareExternalItemEvent(GoogleMapsPrepareExternalItemEvent $event): void
    {
        if (!$event->getConfigModel() instanceof ListConfigModel) {
            return;
        }
        
        $item = (object)$event->getItemData();
        
        $overlay = new OverlayModel();
        $overlay->title = $item->headline;
        $overlay->type = Overlay::TYPE_MARKER;
        if ($item->coordX && $item->coordX) {
            $overlay->positioningMode = Overlay::POSITIONING_MODE_COORDINATE;
            $overlay->positioningLat = trim($item->coordX);
            $overlay->positioningLng = trim($item->coordX);
        } elseif (!empty($item->address)) {
            $overlay->positioningMode = Overlay::POSITIONING_MODE_STATIC_ADDRESS;
            $overlay->positioningAddress = $item->address;
        } else {
            $event->setOverlayModel(null);
            return;
        }
        $overlay->markerType = Overlay::MARKER_TYPE_SIMPLE;
        $overlay->clickEvent = Overlay::CLICK_EVENT_INFO_WINDOW;
        $overlay->infoWindowText = '<p><b>'.$item->headline.'</b></p>';
        $overlay->published='1';
        $event->setOverlayModel($overlay);
    }
}
```





## Migrating from dlh_googlemaps

Although we cannot guarantee to fully migrate your existing dlh_googlemaps instances, you will nevertheless have a point to start from. Think of it as a 95% migration ;-)

Migrating is as simple as running `vendor/bin/contao-console huh:google-maps:migrate-dlh` from your contao root dir. Your dlh google maps are not changed by this process, only new instances in `tl_google_map` and `tl_google_map_overlay` are created out of the existing legacy data.

## Insert Tags

Name | Arguments | Example
---- | --------- | -------
google_map | ID of the `tl_google_map` instance | {{google_map::1}}
google_map_html | ID of the `tl_google_map` instance | {{google_map_html::1}}
google_map_css | ID of the `tl_google_map` instance | {{google_map_css::1}}
google_map_js | ID of the `tl_google_map` instance | {{google_map_js::1}}

## Twig functions

Name | Arguments | Example
---- | --------- | -------
google_map | ID of the `tl_google_map` instance | {{ google_map(1) }}
google_map_html | ID of the `tl_google_map` instance | {{ google_map_html(1) }}
google_map_css | ID of the `tl_google_map` instance | {{ google_map_css(1) }}
google_map_js | ID of the `tl_google_map` instance | {{ google_map_js(1) }}

## TODO

- Overlay types:
    - polyline
    - polygon
    - circle
    - rectangle
    - ground_overlay
    
## Documentation

[Developer documentation](docs/developers.md)
