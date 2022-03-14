# Changelog
All notable changes to this project will be documented in this file.

## [2.5.2] - 2022-03-14
- Fixed: initialize MapRendererListener in service container
- Fixed: twig dependency
- Fixed: warnings in php 8+
- Fixed: exception due missing patchwork/utf8 dependencies in contao 4.13 (replace calls with native php functions)

## [2.5.1] - 2022-02-01
- Fixed: readme

## [2.5.0] - 2022-02-01

- Added: support for [hofff/contao-consent-bridge](https://github.com/hofff/contao-consent-bridge), thanks @dmolineus!

## [2.4.0] - 2021-11-02
- Changed: some refactoring for contao 4.9
- Fixed: remaining template util usages
- Fixed: error when custom overlay icon anchor position not set
- Fixed: `map.stylesheet` listener was removed in 2.1.0
- Fixed: unnecessary container call

## [2.3.0] - 2021-10-29
- Changed: use twig support bundle to render map
- Fixed: template data not passed to maps renderer

## [2.2.2] - 2021-10-28
- Fixed: maps sizing mode css not working

## [2.2.1] - 2021-10-28
- Fixed: missing labels for user right legends

## [2.2.0] - 2021-10-28
- Changed: use GoogleMapsPrepareExternalItemEvent in list and reader config element ([#15])
- Deprecated: ListGoogleMapBeforeRenderEvent (use GoogleMapsPrepareExternalItemEvent instead) ([#15])
- Deprecated: ReaderGoogleMapBeforeRenderEvent (use GoogleMapsPrepareExternalItemEvent instead) ([#15])

## [2.1.1] - 2021-10-27
- Fixed: return values in GoogleMapsPrepareExternalItemEvent

## [2.1.0] - 2021-10-27
- Added: option to render [list](https://github.com/heimrichhannot/contao-list-bundle) as google map ([#14])
- Added: GoogleMapsPrepareExternalItemEvent to customize Marker data from external sources (currently only implemented for Lists rendering added in this version)
- Fixed: uneven script inclusion for consent bars
- Fixed: marker click event info window option not working with empty offset fields

> This version adds functionality previously provided by contao-google-maps-list-bundle. 
> Please uninstall contao-google-maps-list-bundle bundle in order to update to this version as they are not compatible.
> You need to update your list configs as the `HeimrichHannot\GoogleMapsListBundle\DefaultList` and `HeimrichHannot\GoogleMapsListBundle\DefaultItem` classes 
> are no longer necessary and exist.

## [2.0.0] - 2021-09-27

- Added: support for multiple maps on one page
- Added: dry-run option to migration command
- Added: content element and frontend module migration to migration command
- Added: generic BeforeRenderMapEvent
- Added: default values for aspect ratio (16:9)
- Added: kml support ([#7], thanks to [@rabaus])
- Added: user better error message if no api key is set ([#8])
- Added: php8 support ([#10], thanks to [@rabaus])
- [BREAKING] Removed: support for contao 4.4 and symfony 3 (thanks [@rabaus]!)
- [BREAKING] Changed: DlhMigrationModifyMapEvent legacy map type to object
- [BREAKING] Changed: DlhMigrationModifyOverlayEvent legacy map and legacyOverlay type to object
- [BREAKING] Changed: removed mapGoogleJs variable from map template
- [BREAKING] Changed: renamed Bundle class to HeimrichHannotGoogleMapsBundle to reflect coding standards
- Changed: migration command to not require installed delahaye/dlh_googlemaps module
- Changed: refactored GoogleMapListConfigElementType to implement ListConfigElementTypeInterface
- Changed: refactored GoogleMapReaderConfigElementType to implement ReaderConfigElementTypeInterface
- Changed: refactored HookListener to ReplaceInsertTagsListener
- Changed: enhanced warning MapManager::setCenter()
- Changed: removed library overrides
- Fixed: some deprecation notices
- Fixed: `composer.json` twig dependency (thanks to fritzmg)
- Fixed: `EventDispatcher` interface
- Fixed: insert tag inclusion
- Fixed: decoding of styles ([#11], thanks to [@rabaus])
- Added: symfony depencies

## [1.4.1] - 2020-04-15
- fixed MapManager return value declaration

## [1.4.0] - 2019-12-02
- also consider googlemaps_apiKey config key when checking for api key (MapManager)
- removed symfony framework dependency
- some code enhancements

## [1.3.1] - 2019-08-30

### Fixed
- `$markerVariableMapping` bug
- api key bug

## [1.3.0] - 2019-07-02

### Added
- `ElevationService` to get the elevation data for coordinates

## [1.2.5] - 2019-06-19

### Fixed
- `responsive` datacontainer check on `tl_google_map_overlay` (set closed on demand)

## [1.2.4] - 2019-06-18

### Added
- add `tl_list_config_element.php` de language file

## [1.2.3] - 2019-06-14

### Added
- markerVariableMapping in overlay manager

## [1.2.2] - 2019-06-12

### Added
- possibility to override overlays in `MapManager::prepareMap()` method

## [1.2.1] - 2019-06-12

### Removed
- `autoOpen` flag since it has never been needed with the egeloen implementation

## [1.2.0] - 2019-06-12

### Added
- new events: `DlhMigrationModifyMapEvent`, `DlhMigrationModifyOverlayEvent`

### Fixed
- infowindow issue for click event in dlh migration command

## [1.1.0] - 2019-06-11

### Added
- new command parameters:
    - clean-before-migration
    - skip-unsupported-field-warnings

### Removed
- published from google maps entity

### Changed
- dlh migrate command now also imports overlay publish states
- dlh migrate command now adds a higher attention to overlay coordinates rather than addresses

## [1.0.2] - 2019-06-11

### Changed
- dlh migrate command name to match other heimrichhannot bundles (see README)

## [1.0.1] - 2019-06-06

### Fixed
- Migration command now properly sets `tl_google_map.type` to `base`

## [1.0.0] - 2019-06-05

### Fixed
- hide controls if not activated (`zoomControl`, `mapTypeControl`, `scaleControl`, `streetViewControl`, `rotateControl`, `fullscreenControl`)
- Twig `Extension` renderJs function did render css (typo) 

### Added
- responsive map configuration

[#15]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/15
[#14]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/14
[#11]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/11
[#10]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/10
[#8]: https://github.com/heimrichhannot/contao-google-maps-bundle/issues/8
[#7]: https://github.com/heimrichhannot/contao-google-maps-bundle/pull/7

[@rabaus]: https://github.com/rabauss
