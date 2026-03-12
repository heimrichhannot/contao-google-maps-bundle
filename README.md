# Contao Google Maps Bundle

![](https://img.shields.io/packagist/v/heimrichhannot/contao-google-maps-bundle.svg)
![](https://img.shields.io/packagist/dt/heimrichhannot/contao-google-maps-bundle.svg)

This bundle adds google maps integration to [Contao](https://contao.org/de/). It's based on [ivory/google-map](https://github.com/bresam/ivory-google-map).

## Features

- introduces a simple Contao backend entity to configure your Google Map and overlays (markers, info windows, ...)
- frontend module and content element
- insert tag and twig function
- easy contao command based migration tool for [delahaye/dlh_googlemaps](https://github.com/delahaye/dlh_googlemaps) (courtesy to delahaye!)
- responsive support (mobile first), provide responsive configurations that will update the map upon reaching the value (greater than breakpoint)
- support for [hofff/contao-consent-bridge](https://github.com/hofff/contao-consent-bridge)
- support for [Oveleon Cookiebar](https://packagist.org/packages/oveleon/contao-cookiebar)

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
3. Now you can integrate the map in your website using one of the following build-in ways:
    - Content element
    - Module
    - Insert tag (see below)
    - Twig function (see below)


### Twig

To render your map in a twig template, use `google_map` function:

```twig
{# The shortest way: #}
{{ google_map(2) }}

{# There are more possiblities: #}
{% set map = google_map(2)
    .addOverlays(overlays)          {# set overlays dynamically (pass as array|Collection<array|Model> #}
    .build()                        {# build the map, is needed before working with overlays/ markers #}
 %}
 
{# Create link to trigger a marker (typically open info window #}
<a href="#" onclick="{{ map.marker(overlays[1].id).trigger }}">
    Trigger marker id {{ map.marker(overlays[1].id).variable }}
</a>

{# Render the map #}
{{ map }}

{# Or render only html, css or js #}
{{ map.html }}
{{ map.css }}
{{ map.js }}
```



## Migrating from dlh_googlemaps

Although we cannot guarantee to fully migrate your existing dlh_googlemaps instances, you will nevertheless have a point to start from. Think of it as a 95% migration ;-)

Migrating is as simple as running `vendor/bin/contao-console huh:google-maps:migrate-dlh` from your contao root dir. Your dlh google maps are not changed by this process, only new instances in `tl_google_map` and `tl_google_map_overlay` are created out of the existing legacy data.

## Insert Tags

| Name            | Arguments                          | Example                |
|-----------------|------------------------------------|------------------------|
| google_map      | ID of the `tl_google_map` instance | {{google_map::1}}      |
| google_map_html | ID of the `tl_google_map` instance | {{google_map_html::1}} |
| google_map_css  | ID of the `tl_google_map` instance | {{google_map_css::1}}  |
| google_map_js   | ID of the `tl_google_map` instance | {{google_map_js::1}}   |

## Integrations

### Oveleon Cookiebar

This extension comes with a build-in cookie type for the [Oveleon Cookiebar](https://packagist.org/packages/oveleon/contao-cookiebar) 
that you can use to easily integrate the cookie bar with the google maps bundle. 
Just create a cookie type of type "Google Maps (Google Maps Bundle)" and you're done.

## TODO

- Overlay types:
    - polyline
    - circle
    - rectangle
    - ground_overlay
    
## Documentation

[Developer documentation](docs/developers.md)
